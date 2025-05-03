<?php
require_once __DIR__ . "/../../../config/config.php";
require_once BASE_PATH . "server/database/db_utils/db_connect.php";
require_once BASE_PATH . "server/database/services/db_is_username_stored.php"
require_once BASE_PATH . "server/database/services/auth/db_is_email_stored.php"



check_authentication_errors();

function check_authentication_errors() {
    // $conn = NULL;
    // try {
    //     $conn = db_connect();
    // } catch (PDOException $e) {
    //     http_response_code(500);
    //     header('Content-type: application/json');
    //     echo json_encode(["error" => "Database connection falied"]);
    // }

    // // get the data from js script
    // $content = trim(file_get_contents("php://input")); // trim => remove white space from beggining and end
    // $decoded_content = json_decode($content, true); // true is used to get associative array
    
    // // if for some reason no data comes from the client (indiviadual array fields MUST be checked later)
    // if(!isset($decoded_content) || empty($decoded_contentent)) {
    //     http_response_code(400);
    //     echo json_encode(["error" => "Missing 'username' in JSON"]);
    //     exit;
    // }

    $decoded_content['name'] = "hello";
    $decoded_content['surname'] = "zisd";
    $decoded_content['username'] = "asd";
    $decoded_content['password'] = "12345";
    $decoded_content['email'] = "asdf@gmail.com";


    print_r($decoded_content);

    $is_name = is_name_valid($decoded_content["name"]);

    $is_surname = is_name_valid($decoded_content["surname"]);
    
    // must implement the function later
    $is_username_valid = is_username_valid($conn, $decoded_content["username"]);

    
    $is_password = is_password_valid($decoded_content["password"]);
    $is_email = is_email_valid($decoded_content["email"]);

    if($is_name && $is_surname && $is_username_valid $$is_password && $is_email)
        return true;

    return false;
}


function is_name_valid ($name) {

    if(!isset($name) || empty($name))
        return false;

    // if(!is_only_letters(strval($name)))

    if(!is_only_letters($name)) 
        return false;

    return true;
}


// TODO write script that checks the database 
function is_username_valid ($conn, $username) {
    if (!isset($username) || empty($username)) return false;
    if (!is_alphanumeric($username)) return false;

    $result = db_is_username_stored();
    if (!isset($result) || empty($result)) return false;

    return true;
}

function isPasswordValid ($password) {
    if (!isset($password) || empty($password)) return false;
    if (!contains_number($password)) return false;
    if (strlen($password) < 4 || strlen($password) > 10)  return false;
    return true;
}

// TODO maybe check if email already exists
function isEmailValid ($email) {
    if (!isset($email) || empty($email)) return false;
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) return false;
    if (!db_is_email_stored($conn, $email)) return false;

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