<?php

// checks if the password is correct for the given username
function db_are_credentials_correct($conn, $username=null, $password=null) {
    // if credentials not provided
    if ($username === null || $password === null)
        throw new InvalidArgumentException("Fields must not be null.");

    // query to be run
    // BINARY is used because the username is stored with collation utf8mb4_general_ci 
    // which is case insensitive
    // either use BINARY or use collation utf8mb4_bin when creating the db
    $query = "  SELECT username, password
                FROM users 
                WHERE BINARY username = :username AND BINARY password = :password;
            ";
    
    // prepare the statement
    $stmt = $conn->prepare($query);

    // Bind parameters
    $stmt->bindParam(':username', $username, PDO::PARAM_STR);
    $stmt->bindParam(':password', $password, PDO::PARAM_STR);

    // execute the statement
    $stmt->execute();

    // get the all the passwords that match for the given username 
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // if there are no rows then there is no acount with the given username, 
    // that has the given password
    if (count($result) === 0) return false;

    return true;
}

?>