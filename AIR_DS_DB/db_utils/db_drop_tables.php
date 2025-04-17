<?php
$host = "localhost";
$dbname = "air_ds";
$dsn = "mysql:host={$host};dbname={$dbname}";
$username = "root";
$password = "";

// create PDO instance
try{
$conn = new PDO($dsn, $username, $password);
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
 
// Reservations must be deleted first.
// It has references to the primary keys of airports and users
$conn->exec("
DROP TABLE IF EXISTS reservations;
DROP TABLE IF EXISTS airports;
DROP TABLE IF EXISTS users;
");
} catch(PDOException $e){
    echo "FAiled to delete tables";
    echo $e;
}

?>