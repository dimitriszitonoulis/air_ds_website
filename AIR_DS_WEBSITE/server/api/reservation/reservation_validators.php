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
    // check if the username is taken
    try {
        $is_stored = db_is_airport_code_stored($conn, $code);
    } catch (Exception $e) {
        return $response['failure']['nop'];
    }

    // is there another account with that username?
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

?>