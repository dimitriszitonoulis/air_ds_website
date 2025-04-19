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
  </section>
  <footer>
    <form>
      <fieldset>
        <select class="airport_code_selection" name="departure_airport"></select>
      </fieldset>
      
      <fieldset>
        <select class="airport-code-selection" name="return-airport"></select>
      </fieldset>
      
      <fieldset>
        <label for="departure-date">Select Departure Date</label>
        <input type="date" id="departure-date" name="departure-date">
      </fieldset> 

      <fieldset>
        <label for="number-of-people">Choose the nnumber of tickets you want</label>
        <!-- TODO add check that the number of tickets is >= 0 -->
        <input type="number" id="number-of-people" name="number-of-people" value="0">
      </fieldset>

      <fieldset id="buy-tickes-button-fieldset">
        <input type="buy-tickets-button" id="buy-tickets-button" name="submit-button" value="Buy tickets">
      </fieldset>
    </form>
  </footer>

  <script src=".\client\scripts\getAirportCodes.js"></script>
  <script src=".\client\scripts\errorChecking.js"></script>
</body>
</html>