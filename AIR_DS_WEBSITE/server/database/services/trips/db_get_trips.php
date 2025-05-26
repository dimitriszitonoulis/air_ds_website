<?php
function db_get_trips($conn, $username)
{
    // if no departure airport code is provided
    if ($username === null)
        throw new InvalidArgumentException("Username must not be null.");
    
     
    /**
     * this query retrieves the following information for the user with the corresponding username:
     *  
     * departure_airport       -> in flights
     * destination_airport,    -> in flights
     * date                    -> in flights   
     * name                    -> in reservations
     * surname                 -> in reservations
     * seat                    -> in reservations 
     * price                   -> in reservations
     * 
     * select the rows from the reservation where the flight_id
     * matches the id from flights
     *
     * from those rows only keep those where the user_id in reservations
     * matches the id of the user with the given username (from the table users)
     * 
     * Also, show the newest reservation first
     * 
     */
    $query = 
    "   SELECT
            f.departure_airport, f.destination_airport, f.date,
            r.name, r.surname, r.seat, r.price
        FROM
            reservations AS r
        INNER JOIN 
            flights AS f
        ON
            r.flight_id = f.id
        INNER JOIN
            users AS u
        ON r.user_id = u.id
        WHERE
            username = :username
        ORDER BY
            f.date DESC, f.departure_airport, f.destination_airport, r.name, r.surname, r.seat
    ";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(":username", $username, PDO::PARAM_STR);
    // execute query
    $stmt->execute();

    // retrieve response (first fetched column)
    $reservations = $stmt->fetchALL(PDO::FETCH_ASSOC);

    return $reservations;
}
?>