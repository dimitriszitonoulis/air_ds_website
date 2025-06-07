<?php
function db_cancel_trip($conn, $dep_code=null, $dest_code=null, $dep_date=null, $username=null)
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

    // if no username is provided
    if ($username === null)
        throw new InvalidArgumentException("Username must not be null.");

    // get the id of the flight
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

    // get user id
    $query =
    "   SELECT
            id
        FROM 
            users
        WHERE 
            username = :username;
    ";
    // prepare statement
    $stmt = $conn->prepare($query);
    // bind parameters
    $stmt->bindParam(':username', $username, PDO::PARAM_STR);
    // execute query
    $stmt->execute();

    // retrieve response (first fetched column)
    $user_id = $stmt->fetchColumn(0);


    $query = 
    "   DELETE
        FROM
            reservations
        WHERE
            user_id = :user_id AND flight_id = :flight_id;
    ";

    $stmt = $conn->prepare($query);
    $stmt->bindParam(":flight_id", $flight_id, PDO::PARAM_STR);
    $stmt->bindParam(":user_id", $user_id, PDO::PARAM_STR);
    // execute query
    $stmt->execute();
}
?>