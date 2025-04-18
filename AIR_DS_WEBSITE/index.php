<!DOCTYPE html>
<?php
const db_path = '..\AIR_DS_DB\db_utils\\'; 
require_once db_path . '\db_initialize.php';
require_once db_path . 'db_select_queries.php';
// require_once '..\AIR_DS_DB\db_utils\db_initialize.php';
// require_once '..\AIR_DS_DB\db_utils\db_select_queries.php'; 
?>
<?php
$airplane_codes = db_get_airports();
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
        <select id="departure_airport" name="departure_airport">
          <!-- TODO maybe add check that the array is not empty -->
          <?php foreach ($airplane_codes as $airplane_code): ?>
            <option value="<?= $airplane_code[0] ?>"><?= $airplane_code[0] ?></option>
          <?php endforeach; ?>
        </select>
      </fieldset>
      
      <fieldset>
        <!-- TODO add check that departure and return airports are not same -->
        <select id="return_airport" name="return_airport">
          <!-- TODO maybe add check that the array is not empty
                On client side us js
                Ons server side compare the values sent from the form,
                if they are the same make the user refill hthe form (or only the specific fields) -->
          <?php foreach ($airplane_codes as $airplane_code): ?>
            <option value="<?= $airplane_code[0] ?>"><?= $airplane_code[0] ?></option>
          <?php endforeach; ?>
        </select>
      </fieldset>
      
      <fieldset>
        <!-- TODO check that the departure date has now passed -->
        <label for="departure_day">Select Departure Date</label>
        <input type="date" id="departure_day" name="departure_date">
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
</body>

</html>