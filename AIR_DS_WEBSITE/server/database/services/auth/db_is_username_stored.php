<?php 
require_once __DIR__ . "/../../../../config/config.php";
require_once BASE_PATH . "server/database/db_utils/db_connect.php";

// checks if the given username is stored in the database
function db_is_username_stored() {
    $conn = NULL;
    try {
        $conn = db_connect();
    } catch (PDOException $e) {
        http_response_code(500);
        header('Content-Type: application/json');
        echo json_encode(["error" => "Database connection failed"]);
        exit;
    }


    /**
     * The client send the data using POST method.
     * However this is done through js bu using the fetch API.
     * This means that what the client sends is not saved in POST super global variable.
     * The following trick is required to access the data correctly.
     */
    $content = trim(file_get_contents("php://input")); // trim => remove white space from beggining and end
    $decoded_content = json_decode($content, true); // true is used to get associative array
    
    // if for some reason no data comes from the client
    if(!isset($decoded_content['username'])) {
        http_response_code(400);
        echo json_encode(["error" => "Missing 'username' in JSON"]);
        exit;
    }

    $username = $decoded_content['username'];


    // query to be run
    // BINARY is used because the username is stored with collation utf8mb4_general_ci 
    // which is case insensitive
    // either use BINARY or use collation utf8mb4_bin when creating the db
    $query = "  SELECT username 
                FROM users 
                WHERE BINARY username 
                LIKE :username 
                ORDER BY CHAR_LENGTH(username);
            ";
    
    // prepare the statement
    $stmt = $conn->prepare($query);

    // find all usernames that start with the characters in username
    $like_username = $username . '%';

    // Bind username parameter
    $stmt->bindParam(':username', $like_username, PDO::PARAM_STR);

    // execute the statement
    $stmt->execute();

    // get the all the usernames that match 
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    header('Content-Type: application/json');
    echo json_encode($result);
    
    exit;
}

db_is_username_stored();
?>