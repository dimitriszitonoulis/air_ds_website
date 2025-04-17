<?php
$host = "localhost";
$dbname = "air_ds";
$dsn = "mysql:host={$host};dbname={$dbname}";
$username = "root";
$password = "";

// create PDO instance
$conn = new PDO($dsn, $username, $password);
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
?>