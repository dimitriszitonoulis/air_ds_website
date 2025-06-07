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
 * Summary of get_trips
 * 
 * This function is an AJAX end point
 * 
 * It receives the username of a registered user.
 * It returns all the trips that user has made, newest trip 1st.
 * 
 * The username is received as a JSON like: 
 * {
 *  username: <username>,
 * }
 * 
 * 
 * It is responsible to receive the fetch request by the client (username).
 * Validate the input using the validation manager and validation functions.
 * Call the function that returns the trip about the user with the specified username.
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
 *  trips => array containing the trips that user has made
 * ]
 * 
 * the trips are an array like:
 * [
 *  departure_airport => <departure aiport code>,
 *  destination_airport => <destination airport code>,
 *  date => <departure date>,
 *  name => <name>,
 *  surname => <surname>,
 *  seat => <seat code>,
 *  price => <ticket price>
 * ] 
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
    $validator_parameters = [   // parameters needed by some validators that cannot be provided by the validator manager
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
       db_cancel_trip($conn,
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