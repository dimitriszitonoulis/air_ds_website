<?php
require_once __DIR__ . "/../../../config/config.php";
require_once BASE_PATH . "server/database/services/auth/db_is_field_stored.php";
require_once BASE_PATH . "server/database/services/auth/db_is_password_correct.php";
// require_once BASE_PATH . "server/database/services/auth/db_is_email_stored.php";

function is_name_valid ($name) {
    if(!isset($name) || empty($name)) return false;
    if(!is_only_letters($name)) return false;
    return true;
}

function is_username_valid ($conn, $username, $is_login) {
    if (!isset($username) || empty($username)) return false;
    if (!is_alphanumeric($username)) return false;

    $is_stored = db_is_username_stored($conn, $username);
    /**
     * if the check is for login:
     *      if the username is stored return true 
     *      if the username is not stored return false
     * if the check is for register:
     *      if the username is stored return false 
     *      if the username is not stored return true
     * 
     * So return true only if $is_stored and $is_login have the same value
     * 
     */
    return !($is_stored xor $is_login);
}


function is_password_valid ($conn, $username, $password, $is_login=false) {
    if (!isset($password) || empty($password)) return false;
    if (!contains_number($password)) return false;
    if (strlen($password) < 4 || strlen($password) > 10)  return false;
    // if for login check if the password matches the username
    if ($is_login) 
        return db_is_password_correct($conn, $username, $password);
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