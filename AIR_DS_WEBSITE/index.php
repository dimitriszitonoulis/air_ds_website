<!DOCTYPE html>
<?php
// const db_path = '..\AIR_DS_DB\db_utils\\'; 
// require_once db_path . '\db_initialize.php';
// require_once db_path . 'db_select_queries.php';
// require_once '..\server\database\db_utils\db_initialize.php';
// require_once '..\server\database\services\db_select_queries.php';
require_once '.\server\database\db_utils\db_initialize.php';
require_once '.\server\database\services\db_select_queries.php';
db_initialize();
?>
<?php
$airplane_codes = db_get_airports();
?>

<html>

<head>
  <script>
    /*
      Async call is used. What will happen if the html page loads 
      before the script return  the code of the airports?
      TODO try using await
     */
    async function getAirportCodes(){
      const uri = "./server/database/services/get_airport_codes.php";
      airportCodes = await fetch(uri)
                        .then(response => {
                          if(!response.ok) {
                            throw new Error("HTTP error " + response.status);
                          }
                          return response.json();
                        })
                        .then(data => decodeURIComponent(data))
                        .catch(error => console.log(error));
    
      // console.log("the airport codes are " + airport_codes);

      const select = document.getElementsByClassName('airport_code_selection');
      
      // add the airport codes to the select elements
      for(airportCode of airportCodes){
        // create the option element
        let option = document.createElement('option');
        option.value = airportCode;
        option.innerHTML = airportCode;
        // append it to the select elements
        select[0].appendChild(option);
      }
      
    }
    getAirportCodes();

  </script>
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
        <select class="airport_code_selection" name="departure_airport">
          <!-- TODO maybe add check that the array is not empty -->
          <!-- <?php foreach ($airplane_codes as $airplane_code): ?>
            <option value="<?= $airplane_code[0] ?>"><?= $airplane_code[0] ?></option>
          <?php endforeach; ?> -->
        </select>
      </fieldset>
      
      <fieldset>
        <!-- TODO add check that departure and return airports are not same -->
        <select class="airport_code_selection" name="return_airport">
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