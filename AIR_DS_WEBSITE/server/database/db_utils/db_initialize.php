<?php
// *****************************************************************************************************************************************
// INITIALIZE DATABASE


function db_initialize()
{
    // ASSUMES THAT db_initialize AND db_connect.php ARE IN THE SAME FOLDER
    require_once 'db_connect.php';

    // create db
    try {
        // get the connection to the mySQL server
        $conn = db_connect_server();
        // get the config of the database in order to access its name
        $db_config = db_get_config();
        $conn->exec("CREATE DATABASE IF NOT EXISTS {$db_config['db_name']}");
    } catch (PDOException $e) {
        die("Database creation failed.\nCould not connect to server\n" . $e->getMessage());
    }

    //connect to db, add tables
    try {
        // get the connection to the mySQL server
        $conn = db_connect();

        // !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
        //  For development only
        include_once 'db_drop_tables.php';
        drop_tables();
        // !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!

        insert_tables($conn);

        // TODO uncomment the first time the db is created
        // include the script in index and redirect from index to the home page
        add_users($conn);
        add_airports(conn: $conn);
        add_flights($conn);
        add_reservations($conn);

    } catch (PDOException $e) {
        die("Database connection failed\n" . $e);
    }
}

// *****************************************************************************************************************************************





// -----------------------------------------------------------------------------------------------------------------------------------------
// HELPER FUNCTIONS

function insert_tables($conn)
{
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
     * ATTENTION! 
     * Username and email MUST be unique. 
     */
    // TODO maybe hash passwords for security (the length of password should be changed to 255 to store the hashes)
    $conn->exec("
        CREATE TABLE IF NOT EXISTS users(
        id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(20) NOT NULL,
        surname VARCHAR (20) NOT NULL,
        username VARCHAR(255) UNIQUE,
        password VARCHAR(10),       
        email VARCHAR(255) UNIQUE);"
    );

    // holds the table for the flights
    $conn->exec("
        CREATE TABLE IF NOT EXISTS flights(
        id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        date DATETIME NOT NULL,
        departure_airport VARCHAR(3) NOT NULL ,
        destination_airport VARCHAR(3) NOT NULL ,
        FOREIGN KEY (departure_airport) REFERENCES airports(code),
        FOREIGN KEY (destination_airport) REFERENCES airports(code)
        );"
    );
    // create table reservations
    // TODO maybe add the flight id as foreign key instead of the aiport code
    $conn->exec("
        CREATE TABLE IF NOT EXISTS reservations (
        id INT AUTO_INCREMENT PRIMARY KEY, 
        seat VARCHAR(4) NOT NULL,
        flight_id INT UNSIGNED NOT NULL,
        user_id INT UNSIGNED NOT NULL,
        FOREIGN KEY (flight_id) REFERENCES flights(id), 
        FOREIGN KEY (user_id) REFERENCES users(id));"
    );


}

function add_airports($conn)
{
    try {
        $conn->exec(
            "   INSERT INTO 
                airports (name, code, latitude, longitude, fee)
                VALUES
                ('Athens International Airport ''Eleftherios Venizelos''', 'ATH', 37.937225, 23.945238, 150),
                ('Paris Charles de Gaulle Airport', 'CDG', 49.009724, 2.547778, 200),
                ('Leonardo da Vinci Rome Fiumicino Airport', 'FCO', 41.81080, 12.25090, 150),
                ('Adolfo Suárez Madrid–Barajas Airport', 'MAD', 40.4895, 3.5643, 250),
                ('Larnaka International Airport', 'LCA', 34.8715, 33.6077, 150),
                ('Brussels Airport', 'BRU', 50.9002, 4.4859, 200);
        "
        );
    } catch (PDOException $e) {
        // no operation
    }
}

function add_users($conn)
{
    try {
        $users = [
            ['name' => 'Giorgos', 'surname' => 'Georgiou', 'username' => 'giog', 'password' => '12345', 'email' => 'giog@gmail.com'],
            ['name' => 'Maria', 'surname' => 'Asimiou', 'username' => 'mar', 'password' => '12345', 'email' => 'mar@gmail.com'],
            ['name' => 'Mpampis', 'surname' => 'Mpampitos', 'username' => 'mpam', 'password' => '12345', 'email' => 'mpam@gmail.com'],
            ['name' => 'Anastasia', 'surname' => 'Anastasopoulou', 'username' => 'astasia', 'password' => '12345', 'email' => 'astasia@gmail.com'],
            ['name' => 'Nikos', 'surname' => 'Nikolopoulos', 'username' => 'nik', 'password' => '12345', 'email' => 'nik@gmail.com'],
            ['name' => 'Georgia', 'surname' => 'Georgiou', 'username' => 'geog', 'password' => '12345', 'email' => 'geog@gmail.com'],
        ];

        foreach ($users as $user) {
            $conn->exec(
                "   INSERT INTO
                    users (name, surname, username, password, email)
                    VALUES 
                    ('{$user['name']}','{$user['surname']}','{$user['username']}','{$user['password']}','{$user['email']}');
            "
            );
        }
    } catch (PDOException $e) {
        //nop
    }
}

function add_flights($conn)
{
    // $day = date('d');
    $month = date('m');
    $year = date('Y');

    $flight_dates = [];
    for ($day = 1; $day < 29; $day++) {
        $flight_dates[$day] = date("Y-m-d", strtotime("{$year}-{$month}-{$day}"));
    }

    $stmt = $conn->query("SELECT code FROM airports;");
    $airport_codes = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // insert flights to and from every airport from the 1st up to the 28th day of the current month
    foreach ($airport_codes as $departure_airport) {
        foreach ($airport_codes as $destination_airport) {
            // don't add flights for the same airport
            if ($departure_airport['code'] === $destination_airport['code'])
                continue;

            foreach ($flight_dates as $date) {
                $conn->exec(
                    "   INSERT IGNORE INTO 
                        flights (date, departure_airport, destination_airport)
                    VALUES 
                        ('{$date}', '{$departure_airport['code']}', '{$destination_airport['code']}');
                "
                );
            }
        }
    }
}

function add_reservations($conn){
    $user_ids = null;
    $flights = null;
    $taken_seats = [];
    // $row_letters = ['A', 'B', 'C', 'D', 'E', 'F'];
    // $max_colums = 31;   // max seats per row
    $reservations = [];
  
    $taken_seat_letters = ['A', 'C', 'D', 'F'];
    $taken_seat_numbers = [1, 5, 14, 29];

    // get the user ids
    try {
        $stmt = $conn->query("SELECT id from users;");
        $user_ids = $stmt->fetchALL(PDO::FETCH_NUM);
    } catch (PDOException $e) {
        die("could not get user IDs" . $e);
    }

    // get the flight info
    try {
        $stmt = $conn->query("SELECT id FROM flights");
        $flights = $stmt->fetchALL(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        die("could not get flights" . $e);
    }


    // generate taken seats numbers
    foreach ($taken_seat_letters as $letter) {
        foreach ($taken_seat_numbers as $number) {
            $taken_seats[] = "{$letter}-{$number}";
        }
    }

    

    // generate array containing which user bought which seat
    $user_number = count($user_ids);
    $seat_number = count($taken_seats);
    for ($i = 0; $i < $seat_number; $i++) {
        // $user_ids is an array containing arrays of length 0 (the user ids)
        // assign each user some seats, does not matter which
        $user_id = $user_ids[$i % $user_number][0];
        // if the entry for seats of the current user does not exist intialize it
        if (!isset($reservations[$user_id])) {
            $reservations[$user_id] = [];
        }
        $reservations[$user_id][] = $taken_seats[$i];
    }

    try {
        // mark some seats from each flight as taken
        foreach ($flights as $flight) {
            foreach ($reservations as $user_id => $taken_seats) {
                foreach ($taken_seats as $seat) {
                    $query =
                    "   INSERT INTO 
                        reservations  (seat, flight_id, user_id)
                        VALUES
                        ('{$seat}', '{$flight['id']}', '{$user_id}');
                    ";
                    $stmt = $conn->prepare($query);
                    // TODO maybe bind paremeters... 
                    // But I enter the input...
                    $stmt->execute();
                }
            }
        }
    } catch (PDOException $e) {
        die("could not add reservations" . $e);
    }
}


?>