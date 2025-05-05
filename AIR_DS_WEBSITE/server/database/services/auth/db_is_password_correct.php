<?php

// checks if the password is correct for the given username
function db_is_password_correct($conn, $username, $password) {
    // query to be run
    // BINARY is used because the username is stored with collation utf8mb4_general_ci 
    // which is case insensitive
    // either use BINARY or use collation utf8mb4_bin when creating the db
    $query = "  SELECT username, password
                FROM users 
                WHERE BINARY username LIKE :username AND password LIKE :password;
            ";
    
    // prepare the statement
    $stmt = $conn->prepare($query);

    // Bind parameters
    $stmt->bindParam(':username', $username, PDO::PARAM_STR);
    $stmt->bindParam(':password', $password, PDO::PARAM_STR);

    // execute the statement
    $stmt->execute();

    // get the all the usernames that match 
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);


    if (count($result) === 0) return false;

    return true;
}

?>