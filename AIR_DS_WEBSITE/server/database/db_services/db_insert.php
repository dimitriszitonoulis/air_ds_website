<?php
require_once 'db_connect';

function db_insert_users(){
    $conn = NULL;
    try {
        $conn = db_connect();
    } catch (PDOException $e){
        die("Connection failed " . $e);
    }

    $query = "
    INSERT INTO users (fname, lname, username, password, email)
    VALUES (:fname, :lname, :username, :password, :email);";

    $stmt = $conn->prepare($query);
 
    //TODO fininsh binding parameters and executing the query
}
?>