<?php
require_once __DIR__ . "/../../../config/config.php";
require_once BASE_PATH . "server/api/validation_manager.php";
require_once BASE_PATH . "server/database/services/auth/db_insert_user.php";
require_once BASE_PATH . "server/api/auth/auth_validators.php";
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
    $expected_fields = ["name", "surname", "username", "password", "email"];

    // modify response_message to also include messages for the expected fields
    $response_message = get_response_message($expected_fields);
    $validator_parameters = [   // parameters needed by some validators that cannot be provided by the validator manager
        'conn' => $conn,
        'response' => $response_message
    ];          
    $validators = get_validators_register();

    $response = null;
    // response array like:  ["result" => boolean, "message" => string]
    $response = validate_fields($conn, $decoded_content, $field_names, $expected_fields, $validator_parameters, $validators);

    // if a field is invalid
    if (!$response["result"]) {
        http_response_code($response['http_response_code']);
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
        http_response_code($response['http_response_code']);
        echo json_encode($response);
        exit;  
    }

    $response = $response_message['register']['success'];
    http_response_code($response['http_response_code']);
    echo json_encode($response);
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
function get_validators_register() {
    return [
        "name" => function ($params) {
            return is_name_valid($params["name"], $params['response']);
        },
        "surname" => function ($params) { 
            return is_name_valid($params["surname"], $params['response']); 
        },
        "username" => function ($params) {
            return is_username_valid_register($params["conn"], $params["username"], $params['response']);
        },
        "password" => function ($params) {
            return is_password_syntax_valid($params["password"], $params['response']); 
        },
        "email" => function ($params)  {
            return is_email_valid($params["conn"], $params["email"], $params['response']); 
        }
    ];
}
?>