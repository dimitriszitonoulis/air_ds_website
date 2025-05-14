<?php
require_once __DIR__ . "/../../../config/config.php";
require_once BASE_PATH . "server/api/validation_manager.php";
require_once BASE_PATH . "server/api/reservation/reservation_validators.php";
require_once BASE_PATH . "server/database/services/reservations/db_get_flight_dates.php";
require_once BASE_PATH . "config/messages.php";

// error_reporting(0);
// ini_set('display_errors', 0);

get_flight_dates();

function get_flight_dates() {
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
    $expected_fields = ["dep", "dest"];

    // modify response_message to also include messages for the expected fields
    $response_message = get_response_message($expected_fields);
    $validator_parameters = [   // parameters needed by some validators that cannot be provided by the validator manager
        'conn' => $conn,
        'response' => $response_message
    ];          
    $validators = get_validators_reservation();

    $response = null;
    // response array like:  ["result" => boolean, "message" => string, http_response_code => int]
    $response = validate_fields($conn, $decoded_content, $field_names, $expected_fields, $validator_parameters, $validators);

    // if a field is invalid
    if (!$response["result"]) {
        http_response_code($response['http_response_code']);
        echo json_encode($response);
        exit;
    }
   
    // if this point is reached all the fields are valid
    try {
        $flight_dates = db_get_flight_dates($conn, $decoded_content['dep'], $decoded_content['dest']);
    } catch (Exception $e) {
        $response = $response_message['failure']['nop'];
        http_response_code($response['http_response_code']);
        echo json_encode($response);
        exit;  
    }

    // $response = $response_message['register']['success'];
    $response['dates'] = $flight_dates;
    http_response_code($response['http_response_code']);
    echo json_encode($response);
    exit;  
}

/**
 * Summary of get_validators
 * An array containing key value pairs of authorization fields and their validator functions
 * @return array{
 *  dep: (callable(mixed ):bool), 
 *  dest: (callable(mixed ):bool), 
 * }
 */
function get_validators_reservation() {
    return [
        "dep" => function ($params)  {
            return is_airport_code_valid($params["conn"], $params["dep"], $params['response']); 
        },
        "dest" => function ($params)  {
            return is_airport_code_valid($params["conn"], $params["dest"], $params['response']); 
        }
    ];
}
?>