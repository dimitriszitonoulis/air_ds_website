<?php

function get_response_message($fields){
    $failure_message = [
        "missing" => ["result" => false, "message" => "missing content", "http_response_code" => 400],
        
        // invalid but syntactically correct
        "invalid" => ["result" => false, "message" => "invalid credentials", "http_response_code" => 200],
        "nop" => ["result" => false, "message" => "did not perform operation", "http_response_code" => 400],
        "connection" => ["result" => false, "message" => "Could not establish connection", "http_response_code" => 500]
    ];

    // very generic do not use unless no other choice where true must be returned\
    // (ex is_payload_valid() in validation_manager.php)
    $success_message = ["result" => true, "message" => "operation successfull", "http_response_code" => 200];

    $response_message = [
        "register" => [
            // TODO maybe remove, not needed
            // must check the aut h files to see if these are used
            "failure" => $failure_message,
            // return 200 because the request is valid based on what the client sent (no syntactical errors)
            "username_taken" => ["result" => false, "message" => "username is taken", "http_response_code" => 200],
            "email_taken" =>  ["result" => false, "message" => "email is taken", "http_response_code" => 200],
            "success" => ["result" => true, "message" => "user registered" , "http_response_code" => 200]
        ],
        "login" => [
            // TODO maybe remove, not needed
            // must check the aut h files to see if these are used
            "failure" => $failure_message,
            "success" => ["result" => true, "message" => "user logged in", "http_response_code" => 200]
        ],
        "failure" => $failure_message,
        "success" => $success_message
    ];



    // add extra response messages for each field
    foreach ($fields as $field) {
        $response_message[$field] = [
            "missing" => ["result" => false, "message" => "missing field: $field", "http_response_code" => 400],
            // maybe change to invalid and valid
            // "failure" => ["result" => false, "message" => "invalid $field", "http_response_code" => 400],
            "invalid" => ["result" => false, "message" => "invalid $field", "http_response_code" => 400],
            "success" => ["result" => true, "message" => "valid $field", "http_response_code" => 200]
        ];
    }
    return $response_message;
}

// TODO delete function
function response_register($fields){
    foreach ($fields as $field) {
        $response_message[$field] = [
            "missing" => ["result" => false, "message" => "missing field: $field"],
            "failure" => ["result" => false, "message" => "invalid $field"],
            "success" => ["result" => true, "message" => "valid $field"]
        ];
    }
}

// TODO delete function
function add_field_messages($response_message, $fields)
{
    // $fields = ["name", "surname", "username", "password", "email"];
    foreach ($fields as $field) {
        $response_message[$field] = [
            "missing" => ["result" => false, "message" => "missing field: $field"],
            "failure" => ["result" => false, "message" => "invalid $field"],
            "success" => ["result" => true, "message" => "valid $field"]
        ];
    }
    return $response_message;
}
?>