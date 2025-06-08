<?php
require_once __DIR__ . "/../../../config/config.php";
require_once BASE_PATH . "server/database/db_utils/db_connect.php";
require_once BASE_PATH . "server/api/validation_manager.php";
require_once BASE_PATH . "server/api/auth/login_user.php";
require_once BASE_PATH . "config/messages.php";
require_once BASE_PATH . "server/database/services/auth/db_are_credentials_correct.php";
require_once BASE_PATH . "server/api/auth/auth_validators.php";

check_login_errors();

function check_login_errors() {
    header('Content-type: application/json');

    $response_message = get_response_message([]);

    $conn = NULL;
    try {
        $conn = db_connect();
    } catch (PDOException $e) {
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
    $expected_fields = ["username", "password"];

    // modify response_message to also include messages for the expected fields
    // this MUST be done otherwise the validator functions will not have access to the correct response messages
    $response_message = get_response_message($expected_fields);
    // parameters needed by some validators that cannot be provided by the validator manager
    $validator_parameters = [   
        'conn' => $conn,
        'response' => $response_message
    ];          
    $validators = get_validators_login();


    // check the syntactical validity of the username and the password (if they actually belong to  an account will be checked later)
    $response = null;
    // response array like:  ["result" => boolean, "message" => string]
    $response = validate_fields($conn, $decoded_content, $field_names, $expected_fields, $validator_parameters, $validators);
  
    if (!$response["result"]) {
        http_response_code($response['http_response_code']);
        echo json_encode($response);
        exit;
    }

    // no need to check if they exist. if they didn't the validation above would have failed
    $username = $decoded_content["username"];
    $password = $decoded_content["password"];
    $is_credentials_correct = false;
    try {
        $is_credentials_correct = db_are_credentials_correct($conn, $username, $password);
    } catch (Exception $e) {
        $response = $response_message['failure']['invalid'];
        http_response_code($response['http_response_code']);
        echo json_encode($response);
        exit;
    }

    if (!$is_credentials_correct) {
        $response = $response_message['failure']['invalid'];
        http_response_code($response['http_response_code']);
        echo json_encode($response);
        exit;
    }

    // if this point is reached all the fields are valid
    login_user($username);

    if(!isset($_SESSION['userId'])) {
        $response = $response_message['failure']['nop'];
        http_response_code($response['http_response_code']);
        echo json_encode($response);
        exit;  
    }

    $response = $response_message['login']['success'];
    http_response_code($response['http_response_code']);
    echo json_encode($response);
    exit;  
}

// TODO move to auth validators
function get_validators_login() {
    return [
        "username" => function ($params) {
            return is_username_syntax_valid($params["username"], $params['response']);
        },
        "password" => function ($params) {
            return is_password_syntax_valid($params["password"], $params['response']); 
        }
    ];
}
?>