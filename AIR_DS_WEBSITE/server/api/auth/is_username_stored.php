<?php 
require_once __DIR__ . "/../../../config/config.php";
require_once BASE_PATH . "server/database/db_utils/db_connect.php";
require_once BASE_PATH . "server/api/auth/validation_manager.php";
require_once BASE_PATH . "server/database/services/auth/db_is_field_stored.php";
require_once BASE_PATH . "server/api/auth/field_validator_functions.php";
require_once BASE_PATH . "config/messages.php";

find_username();

function find_username() {
    header('Content-type: application/json');
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
    $validators = get_validators_username();

    $response = null;
    // response array like:  ["result" => boolean, "message" => string]
    $response = validate_fields($conn, $decoded_content, $field_names, $expected_fields, $validator_parameters, $validators);

    // if a field is invalid
    if (!$response["result"]) {
        http_response_code($response['http_response_code']);
        echo json_encode($response);
        exit;
    }
    
    // the username has passed syntactical validations
    $username = $decoded_content['username'];
    
    $is_stored_response = is_username_stored($conn, $username, $response_message);

    if(!$is_stored_response['result']) {
        http_response_code($response['http_response_code']);
        echo json_encode($is_stored_response);
        exit;
    }
    
    // TODO maybe add new response message to messages.php
    http_response_code(200);    
    echo json_encode(["result" => true, "message" => "username is stored"]);
    exit;
}


/**
 * Summary of get_validators
 * An array containing key value pairs of authorization fields and their validator functions
 * @return array{
 *  email: (callable(mixed ):bool), 
 *  name: (callable(mixed ):bool), 
 *  password: (callable(mixed ):bool), 
 *  surname: (callable(mixed ):bool), 
 *  username: (callable(mixed ):bool)
 * }
 */
function get_validators_username() {
    return [
        "username" => function ($params) {
             return is_username_syntax_valid ($params['username'], $params['response']);
        }
    ];
}


?>
