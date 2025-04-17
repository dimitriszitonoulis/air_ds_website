<?php
// Get array with the config of the database
// The config variables could be made global, however whenever this file is inlcuded in another file
// they would be available in that other file.
// They don't need to do that.
function db_get_config() {
    $host = 'localhost';
    $db_name = 'air_ds';
    return [
        'host' => "{$host}",
        'db_name' => "{$db_name}",
        'dsn' => "mysql:host={$host};dbname={$db_name}",
        'username' => "root",
        'password' => ""
    ];
}

// Connect to MYSQL server
//Used when creating the database
function db_connect_server(){
    $db_config = db_get_config();
    $conn = new PDO("mysql:host={$db_config['host']}", $db_config['username'], $db_config['password']);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    return $conn;
}

// Function to connect to the database
function db_connect(){
    $db_config = db_get_config();
    // create PDO instance
    $conn = new PDO($db_config['dsn'], $db_config['username'], $db_config['password']);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    return $conn;
}
?>