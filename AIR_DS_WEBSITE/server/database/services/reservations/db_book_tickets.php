<?php
function db_book_tickets($conn, $dep_code, $dest_code, $dep_date, $username, $tickets, $response)
{
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

    // find the user id 
    // find the flight id
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

    // for each ticket insert to db
    foreach($tickets as $ticket) {
        // find the taken seats from 
        $name = $ticket['name'];
        $surname = $ticket['surname'];
        $seat = $ticket['seat'];
        $price = $ticket["price"];

        $query =
        "   INSERT INTO
                reservations ( flight_id, user_id, name, surname, seat, price)
            VALUES
                (:flight_id, :user_id, :name, :surname, :seat, :price)
        ";
        // prepare the stament
        $stmt = $conn->prepare($query);
        // bind parameters
        $stmt->bindParam(':flight_id', $flight_id, PDO::PARAM_STR);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_STR);
        $stmt->bindParam(':name', $name, PDO::PARAM_STR);
        $stmt->bindParam(':surname', $surname, PDO::PARAM_STR);
        $stmt->bindParam(':seat', $seat, PDO::PARAM_STR);
        $stmt->bindParam(':price', $price, PDO::PARAM_STR);

        $stmt->execute();
    }
}
?>