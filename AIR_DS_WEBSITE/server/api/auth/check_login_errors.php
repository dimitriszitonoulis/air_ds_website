<?php
require_once __DIR__ . "/../../../config/config.php";
require_once BASE_PATH . "server/api/auth/validation_manager.php";
require_once BASE_PATH . "server/api/auth/login_user.php";
require_once BASE_PATH . "config/messages.php";

// error_reporting(0);
// ini_set('display_errors', 0);


check_login_errors();

function check_login_errors() {
    header('Content-type: application/json');

    $response_message = get_response_message([]);

    $conn = NULL;
    try {
        $conn = db_connect();
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(["result" => false, "message" => "Database connection failed"]);
        exit;
    }

    // get the data from js script
    $content = trim(file_get_contents("php://input")); // trim => remove white space from beggining and end
    $decoded_content = json_decode($content, true); // true is used to get associative array

    // what if the keys are not what I am expecting?
    // get the name of the fields that come from the client
    $field_names = array_keys($decoded_content);

    // array showing the validity of each field
    // like: field name => validity (boolean)
    // for now initialize all fields as false
    $fields =[];
    foreach($field_names as $name) {
        $fields[$name] = false;
    }

    $response = null;
    $response = validate_fields($conn, $decoded_content, $fields, true);
  
    if (!$response["result"]) {
        http_response_code(400);
        echo json_encode($response);
        exit;
    }

    $username = null;
    $password = null;
    if (array_key_exists("username", $fields)
        && array_key_exists("password", $decoded_content)) {    
        $username = $decoded_content["username"];
        $password = $decoded_content["password"];
    }

    try {
        $is_credentials_correct= db_is_password_correct($conn, $username, $password);
    } catch (Exception $e) {
        // TODO maybe change later to have specific message for login
        $response = $response_message['failure']['invalid'];
        http_response_code($response['http_response_code']);
        echo json_encode($response);
        exit;
    }

    if (!$is_credentials_correct) {
        $response = $response_message['failure']['invalid'];
        // FIXME sends 400 but it should send 200
        http_response_code($response['http_response_code']);
        echo json_encode($response);
        exit;
    }

    // if this point is reached all the fields are valid
    login_user($decoded_content);

    // TODO maybe add check that the session userId is the same as the username
    if(!isset($_SESSION['userId'])) {
        $response = $response_message['failure']['nop'];
        echo json_encode(["result" => true, "seesion" => $_SESSION['userId']]);
        // echo json_encode($response);
        exit;  
    }

    $response = $response_message['login']['success'];
    echo json_encode($response);
    exit;  
}
?>
