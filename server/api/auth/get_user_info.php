<?php
require_once __DIR__ . "/../../../config/config.php";
require_once BASE_PATH . "config/messages.php";
require_once BASE_PATH . "server/database/db_utils/db_connect.php";
require_once BASE_PATH . "server/api/validation_manager.php";
require_once BASE_PATH . "server/api/auth/auth_validators.php";
require_once BASE_PATH . "server/database/services/auth/db_get_full_name.php";

// AJAX ENDPOINT

get_full_name();

/**
 * Summary of get_full_name
 * 
 * This function is an AJAX endpoint
 * 
 * It receives a user's username and returns their name and surname.
 * The username is a JSON like: {username: <username_value>}
 * 
 * It is responsible to receive the fetch request be the client (username).
 * Validate the input using the valition manager and validation functions.
 * Call the function that returns the user's full name.
 * Send the data back to the client.
 * 
 * If at any point something goes wrong a return message is sent
 * 
 * Type of responses:
 * Most of the responses of this function are response_messages detailed in config/messages.php
 * 
 * The only exception to this rule is the success message if everything goes well.
 * This message is an array like:
 * [
 *  result => boolean,
 *  message => string
 *  http_response_code => int
 *  name => string
 *  surname => string
 * ] 
 *  
 * @return never
 */
function get_full_name() {
    header('Content-Type: application/json');

    $response_message = get_response_message([]);


    $conn = null;
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
    $expected_fields = ["username"];

    // modify response_message to also include messages for the expected fields
    $response_message = get_response_message($expected_fields);
    $validator_parameters = [   // parameters needed by some validators that cannot be provided by the validator manager
        'conn' => $conn,
        'response' => $response_message
    ];          
    $validators = get_syntax_validators();

    $response = null;
    // response array like:  ["result" => boolean, "message" => string]
    $response = validate_fields($conn, $decoded_content, $field_names, $expected_fields, $validator_parameters, $validators);

    // if a field is invalid
    if (!$response["result"]) {
        http_response_code($response['http_response_code']);
        echo json_encode($response);
        exit;
    }

    // query the db to get the name and surname for the user with the given username
    $full_name = null;
    try {
        // array containing name and surname
        $full_name = db_get_full_name($conn, $decoded_content['username']);

    } catch (Exception $e){
        $response = $response_message['failure']['nop'];
        http_response_code($response['http_response_code']);
        echo json_encode($response);
        exit;  
    }

    // TODO maybe instead of splittin just include full name array to response
    $response['name'] = $full_name['name'];
    $response['surname'] = $full_name['surname'];
    http_response_code($response['http_response_code']);
    echo json_encode($response);
    exit;
}
?>