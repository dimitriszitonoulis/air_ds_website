<?php
// this file has functions to details aboput the user of an account

db_get_full_name($conn, $username) {
    if ($username === null) 
        throw InvalidArgumentException ("Username must not be null");

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
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // result contains all the rows (1 row of the executed query)
    // get the 1st row
    return $resutl[0];
}
?>