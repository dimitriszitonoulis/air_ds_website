import { fields } from "./reservationFields.js";
import { getCollection, validateRealTime, validateSubmitTime } from "../validationManager.js";
import { isAirportValidSubmitTime } from "./reservationValidators.js";


getFlights();


/**
 * Function that adds option elemens containing the dates of the flights to the date select element
 * 
 * Adds event listeners to the airport fields.
 * When those fields change the value of both fields is collected
 * If it not valid then all the previous options inside the select element get removed
 * If it is valid then the codes of the airports get sent to the server.
 * The server then responds with the dates for the flights between the 2 airports
 */
async function getFlights() {

    // add an option element with value "-"
    const dateSelect = document.getElementById('date-input');
    let option = document.createElement('option');
    option.value = "-";
    option.innerText = "-";
    // if cloneNode is not used the <option> gets appended only to the 2nd select element
    dateSelect.appendChild(option.cloneNode(true));

    // FIXME 
    // for some reason even though I perform submit time validation
    // (checks if the airports have value "-" and shows error message)
    // if an airport has the value "-" nothing is shown
    // However, when the purchase button is pressed the message is shown... WHYYYY 
    //  get the details of the airport fields
    const airportFields = { 'airport': { ...fields['airports'], validatorFunction: isAirportValidSubmitTime } };

    const airports = [
        document.getElementById(airportFields['airport'].inputId['departure']),
        document.getElementById(airportFields['airport'].inputId['destination'])
    ];

    airports.forEach((airport) =>
        airport.addEventListener('change', async (e) => {
            //  get the details of the airport fields
            // const airportFields = {'airport' : { ...fields['airports'], validatorFunction: isAirportValidSubmitTime}};

            // do the airports have valid values?
            const are_airports_valid = await validateSubmitTime(airportFields);

            // if airports are not valid do nothing
            if (!are_airports_valid) {
                // remove all previous options from selection if an airport is wrong 
                dateSelect.replaceChildren();
                return;
            }

            // get airport elements value
            const depAirport = document.getElementById(airportFields['airport'].inputId['departure']).value;
            const destAirport = document.getElementById(airportFields['airport'].inputId['destination']).value;

            // get codes
            // if the text is: Brussels Airport (BRU) 
            // then getCode() returns: ["(BRU)", "BRU"]
            // access the 2nd element
            const depCode = getCode(depAirport)[1];
            const destCode = getCode(destAirport)[1];

            let response = await fetchFlights(depCode, destCode);

            if (!response['result']) {
                return;
            }

            // add the dates to the select element
            for (const date of response['dates']) {
                option = document.createElement('option');
                option.value = `${date}`;
                option.innerHTML = `${date}`;
                dateSelect.appendChild(option.cloneNode(true)); // append option to the select elements 
            }
        })
    )
}



async function fetchFlights(depCode, destCode) {
    const url = `${BASE_URL}server/api/reservation/get_flight_dates.php`;

    let data = "";
    try {
        const response = await fetch(url, {
            method: "POST",
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                // dep: depcode,
                // dest: destcode
                "dep_code": depCode,
                "dest_code": destCode
            })
        });

        // console.log(await response.text());

        data = await response.json();

        if (!response.ok) {
            console.error("Server returned error", data);
            throw new Error("HTTP error " + response.status);
        }


        return data;
    } catch (error) {
        console.error(error);
        return data;
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


// take string and keep what is inside parentheses
function getCode(str) {
    const regex = /\(([^)]+)\)/;
    return str.match(regex);
}

