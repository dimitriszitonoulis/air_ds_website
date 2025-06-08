<?php
/**
 * Summary of db_get_flight_dates
 * 
 * Function that returns the dates for flights between 2 airports
 * 
 * @param PDO $conn the connection to the database
 * @param string $dep_code the code of the departure airport
 * @param string $dest_code the code of the destination airport
 * @throws \InvalidArgumentException if any of the parameters are null
 */
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
    $result = $stmt->fetchALL(PDO::FETCH_COLUMN);

    return $result;
}

//TODO has similar code as db_is_seat_stored() in db_is_field_stored
// if something is wrong here it is also propaly wrong there
/**
 * Summary of db_get_taken_seats
 
 * @param PDO $conn the connection to the database
 * @param string $dep_code the code of the departure airport
 * @param string $dest_code the code of the departure airport
 * @param string $dep_date the departure date
 * @return mixed array containing the seat codes for the specified flight
 * @throws \InvalidArgumentException if any of the parameters are null
 */
function db_get_taken_seats($conn, $dep_code=null, $dest_code=null, $dep_date=null)
{
    // if no departure airport code is provided
    if ($dep_code === null)
        throw new InvalidArgumentException("Departure airport code must not be null.");

    // if no destination airport code is provided
    if ($dest_code === null)
        throw new InvalidArgumentException("Destination airport code must not be null.");

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

    // retrieve response (first fetched column)
    $flight_id = $stmt->fetchColumn(0);


    // find the taken seats from 
    $query =
    "   SELECT
            seat
        FROM 
            reservations
        WHERE
            flight_id = :flight_id;
    ";
    // prepare the stament
    $stmt = $conn->prepare($query);
    // bind parameters
    $stmt->bindParam(':flight_id', $flight_id, PDO::PARAM_STR);
    $stmt->execute();

    // group all the rows of the response into a single array
    $result = $stmt->fetchALL(PDO::FETCH_COLUMN);

    return $result;
}

/**
 * Summary of db_get_airport_information
 * 
 * @param PDO $conn the connection to the database
 * @param string $dep_code the code of the departure airport
 * @param string $dest_code the code of the departure airport
 * @return mixed array containging associative arrays like:
 *                  ["code" => airport code,
 *                  "latitude" => airport latitude,
 *                  "longitude" => airport longitude,
 *                  "fee" => airport fee]
 * @throws \InvalidArgumentException if any of the parameters are null
 */
function db_get_airport_information($conn, $dep_code = null, $dest_code = null) {
     // if no departure airport code is provided
    if ($dep_code === null)
        throw new InvalidArgumentException("Departure airport code must not be null.");

    // if no destination airport code is provided
    if ($dest_code === null)
        throw new InvalidArgumentException("Destination airport code must not be null.");

    $query =
    "   SELECT
           code, latitude, longitude, fee
        FROM 
           airports 
        WHERE 
            code = :dep_code OR code = :dest_code;
    ";
    // prepare statement
    $stmt = $conn->prepare($query);
    // bind parameters
    $stmt->bindParam(':dep_code', $dep_code, PDO::PARAM_STR);
    $stmt->bindParam(':dest_code', $dest_code, PDO::PARAM_STR);
    // execute query
    $stmt->execute();

    $response = $stmt->fetchAll(PDO::FETCH_ASSOC);

    return $response;
}

?>