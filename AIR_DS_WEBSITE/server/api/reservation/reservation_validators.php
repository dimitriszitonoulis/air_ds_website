<?php

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
function is_name_valid ($name, $response) {
    if(!isset($name) || empty($name)) return $response['name']['missing'];
    if(!is_only_letters($name)) return $response['name']['invalid'];
    if (strlen($name) < 3 || strlen($name) > 20) return $response['name']['invalid'];
    return $response['success'];
}
?>