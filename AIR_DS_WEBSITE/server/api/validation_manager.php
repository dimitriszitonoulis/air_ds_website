<?php
require_once __DIR__ . "/../../config/config.php";
require_once BASE_PATH . "server/database/db_utils/db_connect.php";
require_once BASE_PATH . "server/api/auth/auth_validators.php";
require_once BASE_PATH . "server/api/reservation/reservation_validators.php";

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

    $response = get_response_message($expected_fields);

    $is_payload_valid_response = is_payload_valid(  $decoded_content, $field_names, $expected_fields, $response);
    if (!$is_payload_valid_response["result"]) return $is_payload_valid_response;
    
    $are_fields_valid_response = apply_validators( $decoded_content, $field_names,  $response, $validators, $validator_params);
    if (!$are_fields_valid_response["result"]) return $are_fields_valid_response;

    // if everything is alright
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

/**
 * Summary of is_expected_fields
 * checks if the fields received are expected
 * @param mixed $names - an array containing the names of the fields
 * @return bool - true if the name of the fields are what is expected, otherwise false
 */
function is_expected_fields($names, $expected, $response) {
    // array_diff() returns an array that contains all the elements in the 1st array,
    // that do not exists inside the 2nd array.
    // So, if array_diff() returns an empty array, every element of the 1st array exists in the 2nd array

    // do the $names contain more field names than expected
    $is_more = empty(array_diff($names, $expected));
    if (!$is_more) return $response['failure']['more'];

    // do the $names contain less field names than expected
    $is_less = empty(array_diff($expected, $names));
    if (!$is_less) return $response['failure']['missing'];


    return $response['success'];
}

//TODO write better documentation
function apply_validators($decoded_content, $fields, $response, $validators, $params) {
    foreach ($fields as $field) {
        // add the value of the current field to the validator function parameters
        $params[$field] = $decoded_content[$field];
        $validator_response = $validators[$field]($params);

        // no need to unset the key it beacuse $params is pass by reference not value (does not add overhead)
        // In no way must username be unset because it is needed  for password

        // if a field is invalid do not continue with the other checks
        if (!$validator_response['result']) return $validator_response;
    }

    return $response["success"];
}
?>