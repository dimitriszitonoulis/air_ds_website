<?php
require_once __DIR__ . "/../../../config/config.php";
require_once BASE_PATH . "server/api/auth/validation_manager.php";
require_once BASE_PATH . "server/database/services/auth/db_insert_user.php";
require_once BASE_PATH . "config/messages.php";

// error_reporting(0);
// ini_set('display_errors', 0);

check_registration_errors();

function check_registration_errors() {
    header('Content-Type: application/json');

    $response_message = get_response_message([]);

    $conn = NULL;
    try {
        $conn = db_connect();
    } catch (PDOException $e) {
        $response = $response_message['failure']['connection'];
        http_response_code(500);
        echo json_encode($response);
        exit;
    }

    // get the data from js script
    $content = trim(file_get_contents("php://input")); // trim => remove white space from beggining and end
    $decoded_content = json_decode($content, true); // true is used to get associative array

    // what if the keys are not what I am expecting?
    // get the name of the fields that come from the client
    $field_names = array_keys($decoded_content);

    $response = null;
    // response = ["result" => boolean, "message" => string]
    $response = validate_fields($conn, $decoded_content, $field_names, false);
    // if a field is invalid
    if (!$response["result"]) {
        http_response_code(400);
        echo json_encode($response);
        exit;
    }

    $username = null;
    // check if the username is inside the content sent by the client
    if (array_key_exists("username", $decoded_content)) {
        // if it is, then its value has already been validated,
        // So no additional checks needed
        $username = $decoded_content["username"];            
    }

    // check if the username is taken
    try {
        $is_username_stored = db_is_username_stored($conn, $username);
    } catch (Exception $e) {
        // if exception do nothing
        $response = $response_message['failure']['nop'];
        http_response_code(500);
        echo json_encode($response);
        exit;  
    }

    // echo json_encode($is_username_stored);

    // FIXME returns true taken even though I enter a username no in the bd
    // if the username is not available
    if($is_username_stored) {
        $response = $response_message['register']['username_taken'];
        http_response_code($response_message['register']['username_taken']['http_response_code']);
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

    // if this point is reached all the fields are valid
    try {
        db_insert_user($conn, $decoded_content);
    } catch (Exception $e) {
        $response = $response_message['failure']['nop'];
        http_response_code(500);
        echo json_encode($response);
        exit;  
    }

    $response = $response_message['register']['success'];
    echo json_encode($response);
    exit;  
}
?>