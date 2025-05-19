<?php

// checks if the given username is stored in the database
function db_is_username_stored($conn, $username=null) {
    // if no username is provided
    if ($username === null) throw new InvalidArgumentException("Username must not be null.");
    
    // query to be run
    // BINARY is used because the username is stored with collation utf8mb4_general_ci 
    // which is case insensitive
    // either use BINARY or use collation utf8mb4_bin when creating the db
    $query =
    "   SELECT 
            username 
        FROM
            users 
        WHERE BINARY 
            username = :username ;
    ";
    
    // prepare the statement
    $stmt = $conn->prepare($query);

    // Bind username parameter
    $stmt->bindParam(':username', $username, PDO::PARAM_STR);

    // execute the statement
    $stmt->execute();

    // get the all the usernames that match 
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // if there are now rows the username was not found
    if (count($result) === 0) return false;

    return true;
}

// TODO add null as default value and throw exception
function db_is_email_stored($conn, $email) {
    // query to be run
    // BINARY is used because the email is stored with collation utf8mb4_general_ci 
    // which is case insensitive
    // either use BINARY or use collation utf8mb4_bin when creating the db
    $query = 
    "   SELECT
            email
        FROM
            users 
        WHERE BINARY 
            email = :email 
    ";
    
    // prepare the statement
    $stmt = $conn->prepare($query);

    // Bind email parameter
    $stmt->bindParam(':email', $email, PDO::PARAM_STR);

    // execute the statement
    $stmt->execute();

    // if the email already exists then fetchAll() returns it so $result = <returned_username>
    // otherwise $result = empty array (0 elements)
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if(count($result) === 0) return false;

   return true;
}

function db_is_airport_code_stored($conn, $code=null) {
    // if no username is provided
    if ($code === null) throw new InvalidArgumentException("Username must not be null.");
    
    // query to be run
    // BINARY is used because the username is stored with collation utf8mb4_general_ci 
    // which is case insensitive
    // either use BINARY or use collation utf8mb4_bin when creating the db
    $query = 
    "   SELECT 
            code 
        FROM 
            airports
        WHERE BINARY 
            code = :code ;
    ";
    
    // prepare the statement
    $stmt = $conn->prepare($query);

    // Bind username parameter
    $stmt->bindParam(':code', $code, PDO::PARAM_STR);

    // execute the statement
    $stmt->execute();

    // get the all the usernames that match 
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // if there are now rows the username was not found
    if (count($result) === 0) return false;

    return true;
}

//TODO has similar code as db_get_taken_seats in db_get_flight_dates.php
// if something is wrong here it is also propaly wrong there
function db_is_seat_stored($conn, $seat, $dep_code, $dest_code, $dep_date){
    // if no seat is provided
    if ($seat === null) 
        throw new InvalidArgumentException("Departure airport code must not be null.");

    // if no departure airport code is provided
    if ($dep_code === null)
        throw new InvalidArgumentException("Departure airport code must not be null.");

    // if no destination airport code is provided
    if ($dest_code === null)
        throw new InvalidArgumentException("Departure airport code must not be null.");

    // if the departure date is null
    if ($dep_date === null)
        throw new InvalidArgumentException("Departure date must not be null.");


    // find the flight id
    $query =
    "   SELECT
            id
        FROM 
            flights
        WHERE 
            departure_airport = :dep_code AND destination_airport = :dest_code AND date = :dep_date;
    ";
    // prepare statement
    $stmt = $conn->prepare($query);
    // bind parameters
    $stmt->bindParam(':dep_code', $dep_code, PDO::PARAM_STR);
    $stmt->bindParam(':dest_code', $dest_code, PDO::PARAM_STR);
    $stmt->bindParam(':dep_date', $dep_date, PDO::PARAM_STR);
    // execute query
    $stmt->execute();

    // retrieve response (1 row with the flight id)
    $id_array = $stmt->fetchAll(PDO::FETCH_NUM);
    $flight_id = $id_array[0][0]; // extract the id

    // find the taken seats from 
    $query =
    "   SELECT
            seat
        FROM 
            reservations
        WHERE
            flight_id = :flight_id AND seat = :seat;
    ";
    // prepare the stament
    $stmt = $conn->prepare($query);
    // bind parameters
    $stmt->bindParam(':flight_id', $flight_id, PDO::PARAM_STR);
    $stmt->bindParam(':seat', $seat, PDO::PARAM_STR);

    // TODO maybe fetch num
    $result = $stmt->fetchALL(PDO::FETCH_ASSOC);

    if (count($result) === 0) return false;

    return true;
}
?>