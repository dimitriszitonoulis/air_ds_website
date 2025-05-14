<?php
require_once __DIR__ . "/../../../config/config.php";
require_once BASE_PATH . "server/database/services/db_is_field_stored.php";

function is_name_valid ($name, $response) {
    if(!isset($name) || empty($name)) return $response['name']['missing'];
    if(!is_only_letters($name)) return $response['name']['invalid'];
    return $response['success'];
}

/**
 * Summary of is_username_valid_register
 * 
 * Checks if the supplied username is valid for registration
 * A username is valid for registration if:
 * - It has correct syntax
 * - It does not already exist in the database (there is no other user with that username)
 * 
 * @param mixed $conn - the connection to the db
 * @param mixed $username
 * @param mixed $response - an array containing response messages
 */
function is_username_valid_register($conn, $username, $response) {
    $is_syntax = is_username_syntax_valid($username, $response);
    if (!$is_syntax['result']) return $is_syntax;

    $is_stored = is_username_stored($conn, $username, $response);
    // if the username is stored return error message
    if($is_stored['result']) return $response['register']['username_taken'];

    return $response['success'];
}

/**
 * Summary of is_username_valid_register
 * 
 * Checks if the supplied username has valid syntax 
 * A username is has valid syntax if:
 * - It is set and it is not null
 * - It only contains alphanumeric characters
 * 
 * @param mixed $username
 * @param mixed $response - an array containing response messages
 */
function is_username_syntax_valid($username, $response) {
    if (!isset($username) || empty($username)) return $response['username']['missing'];
    if (!is_alphanumeric($username)) return $response['username']['invalid'];
    return $response['success'];
}

function is_username_stored($conn, $username, $response) {
    $is_stored = false;
    // check if the username is taken
    try {
        $is_stored = db_is_username_stored($conn, $username);
    } catch (Exception $e) {
        return $response['failure']['nop'];
    }

    // is there another account with that username?
    if (!$is_stored) return $response['failure']['not_found'];

    return $response['success'];
}


function is_password_syntax_valid ($password, $response) {
    if (!isset($password) || empty($password)) return $response['password']['missing'];
    if (!contains_number($password)) return $response['password']['invalid'];
    if (strlen($password) < 4 || strlen($password) > 10) return $response['password']['invalid'];
    return $response['success'];
}

function is_email_valid ($conn, $email, $response) {
    if (!isset($email) || empty($email)) return $response['email']['missing'];
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) return $response['email']['invalid'];

     $is_stored = false;
    // check if the username is taken
    try {
        $is_stored= db_is_email_stored($conn, $email);
    } catch (Exception $e) {
        return $response['failure']['nop'];

    }
    // is there another account with that email?
    if ($is_stored) return $response['register']['email_taken'];

    return $response['success'];
}

function is_only_letters ($txt) {
    $regex = "/^[a-zA-Z]+$/";
    return preg_match($regex, $txt) === 1;
}

function is_alphanumeric ($txt) {
    $regex = "/^[a-zA-Z0-9]+$/";
    return preg_match($regex, $txt) === 1;
}

function contains_number ($txt) {
    $regex = "/\d/";
    return preg_match($regex, $txt) === 1;
}
?>