<?php 
require_once __DIR__ . "/../../../config/config.php";
require_once BASE_PATH . "server/database/db_utils/db_connect.php";

// checks if the given username is stored in the database
function db_is_username_stored() {
    $conn = NULL;
    try {
        $conn = db_connect();
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(["error" => "Database connection failed"]);
        exit;
    }
    
    // client has sent a JSON string using POST which must be decoded
    $data = json_decode(file_get_contents('php://input'), true);
    // $username = $_POST['username'];
    $username = $data['username'];

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
    
    // if (!empty($result)) {
    //     foreach ($result as $row) {
    //         echo "<br>";
    //         echo htmlspecialchars($row["username"]);
    //     }
    // }
    header('Content-Type: application/json');
    echo json_encode($result);
}

db_is_username_stored();
?>