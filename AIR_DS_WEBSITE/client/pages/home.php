<!DOCTYPE html>
<?php
require_once __DIR__ . "/../../config/config.php";
require_once BASE_PATH . '.\server\database\db_utils\db_initialize.php';

db_initialize();
?>

<html>

<head>
  <!-- <link rel="stylesheet" href="./client/styles/style.css">  -->
  <link rel="stylesheet" href="<?= BASE_URL ?>client/styles/style.css"> 
</head>

<body>

  <?php include_once BASE_PATH . 'client/includes/header.php'?>

  <!-- TODO maybe delete the ids from the labels -->

  <form id="purchase-tickets-form" action="" method="post"> 
    <!-- <form id="form"> -->
    <fieldset>
      <label class="airport-label" for="airport-selection">Select the departure airport</label>
      <br>
      <!-- gets filled with js -->
      <select class="airport-selection" name="departure-airport"></select>
      <div id="departure-airport-error-message" class="error-message">Empty</div>
    </fieldset>

    <fieldset>
      <label class="airport-label" for="airport-selection">Select the destination airport</label>
      <br>
      <!-- gets filled with js -->
      <select class="airport-selection" name="destination-airport"></select>
      <div id="destination-aiport-error-message" class="error-message">Empty</div>
    </fieldset>

    <fieldset>
      <label for="departure-date">Select Departure Date</label>
      <input type="datetime-local" id="departure-date" name="departure-date">
      <div id="departure-date-error-message" class="error-message">Empty</div>
    </fieldset>

    <fieldset>
      <label id="ticket-number-label" for="ticket-number">Choose the number of tickets you want</label>
      <input type="text" id="ticket-number" name="ticket-number" value="1" required>
      <!-- <input type="number" id="ticket-number" name="ticket-number" value="1" min="1"> -->
      <div id="ticket-number-error-message" class="error-message">Empty</div>
    </fieldset>

    <fieldset id="buy-tickets-button-fieldset">
      <input type="submit" id="buy-tickets-button" name="buy-tickets-button" value="Purchase">
    </fieldset>
  </form>

  <?php include_once BASE_PATH . 'client/includes/footer.php'?>

  <script> const BASE_URL = "<?= BASE_URL ?>";</script>
  <script src="<?= BASE_URL ?>client/scripts/getAirports.js"></script>
  <script src="<?= BASE_URL ?>client/scripts/errorChecking.js"></script>
</body>

</html>