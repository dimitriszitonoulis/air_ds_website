<?php 
require_once __DIR__ . "/../../../config/config.php";
require_once BASE_PATH . "server/database/db_utils/db_connect.php";
require_once BASE_PATH . "server/api/auth/validation_manager.php";
require_once BASE_PATH . "server/database/services/auth/db_is_field_stored.php";
require_once BASE_PATH . "config/messages.php";

is_username_stored();

function is_username_stored() {
    header('Content-type: application/json');
    $response_message = get_response_message([]);

    $conn = NULL;
    try {
        $conn = db_connect();
    } catch (PDOException $e) {
       $response = $response_message['failure']['connection'];
        http_response_code(500);
        echo json_encode($response);
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
    // response = ["result" => boolean, "message" => string]
    $response = validate_fields($conn, $decoded_content, $fields, false);
    // if a field is invalid
    if (!$response["result"]) {
        http_response_code(400);
        echo json_encode($response);
        exit;
    }

    $username = null;
    // check if the username is inside the content sent by the client
    if (array_key_exists("username", $decoded_content)) {
        // if it is, then its value has already been validated,
        // So no additional checks needed
        $username = $decoded_content["username"];
    }
    // is the username stored?
    try {
        $is_username_stored = db_is_username_stored($conn, $username);
    } catch (Exception $e) {
        // if exception do nothing
        $response = $response_message['failure']['nop'];
        http_response_code(500);
        echo json_encode($response);
        exit;  
    }

    // if the username is stored
    if($is_username_stored) {
        $response = ['result' => true, 'message' => "username is stored"];
        http_response_code(200);
        echo json_encode($response);
        exit;
    }
    
    header('Content-Type: application/json');
    echo json_encode(["result" => false, "message" => "username is not stored"]);
    exit;
}
?>
