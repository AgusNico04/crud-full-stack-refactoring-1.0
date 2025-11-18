<?php
/**
*    File        : backend/controllers/studentsController.php
*    Project     : CRUD PHP
*    Author      : Tecnologías Informáticas B - Facultad de Ingeniería - UNMdP
*    License     : http://www.gnu.org/licenses/gpl.txt  GNU GPL 3.0
*    Date        : Mayo 2025
*    Status      : Prototype
*    Iteration   : 3.0 ( prototype )
*/

require_once("./repositories/students.php");

function handleGet($conn) 
{
    $input = json_decode(file_get_contents("php://input"), true);
    
    if (isset($input['id'])) 
    {
        $student = getStudentById($conn, $input['id']);
        echo json_encode($student);
    } 
    else
    {
        $students = getAllStudents($conn);
        echo json_encode($students);
    }
}

function handlePost($conn) 
{
    $input = json_decode(file_get_contents("php://input"), true);

    $result = createStudent($conn, $input['fullname'], $input['email'], $input['age']);

    // Si se insertó correctamente
    if ($result['inserted'] > 0) 
    {
        http_response_code(200); //Responde cod 200
        echo json_encode([
            "message" => "Estudiante agregado correctamente",
            "id" => $result['id']
        ]);
        return;
    }

    // Si hay error: email duplicado
    if (isset($result['error']) && $result['error'] === "duplicate_email") 
    {
        http_response_code(409); // error 409 = conflicto
        echo json_encode([
            "error" => "duplicate_email",//tipo de error, importante para levantar desde el frontend
            "message" => "El email ya esta registrado"
        ]);
        return;
    }

    // Cualquier otro error
    http_response_code(500);
    echo json_encode([
        "error" => "db_error",
        "message" => $result['message'] ?? "Error interno"
    ]);
}

function handlePut($conn) 
{
    $input = json_decode(file_get_contents("php://input"), true);

    $result = updateStudent($conn, $input['id'], $input['fullname'], $input['email'], $input['age']);
    if ($result['updated'] > 0) 
    {
        echo json_encode(["message" => "Actualizado correctamente"]);
    } 
    else 
    {
        http_response_code(500);
        echo json_encode(["error" => "No se pudo actualizar"]);
    }
}

function handleDelete($conn) 
{
    $input = json_decode(file_get_contents("php://input"), true);

    $result = deleteStudent($conn, $input['id']);
    if ($result['deleted'] > 0) 
    {
        echo json_encode(["message" => "Eliminado correctamente"]);
    } 
    else 
    {
        http_response_code(500);
        echo json_encode(["error" => "No se pudo eliminar"]);
    }
}
?>