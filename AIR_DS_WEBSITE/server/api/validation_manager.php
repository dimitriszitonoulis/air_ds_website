<?php
require_once __DIR__ . "/../../config/config.php";
require_once BASE_PATH . "server/database/db_utils/db_connect.php";
require_once BASE_PATH . "server/api/auth/field_validator_functions.php";
require_once BASE_PATH . "config/messages.php";

// TODO change documentation
/**
 * Summary of validate_fields
 * @param mixed $conn - the connection to the database
 * @param array{string: string} $decoded_content - the data received from the client through AJAX:
 *                                                  - keys: fields names
 *                                                  - values: field values
 * 
 * @param array{string: boolean} $fields  - An associative array where:
 *                                  - keys: the names of the fields
 *                                  - values: a boolean, true if the field is valid, else false 
 * @param boolean $is_login - true if the validation is done for login, false if it is done for register
 * @return array{message: string, result: bool}
 */
function validate_fields($conn, $decoded_content, $field_names, $expected_fields, $validator_params, $validators) {

    // TODO must be done using $expected_fields
    $response = get_response_message($expected_fields);

    // return ['result' => false, "message" => $response, "http_response_code" => 200];


    // return ['result' => false, "message" => $response_message, "http_response_code" => 200];

    $is_payload_valid_response = is_payload_valid(  $decoded_content, $field_names, $expected_fields, $response);
    if (!$is_payload_valid_response["result"]) return $is_payload_valid_response;
    
    $are_fields_valid_response = apply_validators( $decoded_content, $field_names,  $response, $validators, $validator_params);
    if (!$are_fields_valid_response["result"]) return $are_fields_valid_response;

    // if everything is alright
    return $response['success'];
}

/**
 * Summary of is_expected_fields
 * checks if the fields received are expected
 * @param mixed $names - an array containing the names of the fields
 * @return bool - true if the name of the fields are what is expected, otherwise false
 */
function is_expected_fields($names, $expected, $response) {
     // array_diff() returns an array that contains all the elements in $names,
    // that do not exists inside expected names.
    // So, if array_diff() returns an empty array, every element of $names is also an element of $expected_names
    $is_expected = empty(array_diff($names, $expected));

    // What if instead of missing the user supplies more fields?
    // TODO maybe add new message for this case
    if (!$is_expected) return $response['failure']['missing'];
    return $response['success'];
}

/**
 * Summary of is_payload_valid
 * 
 * Checks if what is received from AJAX request is valid (not empty and the expected fields arrived)
 * 
 * @param mixed $decoded_content - the array received from the AJAX request
 * @param mixed $fields - Associative array where: keys = the name of the fields, values = the validity of the field
 * @param mixed $field_names - Array containing the names of the fields
 * @param mixed $response - An array containing response messages
 * @return mixed - Associative array containing:
 *                   - the result of the check (boolean)
 *                   - a message explaining the result of the check
 */
function is_payload_valid($decoded_content, $field_names, $expected, $response) {

    $is_expected_fields_response = is_expected_fields($field_names, $expected, $response);
    
    // TODO maybe call in the validate_fields instead of here
    // if a user messes with the js on client side unpredictable key names may comme
    // if so ignore them
    if (!$is_expected_fields_response["result"])
        return $is_expected_fields_response;

    // if for some reason no data comes from the client (individual array fields checked later)
    if (!isset($decoded_content) || empty($decoded_content))
        return $response['failure']["missing"];

    // check that each field sent by the client is set
    foreach ($field_names as $field) {
        if (!isset($decoded_content[$field]))
            return $response[$field]['missing'];
    }
    return $response["success"];
}

//TODO write better documentation
function apply_validators($decoded_content, $fields, $response, $validators, $params) {

    // $validators = get_validators();

    // TODO add these to every script that call the validator
    // some extra parameters needed by some of the validators
    // $params = [
    //     "conn" => $conn,
    //     "response_message" => $response_message  
    // ];


    $test_array = [];

    foreach ($fields as $field) {
        // add the value of the current field to the validator function parameters
        $params[$field] = $decoded_content[$field];
        $validator_response = $validators[$field]($params);
        // no need to unset the key it beacuse $params is pass by reference not value (does not add overhead)
        // In no way must username be unset because it is needed  for password

        // if a field is invalid do not continue with the other checks
        if (!$validator_response['result']) return $validator_response;
    }
 
    // return ['result' => false, "message" => $test_array, "http_response_code" => 200];


    return $response["success"];
}
?>