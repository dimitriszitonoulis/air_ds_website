<?php
require_once __DIR__ . "/../../../config/config.php";
require_once BASE_PATH . "server/database/db_utils/db_connect.php";
require_once BASE_PATH . "server/database/services/auth/db_is_username_stored.php";
require_once BASE_PATH . "server/database/services/auth/db_is_email_stored.php";


error_reporting(0);
ini_set('display_errors', 0);


check_registration_errors();



function check_registration_errors() {
    $conn = NULL;
    try {
        $conn = db_connect();
    } catch (PDOException $e) {
        header('Content-type: application/json');
        http_response_code(500);
        echo json_encode(["error" => "Database connection falied"]);
        exit;
    }

    // get the data from js script
    $content = trim(file_get_contents("php://input")); // trim => remove white space from beggining and end
    $decoded_content = json_decode($content, true); // true is used to get associative array


    // if for some reason no data comes from the client (indiviadual array fields MUST be checked later)
    if(!isset($decoded_content) || empty($decoded_content)) {
        header('Content-type: application/json');
        http_response_code(400);
        echo json_encode(["error" => "Missing content"]);
        exit;
    }

    $fields = ["name", "surname", "username", "password", "email"];

    foreach ($fields as $field) {
        if (!isset($decoded_content[$field])) {
            header('Content-type: application/json');
            http_response_code(400);
            echo json_encode(["error" => "Missing field: $field"]);
            exit;
        }
    }

    $is_name = is_name_valid($decoded_content["name"]);
    $is_surname = is_name_valid($decoded_content["surname"]);
    $is_username = is_username_valid($conn, $decoded_content["username"]);
    $is_password = is_password_valid($decoded_content["password"]);
    $is_email = is_email_valid($conn, $decoded_content["email"]);

    if($is_name && $is_surname && $is_username && $is_password && $is_email){
        header('Content-Type: application/json');
        echo json_encode(["response" => "user registered"]);
        exit;
    }

    header('Content-Type: application/json');
    echo json_encode(["response" => "failed to register user"]);     
    exit;
}


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

function contains_at_character ($txt) {
    $regex = "/@/";
    return preg_match($regex, $txt) === 1;
}
?>