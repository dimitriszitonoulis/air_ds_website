<?php
require_once __DIR__ . "/../../../config/config.php";
require_once BASE_PATH . "server/database/db_utils/db_connect.php";
require_once BASE_PATH . "server/api/validation_manager.php";
require_once BASE_PATH . "server/api/reservation/reservation_validators.php";
require_once BASE_PATH . "server/database/services/reservations/db_get_flight_dates.php";
require_once BASE_PATH . "config/messages.php";

get_flight_dates();

/**
 * Summary of get_flight_dates
 * 
 * This function is an AJAX end point
 * 
 * It receives the codes of 2 airports and returns the dates for the flights between them.
 * 
 * The airport codes are received as a JSON like: 
 * {
 *  dep: <departure airport code>,
 *  dest: <destination airport code
 * }
 * 
 * 
 * It is responsible to receive the fetch request be the client (departure and destination airport codes).
 * Validate the input using the valition manager and validation functions.
 * Call the function that returns the flight dates.
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
 *  dates => associative array:  [date => all the dates for the 2 aiports]
 * ]
 * 
 * @return never
 */
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