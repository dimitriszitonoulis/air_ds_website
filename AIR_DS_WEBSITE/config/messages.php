<?php
function get_response_message($fields){
    $failure_message = [
        "missing" => ["result" => false, "message" => "missing content", "http_response_code" => 400],
        "invalid" => ["result" => false, "message" => "invalid credentials", "http_response_code" => 400],
        "problem" => ["result" => false, "message" => "could not perform operation", "http_response_code" => 400]
    ];

    // very generic do not use unless no other choice where true must be returned\
    // (ex is_payload_valid() in validation_manager.php)
    $success_message = ["result" => true, "message" => "operation successfull"];

    $response_message = [
        "register" => [
            "failure" => $failure_message,
            "success" => ["result" => true, "message" => "user registered"]
        ],
        "login" => [
            "failure" => $failure_message,
            "success" => ["result" => true, "message" => "user logged in"]
        ],
        "failure" => $failure_message,
        "success" => $success_message
    ];

    // add extra response messages for each field
    foreach ($fields as $field) {
        $response_message[$field] = [
            "missing" => ["result" => false, "message" => "missing field: $field"],
            "failure" => ["result" => false, "message" => "invalid $field"],
            "success" => ["result" => true, "message" => "valid $field"]
        ];
    }
    return $response_message;
}


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