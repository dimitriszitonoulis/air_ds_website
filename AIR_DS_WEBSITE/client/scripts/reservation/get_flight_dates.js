import { fields } from "./reservationFields.js";
import { getCollection, validateRealTime, validateSubmitTime } from "../validationManager.js";
import { isAirportValidSubmitTime } from "./reservationValidators.js";


getFlights();

// the date err message div
async function getFlights() {
    
    // add an option element with value "-"
    const dateSelect = document.getElementById('date-input');
    let option = document.createElement('option');
    option.value = "-";
    option.innerText = "-";
    // if cloneNode is not used the <option> gets appended only to the 2nd select element
    dateSelect.appendChild(option.cloneNode(true));
    
    //  get the details of the airport fields
    // TODO check why it does not work
    // const airportFields = { ...fields['airports'], validatorFunction: isAirportValidSubmitTime };
    const airportFields = {'airport' : { ...fields['airports'], validatorFunction: isAirportValidSubmitTime}};


    console.log('validatorFunction:', airportFields.validatorFunction);
    console.log(airportFields);

    const are_airports_valid = await validateSubmitTime(airportFields);

    if (!are_airports_valid) {
        return;
    }

    // get airports
    const depAirport = document.getElementById(airportFields.inputId[0]).value;
    const destAirport = document.getElementById(airportFields.inputId[1]).value;

    // get codes
    const depCode = getCode(depAirport);
    const destCode= getCode(destAirport);

    let response = await fetchFlights(depCode, destCode);

    // TODO maybe implement logic in case of error
    if (!response['result']){
        return ;
    }


    // add the airport codes to the select elements

    // add the dates to the select element
    for (const date of response['dates']) {
        option = document.createElement('option');
        option.value = `${date}`;
        option.innerHTML = `${date}`;
        // append option to the select elements 
        dateSelect.appendChild(option.cloneNode(true));
    }
}



async function fetchFlights(depCode, destCode) {
    const url = `${BASE_URL}server/api/reservation/get_flight_dates.php`;

    let airports = "";
    try {
        const response = await fetch(url, {
            method: "POST",
            headers:{
                 'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                // dep: depcode,
                // dest: destcode
                "dep": "ATH",
                "dest": "BRU"
            })
        });
        if (!response.ok){
            throw new Error("HTTP error " + response.status);
        }
        airports = await response.json();
        return airports;
    } catch (error) {
        console.error(error);
        return airports;
    }
}

// 0: Object { name: "Athens International Airport 'Eleftherios Venizelos'", code: "ATH" }
// ​
// 1: Object { name: "Brussels Airport", code: "BRU" }
// ​
// 2: Object { name: "Paris Charles de Gaulle Airport", code: "CDG" }
// ​
// 3: Object { name: "Leonardo da Vinci Rome Fiumicino Airport", code: "FCO" }
// ​
// 4: Object { name: "Larnaka International Airport", code: "LCA" }
// ​
// 5: Object { name: "Adolfo Suárez Madrid–Barajas Airport", code: "MAD" }


function getCode(str) {
    const regex = /\(([^)]+)\)/;
    return str.match(regex);
}

