
<?php
require_once __DIR__ . "/../../../config/config.php";
require_once BASE_PATH . "server/database/services/db_is_field_stored.php"; 
require_once BASE_PATH . "server/database/services/reservations/db_get_flight_info.php";
require_once BASE_PATH . "server/api/auth/auth_validators.php";
require_once BASE_PATH . "server/database/services/auth/db_get_full_name.php";
require_once BASE_PATH . "server/api/reservation/reservation_validators.php";


/**
 * Summary of get_validators
 * An array containing key value pairs of authorization fields and their validator functions
 * @return array{
 *  dep: (callable(mixed ):bool), 
 *  dest: (callable(mixed ):bool), 
 * }
 */
function get_validators() {
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
        "username" => function($params) {
            return is_username_valid_booking($params["conn"], $params["username"], $params["response"]);
        },
    ];
}


//TODO check that date is 30 days after current date before cancelation
function is_departure_date_valid_cancelation($conn, $dep_code, $dest_code, $dep_date, $response) {
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
