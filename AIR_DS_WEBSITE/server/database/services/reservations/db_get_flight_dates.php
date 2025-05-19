<?php

function db_get_flight_dates($conn, $dep_code = null, $dest_code = null)
{
    // if no departure airport code is provided
    if ($dep_code === null)
        throw new InvalidArgumentException("Departure airport code must not be null.");

    // if no destination airport code is provided
    if ($dest_code === null)
        throw new InvalidArgumentException("Departure airport code must not be null.");

    $query =
        "   SELECT 
            date
        FROM 
            flights
        WHERE 
            departure_airport = :dep_code AND destination_airport = :dest_code;
    ";

    // prepare statement
    $stmt = $conn->prepare($query);

    // Bind parameters
    $stmt->bindParam(':dep_code', $dep_code, PDO::PARAM_STR);
    $stmt->bindParam(':dest_code', $dest_code, PDO::PARAM_STR);

    // execute the statement
    $stmt->execute();

    // get all the dates for flights between the 2 specified airports
    $result = $stmt->fetchALL(PDO::FETCH_NUM);

    return $result;
}

function db_get_taken_seats($conn, $dep_code, $dest_code, $dep_date)
{
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
            departure_airport = ':dep_code' AND destination_airport = ':dest_code' AND date = ':dep_date';
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
    // TODO check if it needs another [0]
    $flight_id = $id_array[0]; // extract the id

    // find the taken seats from 
    $query =
    "   SELECT
            seat
        FROM 
            reservations
        WHERE
            flight_id = ':flight_id';
    ";
    // prepare the stament
    $stmt = $conn->prepare($query);
    // bind parameters
    $stmt->bindParam(':flight_id', $flight_id, PDO::PARAM_STR);

    // TODO maybe fetch num
    $result = $stmt->fetchALL(PDO::FETCH_ASSOC);

    return $result;
}

?>