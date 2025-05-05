<?php
require_once __DIR__ . "/../../../config/config.php";
require_once BASE_PATH . "server/database/db_utils/db_connect.php";
require_once BASE_PATH . "server/api/auth/field_validator_functions.php";
function validate_fields($conn, $decoded_content, $fields, $for_login=false) {
    // if for some reason no data comes from the client (individual array fields checked later)
    if(!isset($decoded_content) || empty($decoded_content))
       return ["result" => false, "message" => "Missing content"];

    foreach ($fields as $field => $isValid) {
        if (!isset($decoded_content[$field])) 
            return ["result" => false, "message" => "Missing field: $field"];
    }

    $fields["name"] = is_name_valid($decoded_content["name"]);
    $fields["surname"] = is_name_valid($decoded_content["surname"]);
    $fields["username"] = is_username_valid($conn, $decoded_content["username"]);
    $fields["password"] = is_password_valid($decoded_content["password"]);

    // TODO maybe move this to the validator_functions.php
    // if checking for login add another check for the password
    if ($for_login) {
        $fields["password"] = db_is_password_correct($conn, $fields["username"], $fields["password"]);
    }


    $fields["email"] = is_email_valid($conn, $decoded_content["email"]);

    foreach ($fields as $field => $isValid) {
        if (!$isValid) return ["result" => false, "message" => "invalid $field"];
    }

    return ["result" => true,"message" => "All fields valid"];
}
?>