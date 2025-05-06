<?php 

// checks if the given username is stored in the database
function db_is_username_stored($conn, $username) {
    // query to be run
    // BINARY is used because the username is stored with collation utf8mb4_general_ci 
    // which is case insensitive
    // either use BINARY or use collation utf8mb4_bin when creating the db
    $query = "  SELECT username 
                FROM users 
                WHERE BINARY username = :username ;
            ";
    
    // prepare the statement
    $stmt = $conn->prepare($query);

    // Bind username parameter
    $stmt->bindParam(':username', $username, PDO::PARAM_STR);

    // execute the statement
    $stmt->execute();

    // get the all the usernames that match 
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // if there are now rows the username was not found
    if (count($result) === 0) return false;

    return true;
}
?>