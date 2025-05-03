<?php 
require_once __DIR__ . "/../../../../config/config.php";
require_once BASE_PATH . "server/database/db_utils/db_connect.php";
require_once BASE_PATH . "server/database/services/auth/db_is_username_stored.php"


/**
 * 
 * AJAX
 * END - POINT
 * 
 */


function db_check_username_exists() {
    $conn = NULL;
    try {
        $conn = db_connect();
    } catch (PDOException $e) {
        http_response_code(500);
        header('Content-type: application/json');
        echo json_encode(["error" => "Database connection falied"]);
    }

    // get the data from js script
    $content = trim(file_get_contents("php://input")); // trim => remove white space from beggining and end
    $decoded_content = json_decode($content, true); // true is used to get associative array
    
    // if for some reason no data comes from the client (indiviadual array fields MUST be checked later)
    if(!isset($decoded_content) || empty($decoded_content)) {
        http_response_code(400);
        echo json_encode(["error" => "Missing 'username' in JSON"]);
        exit;
    }

    $username = $decoded_content["username"];

    $result = db_is_username_stored($conn, $username);
    
    header('Content-Type: application/json');
    echo json_encode($result);
    
    exit;
}
?>
