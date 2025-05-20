<?php
// this file has functions to details aboput the user of an account

function db_get_full_name($conn, $username=null) {
    if ($username === null) 
        throw new InvalidArgumentException ("Username must not be null");

    $query = 
    "   SELECT
            name, surname
        FROM
            users
        WHERE
            username = :username;
    ";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(":username", $username, PDO::PARAM_STR);
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // result contains all the rows (1 row of the executed query)
    // get the 1st row
    return $result[0];
}
?>