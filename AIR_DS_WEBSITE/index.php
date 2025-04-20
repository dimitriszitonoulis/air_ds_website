<!DOCTYPE html>
<?php
require_once '.\server\database\db_utils\db_initialize.php';
db_initialize();
?>

<html>

<head>

</head>

<body>
  <header>
    <nav>
      <a>Home</a>
      <a>My Trips</a>
      <a>Login</a>
      <a>Logout</a>
    </nav>
  </header>

  <section>
    <form>
      <fieldset>
        <label class="airport-code-label" for="airport-code-selection">Select the departure airport</label>
        <br>
        <select class="airport-code-selection" name="departure-airport"></select>
      </fieldset>

      <fieldset>
        <label class="airport-code-label" for="airport-code-selection">Select the destination airport</label>
        <br>
        <select class="airport-code-selection" name="destination-airport"></select>
      </fieldset>

      <fieldset>
        <label for="departure-date">Select Departure Date</label>
        <input type="date" id="departure-date" name="departure-date">
      </fieldset>

      <fieldset>
        <label id="number-of-tickets-label" for="number-of-tickets">Choose the number of tickets you want</label>
        <input type="number" id="number-of-tickets" name="number-of-tickets" value="1" min="1">
      </fieldset>

      <fieldset id="buy-tickes-button-fieldset">
        <input type="submit" id="buy-tickets-button" name="buy-tickets-button" value="Purchase">
      </fieldset>
    </form>
    </footer>

    <script src=".\client\scripts\getAirportCodes.js"></script>
    <script src=".\client\scripts\errorChecking.js"></script>
  </section>

  <footer>

  </footer>

</body>

</html>