<?php
require_once __DIR__ . "/../../../config/config.php";
require_once BASE_PATH . "server/database/services/auth/db_is_username_stored.php";

function is_name_valid ($name) {

    if(!isset($name) || empty($name))
        return false;

    // if(!is_only_letters(strval($name)))

    if(!is_only_letters($name)) 
        return false;

    return true;
}

function is_username_valid ($conn, $username) {
    if (!isset($username) || empty($username)) return false;
    if (!is_alphanumeric($username)) return false;

    // if there are no usernames like $username in the db then an empty array is returned
    $result = db_is_username_stored($conn, $username);

    if (count($result) !== 0) return false;

    return true;
}

function is_password_valid ($password) {
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