<?php 
require_once __DIR__ . "/../../../../config/config.php";

// checks if the given username is stored in the database
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

    if(count($result) !== 0) return true;

   return false;
}
?>