<?php
require_once 'db_connect.php';

function db_get_airports(){
    $conn = db_connect();
    // get all the codes of the airports in the database
    $stmt = $conn->prepare("SELECT code FROM airports");
    $stmt->execute();
    //Only one row is return so it does not matter if FETCH_NUM is used
    return $stmt->fetchall(PDO::FETCH_NUM);
}
?>