<?php
require_once __DIR__ . "/../../../config/config.php";
require_once BASE_PATH . "server/database/db_utils/db_connect.php";
require_once BASE_PATH . "server/api/auth/field_validator_functions.php";

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
    // if for some reason no data comes from the client (individual array fields checked later)
    if(!isset($decoded_content) || empty($decoded_content))
       return ["result" => false, "message" => "Missing content"];

    foreach ($fields as $field => $isValid) {
        if (!isset($decoded_content[$field])) 
            return ["result" => false, "message" => "Missing field: $field"];
    }

    // if the validation is for register check the following fields
    if (!$is_login) {
        $fields["name"] = is_name_valid($decoded_content["name"]);
        $fields["surname"] = is_name_valid($decoded_content["surname"]);
        $fields["email"] = is_email_valid($conn, $decoded_content["email"]);
    }

    $fields["username"] = is_username_valid($conn, $decoded_content["username"], $is_login);
    $fields["password"] = is_password_valid($conn, $decoded_content["username"], $decoded_content["password"], $is_login);

    foreach ($fields as $field => $isValid) {
        if (!$isValid) return ["result" => false, "message" => "invalid $field"];
    }

    return ["result" => true,"message" => "All fields valid"];
}
?>