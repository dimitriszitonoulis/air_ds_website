<?php
require_once __DIR__ . "/../../../config/config.php";

/**
 * Summary of login_user
 * @param mixed $conn - The connection to the database
 * @param mixed $username - the username of the user.
 * @return array{message: string, result: bool} - array containing the result of the login
 *  - message: a message that describes what happend. e.x. "Invalid credentials"
 *  - result: If login successful then true, otherwise false
 */
function login_user($conn, $username) {
    // if user credentials are correct create a session
    if (session_status() === PHP_SESSION_NONE)
        session_start();
    $_SESSION['userID'] = $username;
    return ["result" => true, "message" => "user logged in"];
}
?>