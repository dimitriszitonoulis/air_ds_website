<?php
require_once __DIR__ . "/../../../config/config.php";
require_once BASE_PATH . "server/database/services/db_is_field_stored.php"; 
require_once BASE_PATH . "server/database/services/reservations/db_get_flight_info.php";
require_once BASE_PATH . "server/api/auth/auth_validators.php";
require_once BASE_PATH . "server/database/services/auth/db_get_full_name.php";


/**
 * Summary of get_validators
 * An array containing key value pairs of authorization fields and their validator functions
 * @return array{
 *  dep: (callable(mixed ):bool), 
 *  dest: (callable(mixed ):bool), 
 * }
 */
function get_validators_reservation() {
    return [
        "dep_code" => function ($params)  {
            return is_airport_code_valid($params["conn"], $params["dep_code"], $params['response']); 
        },
        "dest_code" => function ($params)  {
            return is_airport_code_valid($params["conn"], $params["dest_code"], $params['response']); 
        },
        "dep_date" => function ($params) {
            return is_departure_date_valid($params["conn"], $params["dep_code"], $params['dest_code'], $params['dep_date'], $params['response']);
        }
    ];
}

function get_validators_booking() {
    return [
        "dep_code" => function ($params)  {
            return is_airport_code_valid($params["conn"], $params["dep_code"], $params['response']); 
        },
        "dest_code" => function ($params)  {
            return is_airport_code_valid($params["conn"], $params["dest_code"], $params['response']); 
        },
        "dep_date" => function ($params) {
            return is_departure_date_valid($params["conn"], $params["dep_code"], $params['dest_code'], $params['dep_date'], $params['response']);
        },
        "ticket_num" => function($params) {
            return is_ticket_number_valid($params["conn"], $params['ticket_num'], $params["dep_code"], $params['dest_code'], $params['dep_date'], $params['response']);
        },
        "username" => function($params) {
            return is_username_valid_booking($params["conn"], $params["username"], $params["response"]);
        },
        "tickets" => function($params) {
            return is_tickets_valid($params["tickets"],  $params["username"], $params["response"]);
        },
    ];
}



/**
 * Summary of is_airport_code_correct
 * 
 * Checks if the supplied airport code is valid
 * A code is valid if:
 * - It is exists in the database
 * 
 * @param mixed $conn - the connection to the database
 * @param mixed $code - the supplied code
 * @param mixed $response - array containing error messages
 */
function is_airport_code_valid($conn, $code, $response) {
    $is_stored = false;

    // check if the code is inside the database
    try {
        $is_stored = db_is_airport_code_stored($conn, $code);
    } catch (Exception $e) {
        return $response['failure']['nop'];
    }
    
    // if the airport code is not found
    if (!$is_stored) return $response['failure']['not_found'];

    return $response['success'];
}



function is_departure_date_valid($conn, $dep_code, $dest_code, $dep_date, $response) {
    
    $today = date("Y-m-d");
    
    // get current date and departure date in seconds
    $today_sec = strtotime($today);
    $dep_date_sec = strtotime($dep_date);

    $difference = $dep_date_sec - $today_sec;
    if ($difference <= 0)  return $response['dep_date']['invalid'];

    $is_stored = db_is_date_stored($conn, $dep_code, $dest_code, $dep_date);
    if (!$is_stored) return $response['dep_date']['invalid'];

    // TODO maybe check if the flight for the specific date has empty seats

    return $response['success'];
}

function is_ticket_number_valid($conn, $ticket_number, $dep_code, $dest_code, $dep_date, $response) {
    // if empty is used then the string:"0" is considered empty and the wrong response is sent
    if (!isset($ticket_number) || trim($ticket_number) === '') 
        return $response['ticket_num']['missing'];
//    if (!isset($ticket_number) || empty($ticket_number)) return $response['ticket_num']['missing'];
    if (!is_numeric($ticket_number)) return $response['ticket_num']['invalid'];
    if (!is_int($ticket_number)) return $response['ticket_num']['invalid'];
    if ($ticket_number < 1) return $response['ticket_num']['invalid'];

    $total_seats = 186;
    // get number of already reserved seats from db
    $taken_seats = 0;
    // maybe the $dep_code, $dest_code, $dep_date are null
    try {
        $taken_seats = count(db_get_taken_seats($conn, $dep_code, $dest_code, $dep_date));
    } catch (Exception $e) {
        return $response['ticket_num']['invalid'];
    }
    
    if ($total_seats - ($taken_seats + $ticket_number) < 0)
        return $response['ticket_num']['invalid'];

    return $response['success'];
}

function is_username_valid_booking($conn, $username,  $response) {
    $is_syntax = is_username_syntax_valid($username, $response);
    if (!$is_syntax['result']) return $is_syntax;

    $is_stored = is_username_stored($conn, $username, $response);
    // if the username is not stored return error message
    if(!$is_stored['result']) return $response['username']['invalid'];

    return $response['success'];
}
function is_tickets_valid($conn, $tickets, $username, $dep_code, $dest_code, $dep_date, $response) {
    if (!isset($tickets) || empty($tickets)) return $response["tickets"]["invalid"];
    if (!is_array($tickets)) return $response["tickets"]["invalid"];

    $names = [];
    $surnames = [];
    $seats = [];
    $expected_fields = ["name", "surname", "seat"];

    // check if the ticket are like: 
    // ["name" => <name>, "surname" => <surname>, "seat" => <seat>]
    foreach ($tickets as $ticket) {
        $field_names = array_keys($ticket);
        $is_payload_valid_response = is_payload_valid(  $ticket, $field_names, $expected_fields, $response);
        if (!$is_payload_valid_response["result"]) return $is_payload_valid_response;
    }

    // fill the arrays with the corresponding fields
    foreach ($tickets as $ticket) {
        $names[] = $ticket["name"];
        $surnames[] = $ticket["surname"];
        $seats[] = $ticket["seat"];
    }

    // use the username to get the name and surname of the registered user
    // maybe I should check again for the validity of username
    $is_username_valid = is_username_valid_booking($conn, $username, $response);
    if ($is_username_valid['result']) return $is_username_valid;

    $user_fullname = db_get_full_name($conn, $username);
    

    is_names_valid($conn, $names, $user_fullname["name"], $response);
    is_names_valid($conn, $surnames, $user_fullname["surname"], $response);
    is_seats_valid($conn, $seats, $dep_code, $dest_code, $dep_date, $response);

}

// use the same function for the surname
function is_names_valid($conn, $names, $registered_name, $response) {
    $found_registered = false;

    foreach ($names as $name) {
        $is_syntax = is_name_syntax_valid_reservation($name, $response);
        if ($is_syntax["result"]) return $is_syntax;
        if ($name === $registered_name) $found_registered = true;
    }
    // is the registered user's name among those people's name
    if ($found_registered) return $response["tickets"]["invalid"];

    return $response["success"];
}

function is_seats_valid($conn, $seats, $dep_code, $dest_code, $dep_date, $response) {
    if (!isset($seats) || empty($seats)) return $response['tickets']['missing'];

    $letters = ['A', 'B', 'C', 'D', 'F'];
    $min_seat = 1;
    $max_seat = 31;
    $taken_seats = [];
    $viewed_seats = [];

    try {
        $taken_seats = db_get_taken_seats($conn, $dep_code, $dest_code, $dep_date);
    } catch (Exception $e) {
        return $response["tickets"]["invalid"];
    }

    foreach ($seats as $seat) {
        if (!isset($seat) || empty($seat)) return $response['tickets']['missing'];

        // every seat has the format "<letter>-<number>"
        // ex "A-18"
        $parts = explode("-",$seat);
        $letter = $parts[0];
        $number = $parts[1];

        // is the row letter of the seat valid
        if (!in_array($letter, $letters)) return $response["tickets"]["invalid"];

        // is the column number of the seat valid
        if ($number < $min_seat || $number > $max_seat) return $response["tickets"]["invalid"];

        // is the seat already reserved
        if (in_array($seat,$taken_seats));

        // it the same seat added twice?
        if (in_array($seat, $viewed_seats));

        // add seat to viewed seats
        $viewed_seats[] = $seat;
    }

    return $response["success"];
}

/*
function is_seat_valid($conn, $seat, $dep_code, $dest_code, $dep_date, $response) {
    if (!isset($seat) || empty($seat)) return $response['seat']['invalid'];

    $seat_letters = ['A', 'B', 'C', 'D', 'E', 'F'];
    $seat_max_number = 31;
    $seat_min = 1;

    // each seat has a code like: <seat letter>-<seat number> ex A-22
    $seat_code = $seat.explode("-", $seat);

    // is the letter valid?
    if (!in_array($seat_code[0], $seat_letters)) return $response['seat']['invalid'];

    // is the seat number valid?
    if ($seat_code < $seat_min || $seat_code > $seat_max_number) return $response['seat']['invalid'];

    $is_stored = false;
    try {
        $is_stored = db_is_seat_stored($conn, $seat, $dep_code, $dest_code, $dep_date);
    } catch (Exception $e) {
        return $response['failure']['nop'];
    }
    // if the seat is stored for the specific flight, another user has booked it
    if ($is_stored) return $response['seat']['invalid'];

    return $response['success'];
}
*/

/**
 * Summary of is_name_valid
 * 
 * function that checks the validity of a name
 * 
 * This function is used to check the validity of both name and surname
 * 
 * a name is valid if:
 *  - it not empty or null
 *  - it contains only letters
 *  - it has no more than 20 and no less than 3 characters
 * 
 * @param mixed $name - the name to be validated
 * @param mixed $response - an array containing response messages
 */


// TODO check if the response message is correct
// TODO what if 2 people (in the current reservation have the same seat?)
function is_name_syntax_valid_reservation ($name, $response) {
    if(!isset($name) || empty($name)) return $response['name']['missing'];
    if(!is_only_letters($name)) return $response['name']['invalid'];
    if (strlen($name) < 3 || strlen($name) > 20) return $response['name']['invalid'];
    return $response['success'];
}