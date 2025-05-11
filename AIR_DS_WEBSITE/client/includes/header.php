<?php
require_once __DIR__ . "/../../config/config.php";
require_once BASE_PATH . "config/config.php";
?>

<!-- TODO implement logout logic (erase session variables) -->

<header id="header">
  <nav>
    <a>Home</a>
    <a>My Trips</a>

    <?php 
      if (!isset($_SESSION["userId"])) { 
    ?>
      <a href="<?= BASE_URL ?>client/pages/auth/login.php">Login</a>
    <?php 
      } else {
    ?>
    <!-- TODO add script to log user out -->
      <a href= "<?= BASE_URL ?>client/includes/logout.php"id="logout">Logout</a>
    <?php 
      } 
    ?>
  </nav>
</header>