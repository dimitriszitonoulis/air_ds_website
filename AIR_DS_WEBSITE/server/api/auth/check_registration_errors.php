<?php
require_once __DIR__ . "/../../../config/config.php";
require_once BASE_PATH . "server/api/auth/validation_manager.php";
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

    // validation = ["result" => boolean, "message" => string]
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

    $validation["result"] = true;
    $validation["message"] = "user registered";
    header('Content-Type: application/json');
    echo json_encode($validation);
    exit;  
}
?>