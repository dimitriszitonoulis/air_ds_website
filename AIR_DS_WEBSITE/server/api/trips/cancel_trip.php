<?php
require_once __DIR__ . "/../../../config/config.php";
require_once BASE_PATH . "config/messages.php";
require_once BASE_PATH . "server/database/db_utils/db_connect.php";
require_once BASE_PATH . "server/api/validation_manager.php";
require_once BASE_PATH . "server/api/trips/trip_validators.php";
require_once BASE_PATH . "server/database/services/trips/db_cancel_trip.php";

cancel_trip();

//TODO fix documentation
/**
 * Summary of cancel_trip 
 * 
 * This function is an AJAX end point
 * 
 * It receives:
 * - the code of the deaprture airport
 * - the code of the destination airport
 * - the departure date
 * - the username of a registered user.
 * 
 * FUNCTIONALITY
 * It cancels the specified trip made by the registered user.
 * 
 * The information is received as a JSON like: 
 * {
 *  dep_code: <departure airport code>,
 *  dest_code: <destination airport code>,
 *  dep_date: <departure date>
 *  username: <username>,
 * }
 * 
 * DESCRIPTION: 
 * It is responsible to receive the fetch request by the client.
 * Validate the input using the validation manager and validation functions.
 * Call the function that cancels the trip.
 * Send a response back to the client.
 * 
 * If at any point something goes wrong an error message is sent
 * 
 * Type of responses:
 * The responses of this function are response_messages detailed in config/messages.php
 * 
 * @return never
 */
function cancel_trip (){
    header('Content-Type: application/json');

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
    $expected_fields = ["dep_code", "dest_code", "dep_date", "username"];

    // modify response_message to also include messages for the expected fields
    $response_message = get_response_message($expected_fields);

    // parameters needed by some validators that cannot be provided by the validator manager
    $validator_parameters = [   
        'conn' => $conn,
        'response' => $response_message
    ];

    $validators = get_validators();

    $response = null;
    // response array like:  ["result" => boolean, "message" => string, http_response_code => int]
    $response = validate_fields($conn, $decoded_content, $field_names, $expected_fields, $validator_parameters, $validators);

    // if a field is invalid
    if (!$response["result"]) {
        http_response_code($response['http_response_code']);
        echo json_encode($response);
        exit;
    }

    try {
       db_cancel_trip(  $conn,
                    $decoded_content["dep_code"],
                   $decoded_content["dest_code"],
                    $decoded_content["dep_date"],
                    $decoded_content["username"]);
    } catch (Exception $e) {
        $response = $response_message['failure']['nop'];
        http_response_code($response['http_response_code']);
        echo json_encode($response);
        exit;  
    }
    
    //if this point is reached then return success message
    // $response was assigned it's value by the validators, 
    // since this point is reached the validation succeded
    // and $response has the value of a success message
    http_response_code($response['http_response_code']);
    echo json_encode($response);
    exit;  
}
?>