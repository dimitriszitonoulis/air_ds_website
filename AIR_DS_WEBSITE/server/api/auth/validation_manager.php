<?php
require_once __DIR__ . "/../../../config/config.php";
require_once BASE_PATH . "server/database/db_utils/db_connect.php";
require_once BASE_PATH . "server/api/auth/field_validator_functions.php";
require_once BASE_PATH . "config/messages.php";

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
function validate_fields($conn, $decoded_content, $fields, $is_login=false) {
    // fields is an array with key values like: field name => boolean 
    // The name of the fields must be passed to the function that gets the response messages for those fields
    $field_names = array_keys($fields);
    $response_message = get_response_message($field_names);

    // FIXME if all good it send generic positive response
    $is_payload_valid_response = is_payload_valid( $decoded_content, $field_names, $response_message);
    if (!$is_payload_valid_response["result"]) return $is_payload_valid_response;

    $are_fields_valid_response = apply_validators($conn, $decoded_content, $fields,  $response_message, $is_login);
    if (!$are_fields_valid_response["result"]) return $are_fields_valid_response;

    // final response message if everything is alright
    //TODO maybe send generic positive response and be more specific after user register/login
    if (!$is_login) return $response_message["register"]["success"];
    else return $response_message["login"]["success"];
}

/**
 * Summary of is_expected_fields
 * checks if the fields received are expected
 * @param mixed $names - an array containing the names of the fields
 * @return bool - true if the name of the fields are what is expected, otherwise false
 */
function is_expected_fields($names) {
    $expected_names = ["name", "surname", "username", "password", "email"];

    // array_diff() returns an array that contains all the elements in $names,
    // that do not exists inside expected names.
    // So, if array_diff() returns an empty array, every element of $names is also an element of $expected_names
    return empty(array_diff($names, $expected_names));
}

/**
 * Summary of is_payload_valid
 * 
 * Checks if what is received from AJAX request is valid (not empty and the expected fields arrived)
 * 
 * @param mixed $decoded_content - the array received from the AJAX request
 * @param mixed $fields - Associative array where: keys = the name of the fields, values = the validity of the field
 * @param mixed $field_names - Array containing the names of the fields
 * @param mixed $response_message - An array containing response messages
 * @return mixed - Associative array containing:
 *                   - the result of the check (boolean)
 *                   - a message explaining the result of the check
 */
function is_payload_valid($decoded_content, $field_names, $response_message) {

    $is_expected_fields = is_expected_fields($field_names);
    
    // if a user messes with the js on client side unpredictable key names may comme
    // if so ignore them
    // TODO maybe add different error message (this one is valid as well)  
    if (!$is_expected_fields)
        return $response_message['failure']["missing"];

    // if for some reason no data comes from the client (individual array fields checked later)
    if (!isset($decoded_content) || empty($decoded_content))
        return $response_message['failure']["missing"];

    // check that each field sent by the client is set
    foreach ($field_names as $field) {
        if (!isset($decoded_content[$field]))
            return $response_message[$field]['missing'];
    }
    return $response_message["success"];
}

//TODO write better documentation
// TODO rename

function apply_validators($conn, $decoded_content, $fields, $response_message, $is_login=false) {

    $validators = get_validators();

    // if the password is set this value is needed
    $username = null;
    if (array_key_exists("username", $decoded_content)) {
        if (isset($decoded_content["username"])) {
            $username = $decoded_content["username"];
        }
    }

    // TODO maybe move them in a function in field_validator_functions
    // some extra parameters needed by some of the validators
    $params = [
        "conn" => $conn,
        "username" => $username,
        "is_login" => $is_login
    ];
    
    // TODO maybe just loop through the field names
    //loop through all the fields, 
    // call their validators,
    // set the value of associative array $fields for the current field
    foreach ($fields as $current => $is_valid) {
        // add the value of thhe current field to the validator function parameters
        $params[$current] =  $decoded_content[$current];
        $fields[$current] = $validators[$current]($params);

        // no need to unset the key it beacuse $params is pass by reference not value
        // does not add overhead
        // In no way must username be unset because it is needed  for password

        // if a field is unvalid do not continue with the other checks
        if (!$fields[$current]) {
            if (!$is_login) return $response_message[$current]["failure"];
            return $response_message["login"]["failure"]["invalid"];
        }
    }

    foreach ($fields as $field => $is_valid) {
        if(!$is_valid) {
            // for register give feedback, which field is problematic
            if (!$is_login) return $response_message[$field]['failure'];
            
            // for login, give no feedback only say that the credentials are wrong
            // (for security reasons to not reveal the users or their passwords) 
            if ($is_login) return $response_message['login']['failure']['invalid'];
        }
    }

    // FIXME if all good it send generic positive response
    return $response_message["success"];
}



?>