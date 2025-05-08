<?php
function get_response_message($fields) {
    $response_message = [
        "register" => [
            "failure" => [
                "missing" => ["result" => false, "message" => "missing content"],
                "invalid" => ["result" => false, "message" => "invalid credentials"]
            ],
            "success" => ["result" => true, "message" => "user registered"]
        ],
        "login" => [
            "failure" => ["result" => false, "message" => "invalid credentials"],
            "success" => ["result" => true, "message" => "user logged in"]
        ],
        "failure" => [
            "missing" => ["result" => false, "message" => "missing content"],
        ]
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

// TODO delete function
function add_field_messages($response_message, $fields) {
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