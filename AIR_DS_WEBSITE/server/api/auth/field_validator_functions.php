<?php
require_once __DIR__ . "/../../../config/config.php";
require_once BASE_PATH . "server/database/services/auth/db_is_field_stored.php";


/**
 * Summary of get_validators
 * An array containing key value pairs of authorization fields and their validator functions
 * @return array{
 *  email: (callable(mixed ):bool), 
 *  name: (callable(mixed ):bool), 
 *  password: (callable(mixed ):bool), 
 *  surname: (callable(mixed ):bool), 
 *  username: (callable(mixed ):bool)
 * }
 */
function get_validators() {
    return [
        "name" => function ($params) { 
            return is_name_valid($params["name"]);
        },
        "surname" => function ($params) { 
            return is_name_valid($params["surname"]); 
        },
        "username" => function ($params) {
            return is_username_syntax_valid($params["username"]);
        },
        "password" => function ($params) {
            return is_password_syntax_valid($params["password"]); 
        },
        "email" => function ($params)  {
            return is_email_valid($params["conn"], $params["email"]); 
        }
    ];
}

function is_name_valid ($name) {
    if(!isset($name) || empty($name)) return false;
    if(!is_only_letters($name)) return false;
    return true;
}

function is_username_syntax_valid($username) {
    if (!isset($username) || empty($username)) return false;
    if (!is_alphanumeric($username)) return false;
    return true;
}

function is_password_syntax_valid ($password) {
    if (!isset($password) || empty($password)) return false;
    if (!contains_number($password)) return false;
    if (strlen($password) < 4 || strlen($password) > 10)  return false;
    return true;
}

function is_email_valid ($conn, $email) {
    if (!isset($email) || empty($email)) return false;
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) return false;
    if (db_is_email_stored($conn, $email)) return false;
    return true;
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