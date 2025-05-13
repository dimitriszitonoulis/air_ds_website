<?php
require_once __DIR__ . "/../../config/config.php";
require_once BASE_PATH . '.\server\database\db_utils\db_initialize.php';
require_once BASE_PATH . "client\includes\start_session.php";
db_initialize();

?>

<!DOCTYPE html>

<html>

<head>
  <!-- <link rel="stylesheet" href="./client/styles/style.css">  -->
  <link rel="stylesheet" href="<?= BASE_URL ?>client/styles/style.css">
  <link rel="stylesheet" href="<?= BASE_URL ?>client/styles/header.css">
  <link rel="stylesheet" href = "<?= BASE_URL ?>client/styles/form.css">
  <link rel="stylesheet" href="<?= BASE_URL ?>client/styles/footer.css">
</head>

<body>

  <?php include_once BASE_PATH . 'client/includes/header.php' ?>

  <main>


    <!-- TODO maybe delete the ids from the labels -->

    <form id="purchase-tickets-form" action="" method="post">
      <!-- <form id="form"> -->

      <!-- departure aiport select field -->
      <fieldset>
        <label class="airport-label" for="departure-airport-input">Select the departure airport</label>
        <br>
        <!-- gets filled with js -->
        <select id="departure-airport-input" class="airport-selection" name="departure-airport"></select>
        <div id="departure-airport-error-message" class="error-message">Empty</div>
      </fieldset>

      <!-- destination airport select field -->
      <fieldset>
        <label class="airport-label" for="destination-airport-input">Select the destination airport</label>
        <br>
        <!-- gets filled with js -->
        <select id="destination-airport-input" class="airport-selection" name="destination-airport"></select>
        <div id="destination-aiport-error-message" class="error-message">Empty</div>
      </fieldset>

      <!-- departure date select field -->
      <fieldset>
        <label for="date-input">Select Departure Date</label>
        <input type="datetime-local" id="date-input" name="date">
        <div id="date-error-message" class="error-message">Empty</div>
      </fieldset>

      <!-- ticket number field -->
      <fieldset>
        <label id="ticket-label" for="ticket-input">Choose the number of tickets you want</label>
        <input type="text" id="ticket-input" name="ticket" value="1" required>
        <!-- <input type="number" id="ticket-number" name="ticket-number" value="1" min="1"> -->
        <div id="ticket-error-message" class="error-message">Empty</div>
      </fieldset>

      <fieldset id="purchase-button-fieldset">
        <input type="submit" id="purchase-button" name="purchase-button" value="Purchase">
        <div id="purchase-button-error-message" class="error-message"></div>
      </fieldset>
    </form>

  </main>

  <?php include_once BASE_PATH . 'client/includes/footer.php' ?>

  <script> const BASE_URL = "<?= BASE_URL ?>";</script>
  <script src="<?= BASE_URL ?>client/scripts/getAirports.js"></script>
  <!-- <script src="<?= BASE_URL ?>client/scripts/errorChecking.js"></script> -->

  <script type="module" src="<?= BASE_URL ?>client/scripts/reservation/checkReservationErrors.js"></script>
  
</body>

</html>