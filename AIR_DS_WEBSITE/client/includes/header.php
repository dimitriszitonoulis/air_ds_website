<?php
require_once __DIR__ . "/../../config/config.php";
require_once BASE_PATH . "config/config.php";
?>

<header id="header">
  <!-- For hamburger menu, on larger screens it is hidden -->
  <button id="hamburger">☰</button>

  <nav>
    <a href="<?= BASE_URL . "client/pages/home.php"?>">Home</a>
    <a href="<?= BASE_URL . "client/pages/my_trips.php"?>">My Trips</a>

    <?php 
      if (!isset($_SESSION["userId"])) { 
    ?>
      <a href="<?= BASE_URL ?>client/pages/auth/login.php">Login</a>
    <?php 
      } else {
    ?>
      <a href= "<?= BASE_URL ?>client/includes/logout.php"id="logout">Logout</a>
    <?php 
      } 
    ?>
  </nav>
</header>