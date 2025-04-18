<?php
function drop_tables(){
    require_once 'C:\xampp\htdocs\WEB_ZITONOULIS_DIMITRIOS_E22054\AIR_DS_WEBSITE\server\database\db_utils\db_connect.php';
    try{
        $conn = db_connect();
        // reservations table must be deleted first because
        // this table has references to the primary keys of airports and users
        $conn->exec("
        DROP TABLE IF EXISTS reservations;
        DROP TABLE IF EXISTS airports;
        DROP TABLE IF EXISTS users;
        ");
    } catch(PDOException $e){
        echo "FAiled to delete tables";
        echo $e;
    }
}
?>