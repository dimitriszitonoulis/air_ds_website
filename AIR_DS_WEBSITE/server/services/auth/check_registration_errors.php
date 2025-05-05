<?php
require_once __DIR__ . "/../../../config/config.php";
require_once BASE_PATH . "server/database/db_utils/db_connect.php";
require_once BASE_PATH . "server/services/auth/field_validator_functions.php";
require_once BASE_PATH . "server/database/services/auth/db_is_email_stored.php";
require_once BASE_PATH . "server/database/services/auth/db_insert_user.php";

// error_reporting(0);
// ini_set('display_errors', 0);


check_registration_errors();



function check_registration_errors() {
    $conn = NULL;
    try {
        $conn = db_connect();
    } catch (PDOException $e) {
        header('Content-type: application/json');
        http_response_code(500);
        echo json_encode(["error" => "Database connection falied"]);
        exit;
    }

    // get the data from js script
    $content = trim(file_get_contents("php://input")); // trim => remove white space from beggining and end
    $decoded_content = json_decode($content, true); // true is used to get associative array


    // if for some reason no data comes from the client (indiviadual array fields MUST be checked later)
    if(!isset($decoded_content) || empty($decoded_content)) {
        header('Content-type: application/json');
        http_response_code(400);
        echo json_encode(["error" => "Missing content"]);
        exit;
    }

    $fields = ["name", "surname", "username", "password", "email"];

    foreach ($fields as $field) {
        if (!isset($decoded_content[$field])) {
            header('Content-type: application/json');
            http_response_code(400);
            echo json_encode(["error" => "Missing field: $field"]);
            exit;
        }
    }

    $is_name = is_name_valid($decoded_content["name"]);
    $is_surname = is_name_valid($decoded_content["surname"]);
    $is_username = is_username_valid($conn, $decoded_content["username"]);
    $is_password = is_password_valid($decoded_content["password"]);
    $is_email = is_email_valid($conn, $decoded_content["email"]);


    if (!$is_name) {
        header('Content-Type: application/json');
        echo json_encode(["response" => "invalid name"]);
        exit;
    }
    
    if (!$is_surname) {
        header('Content-Type: application/json');
        echo json_encode(["response" => "invalid surname"]);
        exit;
    }
    if (!$is_username) {
        header('Content-Type: application/json');
        echo json_encode(["response" => "invalid username"]);
        exit;
    }
    if (!$is_password) {
        header('Content-Type: application/json');
        echo json_encode(["response" => "invalid password"]);
        exit;
    }
    if (!$is_email) {
        header('Content-Type: application/json');
        echo json_encode(["response" => "invalid email"]);
        exit;
    }

    // if every check is passed insertuser to database
    if ($is_name && $is_surname && $is_username && $is_password && $is_email) {
        db_insert_user($conn, $decoded_content);
        header('Content-Type: application/json');
        echo json_encode(["response" => "user registered"]);
        exit;
    }

    header('Content-Type: application/json');
    echo json_encode(["response" => "failed to register user"]);     
    exit;
}

?>