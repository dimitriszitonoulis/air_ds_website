<?php
require_once __DIR__ . "/../../config/config.php";
require_once BASE_PATH . "client\includes\start_session.php";

// TODO uncomment after testing
// // if the user has not logged in redirect to login page
// if (!isset($_SESSION['userId'])) {
//     header("Location:" . BASE_URL. "client/pages/auth/login.php");
// }    
?>

<!DOCTYPE html>

<html>
<head>
  <!-- <link rel="stylesheet" href="./client/styles/style.css">  -->
  <link rel="stylesheet" href="<?= BASE_URL ?>client/styles/style.css">
  <link rel="stylesheet" href="<?= BASE_URL ?>client/styles/header.css">
  <link rel="stylesheet" href="<?= BASE_URL ?>client/styles/form.css">
  <link rel="stylesheet" href="<?= BASE_URL ?>client/styles/footer.css">
</head>

<body>

  <?php include_once BASE_PATH . 'client/includes/header.php' ?>

  <!-- TODO maybe delete the ids from the labels -->
  <main>
    <!-- <div id="trips-info">
    </div> -->

  </main>


  <?php include_once BASE_PATH . 'client/includes/footer.php' ?>

  <script> const BASE_URL = "<?= BASE_URL ?>";</script>
  <script type="module" src="<?= BASE_URL ?>client/scripts/myTrips/myTripsMain.js"></script>
</body>

</html>