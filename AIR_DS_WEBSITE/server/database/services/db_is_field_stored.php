<?php
/**
 * @file
 * 
 */

// checks if the given username is stored in the database
function db_is_username_stored($conn, $username=null) {
    // if no username is provided
    if ($username === null) throw new InvalidArgumentException("Username must not be null.");
    
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

function db_is_email_stored($conn, $email) {
    // query to be run
    // BINARY is used because the email is stored with collation utf8mb4_general_ci 
    // which is case insensitive
    // either use BINARY or use collation utf8mb4_bin when creating the db
    $query = "  SELECT email
                FROM users 
                WHERE BINARY email LIKE :email 
                ORDER BY CHAR_LENGTH(email);
            ";
    
    // prepare the statement
    $stmt = $conn->prepare($query);

    // Bind email parameter
    $stmt->bindParam(':email', $email, PDO::PARAM_STR);

    // execute the statement
    $stmt->execute();

    // if the email already exists then fetchAll() returns it so $result = <returned_username>
    // otherwise $result = empty array (0 elements)
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if(count($result) === 0) return false;

   return true;
}

function db_is_airport_code_stored($conn, $code=null) {
    // if no username is provided
    if ($code === null) throw new InvalidArgumentException("Username must not be null.");
    
    // query to be run
    // BINARY is used because the username is stored with collation utf8mb4_general_ci 
    // which is case insensitive
    // either use BINARY or use collation utf8mb4_bin when creating the db
    $query = "  SELECT code 
                FROM airports
                WHERE BINARY code = :code ;
            ";
    
    // prepare the statement
    $stmt = $conn->prepare($query);

    // Bind username parameter
    $stmt->bindParam(':code', $code, PDO::PARAM_STR);

    // execute the statement
    $stmt->execute();

    // get the all the usernames that match 
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // if there are now rows the username was not found
    if (count($result) === 0) return false;

    return true;
}
?>