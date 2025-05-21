<?php
require_once __DIR__ . "/../../../config/config.php";
require_once BASE_PATH . 'server\database\services\db_is_field_stored.php'; 



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
        }
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

// 0: Object { name: "Athens International Airport 'Eleftherios Venizelos'", code: "ATH" }
// ​
// 1: Object { name: "Brussels Airport", code: "BRU" }
// ​
// 2: Object { name: "Paris Charles de Gaulle Airport", code: "CDG" }
// ​
// 3: Object { name: "Leonardo da Vinci Rome Fiumicino Airport", code: "FCO" }
// ​
// 4: Object { name: "Larnaka International Airport", code: "LCA" }
// ​
// 5: Object { name: "Adolfo Suárez Madrid–Barajas Airport", code: "MAD" }


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
// TODO has same name as auth validator, says that it is referenced. Check if it is true
function is_name_valid_reservation ($name, $response) {
    if(!isset($name) || empty($name)) return $response['name']['missing'];
    if(!is_only_letters($name)) return $response['name']['invalid'];
    if (strlen($name) < 3 || strlen($name) > 20) return $response['name']['invalid'];
    return $response['success'];
}

// TODO check if the response message is correct
// TODO what if 2 people (in the current reservation have the same seat?)
function is_seat_valid($conn, $seat, $dep_code, $dest_code, $dep_date, $response) {
    if (!isset($seat) || empty($seat)) return $response['seat']['invalid'];

    $seat_letters = ['A', 'B', 'C', 'D', 'E', 'F'];
    $seat_max_number = 31;

    // each seat has a code like: <seat letter>-<seat number> ex A-22
    $seat_code = $seat.explode("-", $seat);

    // is the letter valid?
    if (!in_array($seat_code[0], $seat_letters)) return $response['seat']['invalid'];

    // is the seat number valid
    if ($seat_code > $seat_max_number) return $response['seat']['invalid'];

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
?>