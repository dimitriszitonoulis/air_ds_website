<?php
require_once __DIR__ . "/../../../config/config.php";
require_once BASE_PATH . "config/messages.php";
require_once BASE_PATH . "server/database/db_utils/db_connect.php";
require_once BASE_PATH . "server/api/validation_manager.php";
require_once BASE_PATH . "server/api/reservation/reservation_validators.php";
require_once BASE_PATH . "server/database/services/trips/db_get_trips.php";

get_trips();

/**
 * Summary of get_trips
 * 
 * This function is an AJAX end point
 * 
 * It receives the username of a registered user.
 * It returns all the trips that user has made, newest trip 1st.
 * 
 * The username is received as a JSON like: 
 * {
 *  username: <username>,
 * }
 * 
 * 
 * It is responsible to receive the fetch request by the client (username).
 * Validate the input using the validation manager and validation functions.
 * Call the function that returns the trip about the user with the specified username.
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
 *  trips => array containing the trips that user has made
 * ]
 * 
 * the trips are an array like:
 * [
 *  departure_airport => <departure aiport code>,
 *  destination_airport => <destination airport code>,
 *  date => <departure date>,
 *  name => <name>,
 *  surname => <surname>,
 *  seat => <seat code>,
 *  price => <ticket price>
 * ] 
 * 
 * @return never
 */
function get_trips (){
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
    $expected_fields = ["username"];   

    // modify response_message to also include messages for the expected fields
    $response_message = get_response_message($expected_fields);
    $validator_parameters = [   // parameters needed by some validators that cannot be provided by the validator manager
        'conn' => $conn,
        'response' => $response_message
    ];          
    $validators = get_validators_booking();

    $response = null;
    // response array like:  ["result" => boolean, "message" => string, http_response_code => int]
    $response = validate_fields($conn, $decoded_content, $field_names, $expected_fields, $validator_parameters, $validators);

    // if a field is invalid
    if (!$response["result"]) {
        http_response_code($response['http_response_code']);
        echo json_encode($response);
        exit;
    }


    try {
        $trips = db_get_trips($conn, $decoded_content["username"]);
    } catch (Exception $e) {
        $response = $response_message['failure']['nop'];
        http_response_code($response['http_response_code']);
        echo json_encode($response);
        exit;  
    }
    
    $response["trips"] = format_trips($trips);
    //if this point is reached then return success message
    // $response was assigned it's value by the validators, 
    // since this point is reached the validation succeded
    // and $response has the value of a success message
    http_response_code($response['http_response_code']);
    echo json_encode($response);
    exit;  
}


/**
 * Summary of format_trips
 * 
 * Take as input the return rows ($trips) from the DB that have the format:
 * [
 *  departure_airport => "asdf",
 *  destination_airport => "adsf",
 *  date => "asdf",
 *  name => "fdfdf",
 *  surname => "aswdef",
 *  seat => "E-12",
 *  price => "580.12"
 * ] 
 * [
 *  departure_airport => "asdf",
 *  destination_airport => "adsf",
 *  date => "asdf",
 *  name => 'fgfdsgf',
 *  surname => 'ajhgfef',
 *  seat => 'F-12',
 *  price => '580.12'
 * ] 
 * 
 * 
 * And turn them to:
 * current_trip = [
 *  flight_info => [dest => asdf, dep=> adsf, date =>asdf],
 *  passengers[
 *      [
 *          name: 'fdfdf',
 *          surname: 'aswdef',
 *          seat: 'E-12',
 *          price: '580.12'
 *      ],
 *      [
 *          name: 'fgfdsgf',
 *          surname: 'ajhgfef',
 *          seat: 'F-12',
 *          price: '580.12'
 *      ]
 *    ]
 * ]
 * 
 * ATTENTION
 * The function assumes that the rows returned by the database are grouped by the similar flights
 * A flight is determined by the departure airport, the destination airport and the flight date
 * This means that the rows of the database must be sorted by those 3 columns
 * 
 * @param mixed $trips
 * @return array{passengers: array, flight_info: array}
 * 
 */
function format_trips($trips) {
    $formated_trips = []; 

    foreach ($trips as $trip) {
        // initialize $formated_trips
        if (empty($formated_trips)) {
            $formated_trips[] = [
                'flight_info' => [
                    'departure_airport' => $trip['departure_airport'],
                    'destination_airport' => $trip['destination_airport'],
                    'date' => $trip['date']
                ],
                'passengers' => [
                    [
                    'name' => $trip['name'],
                    'surname' => $trip['surname'],
                    'seat' => $trip['seat'],
                    'price' => $trip['price']
                    ]
                ]
            ];
            continue;
        }

        // get the trip info for the last element
        $last_index = array_key_last($formated_trips);
        // $flight_info = $formated_trips[$last_index];

        // extract trip info
        $dep_airport = $formated_trips[$last_index]['flight_info']['departure_airport'];
        $dest_airport = $formated_trips[$last_index]['flight_info']['destination_airport'];
        $date = $formated_trips[$last_index]['flight_info']['date'];

        // if at least one different, then it is a different flight
        // add new entry to $current_trip
        if ($dep_airport !== $trip['departure_airport'] ||
            $dest_airport !== $trip['destination_airport'] ||
            $date !== $trip['date']
        )
        {
            $formated_trips[] = [
                "flight_info" => [
                    'departure_airport' => $trip['departure_airport'],
                    'destination_airport' => $trip['destination_airport'],
                    'date' => $trip['date']
                ],
                'passengers' => [
                    // passengers contains one array for eache passenger
                    [
                    "name"=> $trip['name'],
                    "surname"=> $trip['surname'],
                    "seat"=> $trip['seat'],
                    "price"=> $trip['price']
                    ]
                ]
            ];  
            continue;
        }

        // if there is already an entry for the flight just add the passenger 
        // to the passengers array
        $formated_trips[$last_index]['passengers'][] = [
            "name"=> $trip['name'],
            "surname"=> $trip['surname'],
            "seat"=> $trip['seat'],
            "price"=> $trip['price']
        ];
    }    
    return $formated_trips;
}

?>