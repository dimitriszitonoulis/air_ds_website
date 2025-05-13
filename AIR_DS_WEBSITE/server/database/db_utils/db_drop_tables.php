<?php
function drop_tables(){
    // ASSUMES THAT db_drop_tables.php AND db_connect.php ARE IN THE SAME FOLDER
    require_once 'db_connect.php';
    try{
        $conn = db_connect();
        // reservations table must be deleted first because
        // this table has references to the primary keys of airports and users
        $conn->exec("
        DROP TABLE IF EXISTS reservations;
        DROP TABLE IF EXISTS flights;
        DROP TABLE IF EXISTS airports;
        DROP TABLE IF EXISTS users;
        ");
    } catch(PDOException $e){
        echo "Failed to delete tables";
        echo $e;
    }
}
?>