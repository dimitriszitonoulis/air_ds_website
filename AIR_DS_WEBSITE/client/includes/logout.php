<?php
require_once __DIR__ . "/../../config/config.php";
require_once BASE_PATH . "client/includes/start_session.php";

// if the user is logged in, log them out wihtout ending the session
function logout() {
    if (isset($_SESSION['userId'])) unset($_SESSION['usedId']);
    exit;
}
?>