<?php
require_once __DIR__ . "/../../../config/config.php";
require_once BASE_PATH . "config/messages.php";
require_once BASE_PATH . "server/database/db_utils/db_connect.php";
require_once BASE_PATH . "server/api/validation_manager.php";
require_once BASE_PATH . "server/api/reservation/reservation_validators.php";
require_once BASE_PATH . "server/database/services/reservations/db_get_flight_info.php";

get_airport_info();


/**
 * Summary of get_airport_info
 * 
 * This function is an AJAX end point
 * 
 * It receives the codes of 2 airports.
 * It returns details about them.
 * 
 * The airport codes and the date are received as a JSON like: 
 * {
 *  dep_code: <departure airport code>,
 *  dest_code: <destination airport code>,
 * }
 * 
 * 
 * It is responsible to receive the fetch request by the client (departure code, destination code).
 * Validate the input using the validation manager and validation functions.
 * Call the function that returns the information about those airports.
 * Send the data back to the client.
 * 
 * If at any point something goes wrong an error message is sent
 * 
 * Type of responses:
 * The responses of this function are response_messages detailed in config/messages.php
 * 
 * The only exception to this rule is the success message if everything goes well.
 * This message is an array like:
 * [
 *  result => boolean,
 *  message => string
 *  http_response_code => int
 *  airport_info => array containing the information about the specified airports
 * ]
 * 
 * @return never
 */
function get_airport_info (){
    header('Content-Type: application/json');

// TODO update what type of information is received about the 2 airports

    $response_message = get_response_message([]);

    $conn = NULL;
    try {
        $conn = db_connect();
    } catch (PDOException $e) {
        $response = $response_message['failure']['connection'];
        http_response_code($response['http_response_code']);
        echo json_encode($response);
        exit;
    }

    // get the data from js script
    $content = trim(file_get_contents("php://input")); // trim => remove white space from beggining and end
    $decoded_content = json_decode($content, true); // true is used to get associative array

    // what if the keys are not what I am expecting?
    // get the name of the fields that come from the client
    $field_names = array_keys($decoded_content);
    $expected_fields = ["dep_code", "dest_code"];   

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

    $airport_information = [];
    // if this point is reached all the fields are valid
    try {
        $airport_information = db_get_airport_information($conn, $decoded_content['dep_code'], $decoded_content['dest_code']);
    } catch (Exception $e) {
        $response = $response_message['failure']['nop'];
        http_response_code($response['http_response_code']);
        echo json_encode($response);
        exit;  
    }

    $response['airport_info'] = $airport_information;
    http_response_code($response['http_response_code']);
    echo json_encode($response);
    exit;  
}
?>