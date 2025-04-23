<!DOCTYPE html>
<?php
require_once '.\server\database\db_utils\db_initialize.php';
db_initialize();
?>

<html>

<head>
 
</head>

<body>

  <?php include_once '.\client\includes\header.php'?>

  <header>

  </header>

  <section>
    <form>
      <fieldset>
        <label class="airport-code-label" for="airport-code-selection">Select the departure airport</label>
        <br>
        <!-- gest filled with js -->
        <select class="airport-code-selection" name="departure-airport"></select>
        <div id="departure-aiport-error-message" class="error-message"><div>
      </fieldset>

      <fieldset>
        <label class="airport-code-label" for="airport-code-selection">Select the destination airport</label>
        <br>
        <!-- getsfilled with js -->
        <select class="airport-code-selection" name="destination-airport"></select>
        <div id="destination-aiport-error-message" class="error-message"><div>
      </fieldset>

      <fieldset>
        <label for="departure-date">Select Departure Date</label>
        <input type="date" id="departure-date" name="departure-date">
        <div id="departure-date-error-message" class="error-message"><div>
      </fieldset>

      <fieldset>
        <label id="ticket-number-label" for="ticket-number">Choose the number of tickets you want</label>
        <input type="number" id="ticket-number" name="ticket-number" value="1" min="1">
        <div id="ticket-number-error-message" class="error-message"></div>
      </fieldset>

      <fieldset id="buy-tickes-button-fieldset">
        <input type="submit" id="buy-tickets-button" name="buy-tickets-button" value="Purchase">
      </fieldset>
    </form>

  </section>

  <?php include_once '.\client\includes\footer.php'?>

    <script src=".\client\scripts\getAirportCodes.js"></script>
    <script src=".\client\scripts\errorChecking.js"></script>
</body>

</html>