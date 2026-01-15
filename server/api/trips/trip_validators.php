
<?php
require_once __DIR__ . "/../../../config/config.php";
require_once BASE_PATH . "server/database/services/db_is_field_stored.php"; 
require_once BASE_PATH . "server/database/services/reservations/db_get_flight_info.php";
require_once BASE_PATH . "server/api/auth/auth_validators.php";
require_once BASE_PATH . "server/database/services/auth/db_get_full_name.php";
require_once BASE_PATH . "server/api/reservation/reservation_validators.php";



/**
 * Summary of get_validators_trip
 * @return array{dep_code: (callable(mixed )), dep_date: (callable(mixed )), dest_code: (callable(mixed )), username: (callable(mixed ))}
 */
function get_validators_trip() {
    return [
        "dep_code" => function ($params)  {
            return is_airport_code_valid($params["conn"], $params["dep_code"], $params['response']); 
        },
        "dest_code" => function ($params)  {
            return is_airport_code_valid($params["conn"], $params["dest_code"], $params['response']); 
        },
        "dep_date" => function ($params) {
            return is_departure_date_valid_cancelation($params["conn"], $params["dep_code"], $params['dest_code'], $params['dep_date'], $params['response']);
        },
        "username" => function($params) {
            return is_username_valid_booking($params["conn"], $params["username"], $params["response"]);
        },
    ];
}



/**
 * Summary of is_departure_date_valid_cancelation
 * 
 * Check if the departure date for a flight is valid when a user wants to cancel the flight
 * 
 * A flight is considered valid if:
 * - It is at least 30 days after the current date
 * - There is a flight between the specified airports for that date
 * 
 * @param PDO $conn
 * @param string $dep_code - The code of the departure airport
 * @param string $dest_code - The code of the destination airport
 * @param string $dep_date - the departure date
 * @param mixed $response - An array containing response messages
 * @return mixed - the appropriate response message
 */
function is_departure_date_valid_cancelation($conn, $dep_code, $dest_code, $dep_date, $response) {
    $today = date("Y-m-d");
    
    // get current date and departure date in seconds
    $today_sec = strtotime($today);
    $dep_date_sec = strtotime($dep_date);
    $month_seconds = 30 * 86400;

    $difference = $dep_date_sec - $today_sec;
    // is the dep_date at least 30 days into the future
    if ($difference < $month_seconds) return $response['dep_date']['invalid'];

    $is_stored = db_is_date_stored($conn, $dep_code, $dest_code, $dep_date);
    if (!$is_stored) return $response['dep_date']['invalid'];

    // TODO maybe check if the flight for the specific date has empty seats

    return $response['success'];
}