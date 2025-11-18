/**
*    File        : frontend/js/api/apiFactory.js
*    Project     : CRUD PHP
*    Author      : Tecnologías Informáticas B - Facultad de Ingeniería - UNMdP
*    License     : http://www.gnu.org/licenses/gpl.txt  GNU GPL 3.0
*    Date        : Mayo 2025
*    Status      : Prototype
*    Iteration   : 3.0 ( prototype )
*/

export function createAPI(moduleName, config = {}) 
{
    const API_URL = config.urlOverride ?? `../../backend/server.php?module=${moduleName}`;

    async function sendJSON(method, data) 
    {
        const res = await fetch(API_URL, {
            method,
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify(data)
        });

        let result;//variable para almacenar el resultado parseado

        try {
            result = await res.json();  // intenta parsear JSON
        } 
        catch (e) {
        // Si falla, entonces la respuesta NO era JSON
            throw {
                error: "invalid_json",
                message: "El servidor devolvió un formato no válido",
                raw: await res.text()
            };
        }

        if (!res.ok) {//si la respuesta no es OK (código 200-299)
            throw result; // Tiramos el JSON
        }

        return result;//devolvemos el resultado parseado
    }

    return {
        async fetchAll()
        {
            const res = await fetch(API_URL);
            if (!res.ok) throw new Error("No se pudieron obtener los datos");
            return await res.json();
        },
        async create(data)
        {
            return await sendJSON('POST', data);
        },
        async update(data)
        {
            return await sendJSON('PUT', data);
        },
        async remove(id)
        {
            return await sendJSON('DELETE', { id });
        }
    };
}
