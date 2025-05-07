<?php 
require_once __DIR__ . "/../../../config/config.php";
require_once BASE_PATH . "server/database/db_utils/db_connect.php";
require_once BASE_PATH . "server/database/services/auth/db_is_field_stored.php";

is_username_stored();

function is_username_stored() {
    $conn = NULL;
    try {
        $conn = db_connect();
    } catch (PDOException $e) {
        header('Content-type: application/json');
        http_response_code(500);
        echo json_encode(["error" => "Database connection falied"]);
    }

    // get the data from js script
    $content = trim(file_get_contents("php://input")); // trim => remove white space from beggining and end
    $decoded_content = json_decode($content, true); // true is used to get associative array
    
    if (!isset($decoded_content) || empty($decoded_content)) {
        header('Content-type: application/json');
        http_response_code(400);
        echo json_encode(["error" => "Missing 'username' in JSON"]);
        exit;
    }

    $username = $decoded_content["username"];

    $is_stored = db_is_username_stored($conn, $username);
    
    if (!$is_stored) {
        header('Content-Type: application/json');
        echo json_encode(value: ["result" => false, "message" => "username is not stored"]);
        exit;
    }
    
    header('Content-Type: application/json');
    echo json_encode(["result" => true, "message" => "username is stored"]);
    exit;
}
?>
