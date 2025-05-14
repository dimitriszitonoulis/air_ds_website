<?php

function db_get_flight_dates($conn, $dep_code=null, $dest_code="null") {
    // if no departure airport code is provided
    if ($dep_code === null) throw new InvalidArgumentException("Departure airport code must not be null.");

    // if no destination airport code is provided
    if ($dest_code=== null) throw new InvalidArgumentException("Departure airport code must not be null.");


    $query = "  SELECT date
                FROM flights
                WHERE departure_airport = :dep_code AND destination_airport = :dest_code;
             ";

    // prepare statement
    $stmt = $conn->prepare($query);

    // Bind parameters
    $stmt->bind(':dep_code', $dep_code, PDO::PARAM_STR);
    $stmt->bind(':dest_code', $dest_code, PDO::PARAM_STR);

    // execute the statement
    $stmt->execute();

    // get all the dates for flights between the 2 specified airports
    $result = $stmt->fetchALL(PDO::FETCH_ASSOC);

    return $result;
}

?>