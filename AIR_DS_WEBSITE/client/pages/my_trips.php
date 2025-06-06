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
    <div id="trips-info">

      <h2>No trips to show</h2>

      <table id="passenger-trips-header-row">
        <tr>
          <th>Departure</th>
          <th>Destination</th>
          <th>Date</th>
          <th>Cost</th>
          <th>Surname</th>
          <th>Seat</th>
          <th>Seat Price</th>
          <th>Ticket Price</th>
        <tr>
          <td id="departure-airport"></td>
          <td id="destination-airport"></td>
          <td id="departure-date"></td>
          <td id="total-cost"></td>
          <td id="passenger-surname"></td>
          <td id="passenger-seat"></td>
          <td id="paassenger-seat-price"></td>
          <td id="passenger-ticket-price"></td>
        </tr>
      </table>
    </div>

  </main>


  <?php include_once BASE_PATH . 'client/includes/footer.php' ?>

  <script> const BASE_URL = "<?= BASE_URL ?>";</script>
  <script type="module" src="<?= BASE_URL ?>client/scripts/myTrips/myTripsMain.js"></script>
</body>

</html>