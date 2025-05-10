<?php
require_once __DIR__ . "/../../../config/config.php";
require_once BASE_PATH . "server/api/auth/validation_manager.php";
require_once BASE_PATH . "server/database/services/auth/db_insert_user.php";
require_once BASE_PATH . "config/messages.php";

// error_reporting(0);
// ini_set('display_errors', 0);

check_registration_errors();

function check_registration_errors() {
    $conn = NULL;
    try {
        $conn = db_connect();
    } catch (PDOException $e) {
        header('Content-Type: application/json');
        http_response_code(500);
        echo json_encode(["result" => false, "message" => "Database connection failed"]);
        exit;
    }

    // get the data from js script
    $content = trim(file_get_contents("php://input")); // trim => remove white space from beggining and end
    $decoded_content = json_decode($content, true); // true is used to get associative array

    // what if the keys are not what I am expecting?
    // get the name of the fields that come from the client
    $field_names = array_keys($decoded_content);

    // array showing the validity of each field
    // like: field name => validity (boolean)
    // for now initialize all fields as false
    $fields =[];
    foreach($field_names as $name) {
        $fields[$name] = false;
    }

    // "name","surname","username","password","email"

    // response = ["result" => boolean, "message" => string]
    $response = null;
    $response = validate_fields($conn, $decoded_content, $fields, false);
  
    if (!$response["result"]) {
        header('Content-Type: application/json');
        // 400 should only be returned if the input is syntactically incorrect
        // it would not be right to send 400 if a username is taken
        http_response_code(400);
        echo json_encode($response);
        exit;
    }

/**
 * !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
 *                                  ATTENTION
 * 
 *  On client side the fetch script checks the response
 *  If response["result"] is not true,
 *  then the client cannot go from the register page to the login page
 * 
 * !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
 */
    $response_message = get_response_message([]);

    // no reason to get error messages for each field, send empty array
    // if this point is reached all the fields are valid
    try {
        db_insert_user($conn, $decoded_content);
    } catch (PDOException $e) {
        header('Content-Type: application/json');
        $response = $response_message['failure']['nop'];
        echo json_encode($response);
        exit;  
    }

    header('Content-Type: application/json');
    $response = $response_message['register']['success'];
    echo json_encode($response);
    exit;  
}
?>