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
        echo json_encode(["error" => "Database connection failed"]);
        exit;
    }

    // get the data from js script
    $content = trim(file_get_contents("php://input")); // trim => remove white space from beggining and end
    $decoded_content = json_decode($content, true); // true is used to get associative array

    // Array with: field names => field validity
    $fields = [
        "name" => false,
        "surname" => false,
        "username" => false,
        "password" => false,
        "email" => false
    ];

    
    $validation = null;
    $validation = validate_fields($conn, $decoded_content, $fields);
  
    if (!$validation['result']) {
        header('Content-type: application/json');
        http_response_code(400);
        echo json_encode($validation);
        exit;
    }

/**
 * !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
 *                                  ATTENTION
 * 
 *  On client side the fetch script checks the following response
 *  If any response other than "user registered" is fetched,
 *  then the client cannot go from the register page to the login page
 * 
 * !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
 */

    // if this point is reached all the fields are valid
    db_insert_user($conn, $decoded_content);
    header('Content-Type: application/json');
    echo json_encode(["response" => "user registered"]);
    exit;  
}

function validate_fields($conn, $decoded_content, $fields) {
    // if for some reason no data comes from the client (individual array fields checked later)
    if(!isset($decoded_content) || empty($decoded_content))
       return ["result" => false, "message" => "Missing content"];

    foreach ($fields as $field => $isValid) {
        if (!isset($decoded_content[$field])) 
            return ["result" => false, "message" => "Missing field: $field"];
    }

    $fields["name"] = is_name_valid($decoded_content["name"]);
    $fields["surname"] = is_name_valid($decoded_content["surname"]);
    $fields["username"] = is_username_valid($conn, $decoded_content["username"]);
    $fields["password"] = is_password_valid($decoded_content["password"]);
    $fields["email"] = is_email_valid($conn, $decoded_content["email"]);

    foreach ($fields as $field => $isValid) {
        if (!$isValid) return ["result" => false, "message" => "invalid $field"];
    }

    return ["result" => true,"message" => "All fields valid"];
}
?>