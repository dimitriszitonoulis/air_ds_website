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

    // if for some reason no data comes from the client (individual array fields checked later)
    if(!isset($decoded_content) || empty($decoded_content))
        return $response_message['failure']["missing"];

    foreach ($fields as $field => $isValid) {
        if (!isset($decoded_content[$field]))
            return $response_message[$field]['missing'];
    }
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
    
    if (!$is_login) return $response_message["register"]["success"];
    else return $response_message["login"]["success"];
}
?>