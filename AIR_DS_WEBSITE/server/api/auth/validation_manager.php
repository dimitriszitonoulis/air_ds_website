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
    $is_payload_valid_response = is_payload_valid( $decoded_content, $fields, $field_names, $response_message);
    if ($is_payload_valid_response["result"]) return $is_payload_valid_response;


    // TODO CALL FUNTION
    $are_fields_checked_response = check_field_validity($conn, $decoded_content, $fields,  $response_message);
    if (!$are_fields_checked_response["result"]) return $are_fields_checked_response["result"];

    // final response message if everything is alright
    //TODO maybe send generic positive response and be more specific after user register/login
    if (!$is_login) return $response_message["register"]["success"];
    else return $response_message["login"]["success"];
}

// checks if the fields received are expected
function is_expected_fields_($names) {
    $expected_names = ["name", "surname", "username", "password", "email"];

    // array_diff() returns an array that contains all the elements in $names,
    // that do not exists inside expected names.
    // So, if array_diff() returns an empty array, every element of $names is also an element of $expected_names
    return empty(array_diff($names, $expected_names));
}


//TODO write better documentation
// checks if what is received from AJAX request is valid ()
function is_payload_valid($decoded_content, $fields, $field_names, $response_message) {

    $is_expected_fields = is_expected_fields_($field_names);
    
    // if a user messes with the js on client side unpredictable key names may comme
    // if so ignore them
    // TODO maybe add different error message (this one is valid as well)  
    if (!$is_expected_fields)
        return $response_message['failure']["missing"];


    // if for some reason no data comes from the client (individual array fields checked later)
    if (!isset($decoded_content) || empty($decoded_content))
        return $response_message['failure']["missing"];

    foreach ($fields as $field => $isValid) {
        if (!isset($decoded_content[$field]))
            return $response_message[$field]['missing'];
    }

    return $response_message["success"];
}

//TODO write better documentation
// TODO rename
function check_field_validity($conn, $decoded_content, $fields, $response_message, $is_login=false) {
    // if the validation is for register check the following fields
    if (!$is_login) {
        $fields["name"] = is_name_valid($decoded_content["name"]);
        $fields["surname"] = is_name_valid($decoded_content["surname"]);
        $fields["email"] = is_email_valid($conn, $decoded_content["email"]);
    }

    $fields["username"] = is_username_valid($conn, $decoded_content["username"], $is_login);
    $fields["password"] = is_password_valid($conn, $decoded_content["username"], $decoded_content["password"], $is_login);


    // return ["result" => false, $response_message["email"]];

    foreach ($fields as $field => $isValid) {
        // for register give feedback, which field is problematic
        if (!$isValid && !$is_login) 
            return $response_message[$field]['failure'];
        // for login, give no feedback only say that the credentials are wrong
        // (for security reasons to not reveal the users or their passwords) 
        if(!$isValid && $is_login) 
            return $response_message['login']['failure']['invalid'];
    }

    // FIXME if all good it send generic positive response
    return $response_message["success"];
}

?>