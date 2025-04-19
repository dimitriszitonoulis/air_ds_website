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
        <select class="airport_code_selection" name="return_airport"></select>
      </fieldset>
      
      <fieldset>
        <label for="departure_date">Select Departure Date</label>
        <input type="date" id="departure_date" name="departure_date">
      </fieldset> 

      <fieldset>
        <label for="number_of_people">Choose the nnumber of tickets you want</label>
        <!-- TODO add check that the number of tickets is >= 0 -->
        <input type="number" id="number_of_people" name="number_of_people" value="0">
      </fieldset>

      <fieldset>
        <input type="submit" name="submit">
      </fieldset>
    </form>
  </footer>

  <script src=".\client\scripts\getAirportCodes.js"></script>
</body>
</html>