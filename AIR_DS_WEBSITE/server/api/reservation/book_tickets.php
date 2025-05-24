
<?php
require_once __DIR__ . "/../../../config/config.php";
require_once BASE_PATH . "config/messages.php";
require_once BASE_PATH . "server/database/db_utils/db_connect.php";
require_once BASE_PATH . "server/api/validation_manager.php";
require_once BASE_PATH . "server/api/reservation/reservation_validators.php";
require_once BASE_PATH . "server/database/services/reservations/db_get_flight_info.php";

book_tickets();


/**
 * Summary of get_airport_info
 * 
 * This function is an AJAX end point
 * 
 * It receives the codes of 2 airports.
 * It returns details about them.
 * 
 * The airport codes and the date are received as a JSON like: 
 * {
 *  dep_code: <departure airport code>,
 *  dest_code: <destination airport code>,
 * }
 * 
 * 
 * It is responsible to receive the fetch request by the client (departure code, destination code).
 * Validate the input using the validation manager and validation functions.
 * Call the function that returns the information about those airports.
 * Send the data back to the client.
 * 
 * If at any point something goes wrong an error message is sent
 * 
 * Type of responses:
 * The responses of this function are response_messages detailed in config/messages.php
 * 
 * The only exception to this rule is the success message if everything goes well.
 * This message is an array like:
 * [
 *  result => boolean,
 *  message => string
 *  http_response_code => int
 *  airport_info => array containing the information about the specified airports
 * ]
 * 
 * @return never
 */
function book_tickets (){
    header('Content-Type: application/json');

    $response_message = get_response_message([]);

    $conn = NULL;
    try {
        $conn = db_connect();
    } catch (PDOException $e) {
        $response = $response_message['failure']['connection'];
        http_response_code($response['http_response_code']);
        echo json_encode($response);
        exit;
    }

    // get the data from js script
    $content = trim(file_get_contents("php://input")); // trim => remove white space from beggining and end
    $decoded_content = json_decode($content, true); // true is used to get associative array

    // what if the keys are not what I am expecting?
    // get the name of the fields that come from the client
    $field_names = array_keys($decoded_content);
    $expected_fields = ["dep_code", "dest_code", "dep_date", "ticket_num", "username", "tickets"];   

    // modify response_message to also include messages for the expected fields
    $response_message = get_response_message($expected_fields);
    $validator_parameters = [   // parameters needed by some validators that cannot be provided by the validator manager
        'conn' => $conn,
        'response' => $response_message
    ];          
    $validators = get_validators_booking();

    // TODO delete later
    // http_response_code(200);
    // echo [var_dump($decoded_content)];
    // exit;

    $response = null;
    // response array like:  ["result" => boolean, "message" => string, http_response_code => int]
    $response = validate_fields($conn, $decoded_content, $field_names, $expected_fields, $validator_parameters, $validators);

    // if a field is invalid
    if (!$response["result"]) {
        http_response_code($response['http_response_code']);
        echo json_encode($response);
        exit;
    }


    $dep_code = $decoded_content["dep_code"];
    $dest_code = $decoded_content["dest_code"];
    $dep_date = $decoded_content["dep_date"];
    $ticket_num = $decoded_content["ticket_num"];
    $username = $decoded_content["username"];
    // array of arrays like: ["name" => <name>, "surname" => surname, "seat" => seat]
    $tickets = $decoded_content["tickets"]; 
    get_ticket_price($conn, $dep_code, $dest_code, $tickets);

    try {
        $response = db_book_tickets($conn, $dep_code, $dest_code, $dep_date, $username, $tickets, $response);
    } catch (Exception $e) {
        $response = $response_message['failure']['nop'];
        http_response_code($response['http_response_code']);
        echo json_encode($response);
        exit;  
    }
    

    // $response['airport_info'] = $airport_information;
    http_response_code($response['http_response_code']);
    echo json_encode($response);
    exit;  
}

// assumes that tickets are validated
// adds a column with the key: "price" to the $tickets array
function get_ticket_price($conn, $dep_code, $dest_code, $tickets){
    $airport_info = get_info($conn, $dep_code, $dest_code);
    $air_info1 = $airport_info[0];
    $air_info2 = $airport_info[1];
    $distance = get_distance($air_info1["latitude"], $air_info1['longitude'], $air_info2['latitude'], $air_info2['longitude']);
    $fee = get_fee($air_info1["fee"], $air_info2["fee"]);
    $flight_cost = get_flight_cost($distance);

    for ($i = 0; $i < count($tickets); $i++) {
        $seat_cost = get_seat_cost($tickets[$i]["seat"]);
        $ticket_price = $fee + $flight_cost + $seat_cost;
        $tickets[$i]["price"] = $ticket_price;
    }
}

function get_info($conn, $dep_code, $dest_code) {
    $airport_information = db_get_airport_information($conn, $dep_code, $dest_code);
    return $airport_information;
}

function get_fee($fee1, $fee2) {
    return round($fee1 + $fee2, 2);
}
        
function get_flight_cost($distance) {
    return round($distance / 10, 2);
}

function get_seat_cost($seat) {
    $parts = explode("-", $seat);
    $seat_number = $parts[1];
    $cost = 0;

    if ($seat_number == 1 || $seat_number == 11 || $seat_number == 12) $cost = 20;
    else if ($seat_number > 1 && $seat_number < 11) $cost = 10;
    else $cost = 0;

    return $cost;
}

function get_distance($lat1, $lon1, $lat2, $lon2) {
    // Earth's radius in kilometers
    $R = 6371e3;

    // Convert degrees to radians
    $f1 = deg2rad($lat1);
    $f2 = deg2rad($lat2);
    $df = deg2rad($lat2 - $lat1);
    $dth = deg2rad($lon2 - $lon1);

    $a = sin($df / 2) ** 2 + cos($f1) * cos($f2) * sin($dth / 2) ** 2;

    $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

    // Distance in kilometers
    $d = $R * $c;

    return $d / 1000;
}

?>