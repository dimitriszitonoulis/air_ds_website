<?php
// *****************************************************************************************************************************************
// INITIALIZE DATABASE
function db_initialize() {
    require_once 'C:\xampp\htdocs\WEB_ZITONOULIS_DIMITRIOS_E22054\AIR_DS_WEBSITE\server\database\db_utils\db_connect.php';

    // create db
    try{
        // get the connection to the mySQL server
        $conn = db_connect_server();
        // get the config of the database in order to access its name
        $db_config = db_get_config();
        $conn->exec("CREATE DATABASE IF NOT EXISTS {$db_config['db_name']}");    
    } catch (PDOException $e){
        die("Database creation failed.\nCould not connect to server\n" . $e->getMessage());
    }
    
    //connect to db, add tables
    try{
        // get the connection to the mySQL server
        $conn = db_connect(); 
        insert_tables($conn);
        add_airports(conn: $conn);

        // !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
        //  For development only
        // include 'db_drop_tables.php';
        // drop_tables();
        // !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!

    } catch (PDOException $e){
        die("Database connection failed\n" . $e); 
    }  
}

// *****************************************************************************************************************************************





// -----------------------------------------------------------------------------------------------------------------------------------------
// HELPER FUNCTIONS
function insert_tables($conn){
    //create table airports
    $conn->exec("
        CREATE TABLE IF NOT EXISTS airports(
        code VARCHAR(3) PRIMARY KEY,
        name VARCHAR(255) NOT NULL,
        latitude DECIMAL(10, 8) NOT NULL,
        longitude DECIMAL (10, 8) NOT NULL,
        fee INT UNSIGNED NOT NULL);"
    );

    //create table users
    /*
     * A user can make a reservation and buy tickets for himself or more people.
     * If he buys tickets for more people then the columns for username, password and email for those people will be NULL.
     * ATTENTION! check must be made so that a user without an account cannot make reservations for hiself or others
     * Username and email MUST be unique. 
    */
    // TODO maybe hash passwords for security (the length of password should be changed to 255 to store the hashes)
    $conn->exec("
        CREATE TABLE IF NOT EXISTS users(
        id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        fname VARCHAR(20) NOT NULL,
        lname VARCHAR (20) NOT NULL,
        username VARCHAR(255) UNIQUE,
        password VARCHAR(10),       
        email VARCHAR(255) UNIQUE);"
        );

    // create table reservations
    // TODO maybe add CURENT_TIMESTAMP as default value to departure_date
    $conn->exec("
    CREATE TABLE IF NOT EXISTS reservations (
    id INT AUTO_INCREMENT PRIMARY KEY, 
    seat VARCHAR(3) NOT NULL,
    departure_date DATETIME NOT NULL,
    airportID VARCHAR(3) NOT NULL,
    user_id INT UNSIGNED NOT NULL,
    FOREIGN KEY (airportID) REFERENCES airports(code), 
    FOREIGN KEY (user_id) REFERENCES users(id));"
    );
}

function add_airports($conn){
   try{
    $conn->exec("
    INSERT INTO 
        airports (name, code, latitude, longitude, fee)
    VALUES
        ('Athens International Airport ''Eleftherios Venizelos''', 'ATH', 37.937225, 23.945238, 150),
        ('Paris Charles de Gaulle Airport', 'CDG', 49.009724, 2.547778, 200),
        ('Leonardo da Vinci Rome Fiumicino Airport', 'FCO', 41.81080, 12.25090, 150),
        ('Adolfo Suárez Madrid–Barajas Airport', 'MAD', 40.4895, 3.5643, 250),
        ('Larnaka International Airport', 'LCA', 34.8715, 33.6077, 150),
        ('Brussels Airport', 'BRU', 50.9002, 4.4859, 200);
        ");
   } catch (PDOException $e){
        // no operation
   }
}
// -----------------------------------------------------------------------------------------------------------------------------------------

?>