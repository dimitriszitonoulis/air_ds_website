import { addFullNames, fields } from "./bookingFields.js";
import { validateRealTime, validateSubmitTime } from '../validationManager.js';
import { isNameValid } from "./bookingValidators.js";
import { showError } from "../displayMessages.js";
import { getAirportInfo, getFullName, getTakenSeats } from "./getBookingInfo.js";
import { createSeatMap, showSeatMap } from "./createSeatMap.js";
import { addInfoFieldSets, fillUserInfo } from "./showNameForm.js";

// TODO delete later
const TICKET_NUMBER = 2;
const USERNAME = "giog";    // the username must be taken from the session variable
const DEPARTURE_AIRPORT = "ATH";
const DESTINATION_AIRPORT = "BRU";
const DATE = "2025-05-25 00:00:00";

const planeBody = document.getElementById('plane-body');    // get the plane body div
// TODO uncomment later
// seatmapContainer.style.display = "none";

// THESE MUST BE GLOBAL
// for seat selection
let curSeatDiv = null;
let selectedSeats = [];



main()

async function main () {
    // get info about distances from the db
    const airport_codes = {
        "dep_code": DEPARTURE_AIRPORT,
        "dest_code": DESTINATION_AIRPORT
    }
    const info = await getAirportInfo(airport_codes, BASE_URL);
    const airInfo1 = info[0];
    const airInfo2 = info[1];
    const distance = getDistance(airInfo1['latitude'], airInfo1['longitude'], airInfo2['latitude'], airInfo2['longitude']);
    const fee = parseFloat((airInfo1['fee'] + airInfo2['fee']).toFixed(2));
    const flightCost = parseFloat((distance / 10).toFixed(2));
    const seatCostTable =  { 
        "leg": 20, 
        "front": 10, 
        "other": 0 
    };
    
    setUpPassengers(USERNAME, TICKET_NUMBER, fields, BASE_URL);

    await setUpSeatMap(DEPARTURE_AIRPORT, DESTINATION_AIRPORT, DATE);

    setUpFieldValidation();

    const passengerFieldsets = document.querySelectorAll(".passenger-info-fieldset");
    // array containing information about each passenger,
    // their seat and the cost of their ticket
    let tickets = setTickets(passengerFieldsets, seatCostTable, fee, flightCost);

    // TODO delete later
    tickets = [
        {
            "name": "nghjke",
            "surname": "hgfdh",
            "seat": "sfvsfdgv",
            "seatCost": "csdfa",
            "total": "asdfsdfa"
        },
        {
            "name": "ndjgfhghj",
            "surname": "fghjfghjh",
            "seat": "qewrv",
            "seatCost": "nbcva",
            "total": "ahjkl"
        }
    ];
    addPricingInfo(DEPARTURE_AIRPORT, DESTINATION_AIRPORT, DATE, distance, fee, flightCost, tickets)
}


function setUpPassengers(username, ticketNumber, fields, baseUrl) {
    fillUserInfo(username, baseUrl);    // fill info about the registered user
    addInfoFieldSets(ticketNumber);     // add  fielsets for the rest of the users
    addFullNames(ticketNumber, fields); // fill the fields with information about the HTML elements containing 
}

async function setUpSeatMap (depAirport, destAirport, depDate) {
    await createSeatMap(depAirport, destAirport, depDate);    // pass them to seat map function 
    // after the seatmap is created select all the seats 
    const seats = planeBody.querySelectorAll(".seat");

    seats.forEach((seat) => 
        seat.addEventListener('click', (e) => {
            // if no passengers are selected nop
            // ONLY when a passenger is selected, can a seat be selected
            if (curSeatDiv === null) return;

            // if the user tries to select a seat for a specific person,
            // and they click 2 times on the same seat, de-select the seat
            if (curSeatDiv.innerText === seat.id) {
                seat.style.backgroundColor = "";
                curSeatDiv.innerText = "--";
                const i = selectedSeats.indexOf(seat.id);   // find index of element to be removed
                selectedSeats.splice(i, 1);                 // remove 1 element from  selectedSeats at the selected index
                return;
            }

            // if the seat for the current passenger already has a value, 
            // and the same passenger tries to select another seat, do not let them
            // They MUST  first deselect that  seat and then choose another
            if (curSeatDiv.innerText !== "--") {
                return;
            }

            // if a seat is selected by another passenger,
            // do not re-select it
            if (selectedSeats.includes(seat.id)) {
                console.log("already included");
                return;
            }

            seat.style.backgroundColor = "#93C572";
            curSeatDiv.innerText = seat.id;
            selectedSeats.push(seat.id);
    }));

}

function setUpFieldValidation(passengerFieldsets) {
    const bookingFields = { ...fields };

    // assign validator function to names and surnames
    // the same validator function is used for the names and surnames
    for (const currentField in bookingFields) {
        bookingFields[currentField].validatorFunction = isNameValid;
    }

    // only validate names and surnames if the customer chose to buy more that 1 ticket
    // if they bought only one ticket then the name and surname have passed validation
    // when the customer registered,
    // no need to redo check
    // if (bookingFields !== null)
    validateRealTime(bookingFields);

    const chooseSeatsBtn = document.getElementById('choose-seats-button');
    const chooseSeatsErrorDiv = document.getElementById('choose-seats-button-error-message');


    // let isAllValid = false;
    //TODO remove later
    // isAllValid = true;

    chooseSeatsBtn.addEventListener('click', async (e) => {
        // if the button is clicked without any field being checked do nothing 
        // e.preventDefault();
        //TODO uncomment later
        const isAllValid = await validateSubmitTime(bookingFields);
        // isAllValid = validateSubmitTime(bookingFields); 

        if (isAllValid) {

            // show seatmap
            // TODO maybe make fields readonly now
            showSeatMap();
            const passengerFieldsets = document.querySelectorAll(".passenger-info-fieldset");
            setUpPassengerSelection(passengerFieldsets);

        } else {
            showError(chooseSeatsErrorDiv, "Could not process names");
        }

    })
 
}

// this variable must be declared after the passenger fieldsets are set

function setUpPassengerSelection(passengerFieldsets) {
    // make name and surname read only
    const name = document.querySelectorAll('.name')
                                    .forEach((input) => input.readOnly = true);
    const surname = document.querySelectorAll('.surname')
                                    .forEach((input) => input.readOnly = true);

    // add event listeners to all passenger fieldsets
    passengerFieldsets.forEach((curFieldset) =>
        curFieldset.addEventListener('click', (e) => {
            // if the current fieldset is already selected de-select it 
            if (curSeatDiv === curFieldset.querySelector(".seat-info")) {
                curFieldset.style.backgroundColor = "";
                curSeatDiv = null;
                return;
            }
            // TODO when the last information button is clicked for the price  
            // do not forget to clear all the green colors 

            // if the current fieldset is pressed remove the colors from the others
            passengerFieldsets.forEach((fieldset) => fieldset.style.backgroundColor = "");

            // make current fieldset green
            curFieldset.style.backgroundColor = "#93C572";

            // store the seat info div that is child of the current fieldset
            curSeatDiv = curFieldset.querySelector(".seat-info");
        })
    );
}



// let tickets = setTickets(passengerFieldsets, seatCostTable, fee, flightCost);

function getDistance(lat1, lon1, lat2, lon2) {
    const degToRad = (deg) => (deg * Math.PI) / 180.0;

    const R = 6371e3; // Earth's radius in meters

    const f1 = degToRad(lat1);
    const f2 = degToRad(lat2);
    const Df = degToRad(lat2 - lat1);
    const Dth = degToRad(lon2 - lon1);

    const a = Math.sin(Df / 2) ** 2 + (Math.cos(f1) * Math.cos(f2) * Math.sin(Dth / 2) ** 2);
    const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
    const d = R * c;

    return d / 1000; // return distance in km
}

function setTickets(passengerFieldsets, seatCostTable, fee, flightCost) {
    let tickets = [];
    // get info about each passenger
    passengerFieldsets.forEach((fs) => {
        const name = fs.querySelector('.name').value;
        const surname = fs.querySelector('.surname').value;
        const seat = fs.querySelector('.seat-info').innerText;

        const current = {
            "name": name,
            "surname": surname,
            "seat": seat
        };

        tickets.push(current);
    });

    // get seat cost info for each passenger and total const for each passenger
    tickets.forEach((current) => {
        const seat = current['seat'];
        // take the row number from the seat
        const number = parseInt(seat.split('-')[1]); 

        // set the cost for each seat
        if (number === 1 || number === 11 || number === 12) {
            current['seatCost'] = seatCostTable['leg'];
        }
        else if (number > 1 && number < 11) {
            current['seatCost'] = seatCostTable['front'];
        }
        else {
            current['seatCost'] = seatCostTable['other'];
        }

        // set the total for the current ticket
        current['total'] = fee + flightCost + current['seatCost'];
    });

    return tickets;
}


function addPricingInfo(depAirport, destAirport, date, distance, fee, flightCost, tickets) {

    let total = 0;
    for (const ticket in tickets) {
        total += ticket['total'];
    }

    const table = document.getElementById('passenger-info-table');

    // get the columns for the flight information
    const depCol = document.getElementById('departure-airport')
    const destCol= document.getElementById('destination-airport');
    const depDateCol= document.getElementById('departure-date');
    const distCol = document.getElementById('distance');
    const feeCol = document.getElementById('fee');
    const costCol = document.getElementById('flight-cost');

    // fill the columns with the flight information
    depCol.innerText = depAirport;
    destCol.innerText = destAirport;
    depDateCol.innerText = date;
    distCol.innerText = distance; 
    feeCol.innerText = fee;
    costCol.innerText = flightCost;

    for (const i in tickets) {
        const ticket = tickets[i];
        const row = document.createElement('tr');
        for(const field in ticket){
            const col = document.createElement('td');
            col.innerText  = ticket[field];
            row.appendChild(col);
        }
        table.appendChild(row);
    }
}






