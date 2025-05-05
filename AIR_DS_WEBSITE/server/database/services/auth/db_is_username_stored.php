<?php 

// checks if the given username is stored in the database
function db_is_username_stored($conn, $username) {
    // query to be run
    // BINARY is used because the username is stored with collation utf8mb4_general_ci 
    // which is case insensitive
    // either use BINARY or use collation utf8mb4_bin when creating the db
    $query = "  SELECT username 
                FROM users 
                WHERE BINARY username LIKE :username 
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


// !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
// TODO MAYBE RETURN IF THE USERNAME IS FOUND AND NOT ALL MATCHING USERNAME
// !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!


    // get the all the usernames that match 
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
   
    return $result;
}
?>