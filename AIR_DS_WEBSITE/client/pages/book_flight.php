<?php
require_once __DIR__ . "/../../config/config.php";
require_once BASE_PATH . "client\includes\start_session.php";

// TODO uncomment after testing
// // if the user has not logged in redirect to login page
// if (!isset($_SESSION['userId'])) {
//     header("Location:" . BASE_URL. "client/pages/auth/login.php");
// }    


// TODO uncomment after testing is finished
// maybe check if the values are empty or a method other than POST is used
// if a method other than post is used redirect to home page
// if ($_SERVER["REQUEST_METHOD"] !== "POST") {
//   header("Location:" . BASE_URL . "client/pages/home.php");
// }
// // if any of the fields are empty redirect to home page
// if(empty($_POST["departure-airport"]))
//   header("Location:" . BASE_URL . "client/pages/home.php");
// if(empty($_POST["destination-airport"]))
//   header("Location:" . BASE_URL . "client/pages/home.php");
// if(empty($_POST["date"]))
//   header("Location:" . BASE_URL . "client/pages/home.php");
// if(empty($_POST["ticket"]))
//   header("Location:" . BASE_URL . "client/pages/home.php");

// add the value of the fields to constants
// define("DEPARTURE_AIRPORT", $_POST['departure-airport']);
// define("DESTINATION_AIRPORT", $_POST['destination-airport']);
// define("DATE", $_POST['date']);
// define("TICKET_NUMBER", $_POST['ticket']);

?>

<!DOCTYPE html>


<html>

<head>
  <!-- <link rel="stylesheet" href="./client/styles/style.css">  -->
  <link rel="stylesheet" href="<?= BASE_URL ?>client/styles/style.css">
  <link rel="stylesheet" href="<?= BASE_URL ?>client/styles/header.css">
  <link rel="stylesheet" href="<?= BASE_URL ?>client/styles/form.css">
  <link rel="stylesheet" href="<?= BASE_URL ?>client/styles/footer.css">
  <link rel="stylesheet" href="<?= BASE_URL ?>client/styles/seat_map.css">
</head>

<body>

  <?php include_once BASE_PATH . 'client/includes/header.php' ?>

  <!-- TODO maybe delete the ids from the labels -->
  <main>
    <div id="form-div">
      <form id="book-flight-form">
        <fieldset class="passenger-info-fieldset">
          <!-- TODO maybe fill it with php
           and then when taking the values with js dont take this value -->
          <label class="ticket-info-label" for="name">Name</label>
          <input type="text" id="name-0" class="name" readonly>

          <label class="ticket-info-label" for="surname">Surname</label>
          <input type="text" id="surname-0" class="surname" readonly>

          <div id="fieldset-error-message-0" class="error-message">Empty</div>

          <!-- div  for the seat -->
           <!-- it will be hidden at first and appear when all the names are filled -->
          <div id="seat-field-0">
            <!-- id is needed to fill the seat dynamically -->
            <div id="seat-info-label-0" class="seat-info-label">Seat</div>
            <div id="seat-0" class="seat-info">--</div>
          </div>
        </fieldset>

        <!-- TODO change id of button to something more fitting -->
        <div id="choose-seats-div">
          <button type="button" id="choose-seats-button">Choose seats</button>
          <div id="choose-seats-button-error-message" class="error-message">Empty</div>
        </div>
      </form>
    </div>

    <div id="seat-map-container">
      <span>Select seats for passesger:</span>
      <div id="plane-body"></div>

      <div id="show-pricing-button-div">
        <button type="button" id="show-pricing-info-button">Show pricing</button>
        <div id="show-pricing-info-button-error-message" class="error-message">Empty</div>
      </div>


    </div>

    <div id="pricing-info">
      <table id="pricing-info-table">
        <tr id="airport-info-header-row">
          <th>Departure</th>
          <th>Destination</th>
          <th>Date</th>
          <th>Fee</th>
          <th>Flight Cost</th>
          <!-- <th>Cost</th> -->
        </tr>
        <tr id="airport-info-values-row">
          <td id="departure-airport"></td>
          <td id="destination-airport"></td>
          <td id="departure-date"></td>
          <td id="fee"></td>
          <td id="flight-cost"></td>
          <!-- <td id="total-cost"></td> -->
        </tr>

        <tr id="passenger-info-header-row">
          <th>Name</th>
          <th>Surname</th>
          <th>Seat</th>
          <th>Seat Price</th>
          <th>Ticket Price</th>
        </tr>
        <tr id="total-cost-row">
          <th colspan="4">Total cost</th>
          <td id="total-cost"></td>
        </tr>
      </table>
    </div>

    <!-- must be hidden first -->
    <button id="book-tickets-btn">Book tickets</button>
  </main>


  <?php include_once BASE_PATH . 'client/includes/footer.php' ?>

  <script> const BASE_URL = "<?= BASE_URL ?>";</script>
  <script type="module" src="<?= BASE_URL ?>client/scripts/booking/bookingMain.js"></script>
</body>

</html>