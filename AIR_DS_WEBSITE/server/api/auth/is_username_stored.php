<?php 
require_once __DIR__ . "/../../../config/config.php";
require_once BASE_PATH . "server/database/db_utils/db_connect.php";
require_once BASE_PATH . "server/api/auth/validation_manager.php";
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
    $response = validate_fields($conn, $decoded_content, $fields, false);

    //FIXME
    /**
     * problems:
     * - I dont know if the name is invalid or if it does not exist I just get true or false
     *  I can get that from client (based on how the cheks are made in client, username availability is the last one 
     *  it can change)
     * - Incorrect http response code 400 should be sent only if the username is syntactically incorrect not if it does not exist
     * - MAybe modify the validator function to answer if the username is not valid because of syntax or availability
     */


    if (!$response["result"]) {
        header('Content-Type: application/json');
        echo json_encode($response);
        exit;
    }

    
    header('Content-Type: application/json');
    echo json_encode(["result" => true, "message" => "username is not stored"]);
    exit;
}
?>
