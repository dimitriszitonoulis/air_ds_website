<?php
// FIXME
/**
 * it may cause problems beacause it is not in function
 * So when I use require_once it runs only the first time
 * If I want to run it multple times, I should put it inside a fucntion and call it everytime
 */
if(session_status() === PHP_SESSION_NONE)
    session_start();
?>