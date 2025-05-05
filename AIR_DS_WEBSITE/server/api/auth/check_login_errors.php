<?php
require_once __DIR__ . "/../../../config/config.php";
require_once BASE_PATH . "server/api/auth/validation_manager.php";
require_once BASE_PATH. "server/api/auth/login_user.php";
    
   

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

    // Array with: field names => field validity
    $fields = [
        "username" => false,
        "password" => false,
    ];

    // response = ["result" => boolean, "message" => string]
    $response = null;
    $response = validate_fields($conn, $decoded_content, $fields);
  
    if (!$response['result']) {
        header('Content-type: application/json');
        http_response_code(400);
        echo json_encode($response);
        exit;
    }
 
    // if this point is reached all the fields are valid
    $response = login_user($conn, $decoded_content);

    // these are set from login_user()
    // $response["result"] = true;
    // $response["message"] = "user registered";
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;  
}
?>
