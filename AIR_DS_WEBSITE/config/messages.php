<?php
function get_response_message($fields){
    $failure_message = [
        "missing" => ["result" => false, "message" => "missing content"],
        "invalid" => ["result" => false, "message" => "invalid credentials"]
    ];

    $response_message = [
        "register" => [
            "failure" => $failure_message,
            "success" => ["result" => true, "message" => "user registered"]
        ],
        "login" => [
            "failure" => $failure_message,
            "success" => ["result" => true, "message" => "user logged in"]
        ],
        "failure" => $failure_message
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