<?php
require_once __DIR__ . "/../../../config/config.php";
require_once BASE_PATH . "server/api/auth/validation_manager.php";
require_once BASE_PATH . "server/api/auth/login_user.php";
require_once BASE_PATH . "config/messages.php";

// error_reporting(0);
// ini_set('display_errors', 0);


check_login_errors();

function check_login_errors() {
    $conn = NULL;
    try {
        $conn = db_connect();
    } catch (PDOException $e) {
        header('Content-type: application/json');
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
        header('Content-Type: application/json');
        // 400 should only be returned if the input is syntactically incorrect
        // it would not be right to send 400 if a username is taken
        http_response_code(400);
        echo json_encode($response);
        exit;
    }

    // if this point is reached all the fields are valid
    login_user($conn, $decoded_content);


    $response_message = get_response_message([]);

    if(!isset($_SESSION['userId'])) {
        $response = $response_message['failure']['nop'];
        header('Content-Type: application/json');
        // echo json_encode(['result' => true, "session" => $_SESSION['userId']]);
        echo json_encode($response);
        exit;  
    }

    $response = $response_message['login']['success'];
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;  
}
?>
