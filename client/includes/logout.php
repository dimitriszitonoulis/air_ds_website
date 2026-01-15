<?php
require_once __DIR__ . "/../../config/config.php";
require_once BASE_PATH . "client/includes/start_session.php";

// if the user is logged in, log them out wihtout ending the session
logout();

function logout() {
    if (isset($_SESSION['userId'])) {
        // TODO maybe do not unset all session variables
        // session_unset();
        unset($_SESSION['userId']);
        // session_destroy();
    }

    // redirect to home page
    header("Location:" . BASE_URL. "client/pages/home.php");
    exit;
}
?>