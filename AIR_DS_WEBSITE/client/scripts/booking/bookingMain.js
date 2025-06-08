import { addFullNames, fields } from "./bookingFields.js";
import { validateRealTime} from '../validationManager.js';
import { isNameValid } from "./bookingValidators.js";
import { clearError, showError } from "../displayMessages.js";
import { getAirportInfo, getFullName, getTakenSeats } from "./getBookingInfo.js";
import { createSeatMap, hideSeatMap, showSeatMap } from "./createSeatMap.js";
import { addInfoFieldSets, fillUserInfo } from "./showNameForm.js";
import { bookTickets } from "./bookTickets.js";

// TODO delete later
const TICKET_NUMBER = 2;
const USERNAME = "Dim";    // the username must be taken from the session variable
const DEPARTURE_AIRPORT = "ATH";
const DESTINATION_AIRPORT = "BRU";
const DATE = "2025-06-26 00:00:00";

// get info about distances from the db
const airport_codes = {
    "dep_code": DEPARTURE_AIRPORT,
    "dest_code": DESTINATION_AIRPORT
}
const INFO = await getAirportInfo(airport_codes, BASE_URL);
const AIR_INFO1 = INFO[0];
const AIR_INFO2 = INFO[1];

// THESE MUST BE GLOBAL
// for seat selection
let CURR_SEAT_DIV = null;
let SELECTED_SEATS = [];



main()

async function main() {

    // make the seatmap container invisible
    hideSeatMap();
    hidePricingInfo();
    hideBookTicketsBtn();

    setUpPassengers(USERNAME, TICKET_NUMBER, fields, BASE_URL);

    await setUpSeatMap(DEPARTURE_AIRPORT, DESTINATION_AIRPORT, DATE);

    setUpFieldValidation();

    // this variable must be declared after the passenger fieldsets are set
    const passengerFieldsets = document.querySelectorAll(".passenger-info-fieldset");

    setUpSeatValidation(passengerFieldsets);

    // add button with event listener for when to sumbit
    submitBooking(passengerFieldsets);
}

async function submitBooking(passengerFieldsets) {
    const bookBtn = document.getElementById("book-tickets-btn");

    bookBtn.addEventListener('click', async () => {
        const tickets = setTickets(passengerFieldsets);
        
        const flightInfo = {
            "dep_code":     DEPARTURE_AIRPORT,
            "dest_code":    DESTINATION_AIRPORT,
            "dep_date":     DATE,
            "ticket_num":   TICKET_NUMBER,
            "username":     USERNAME,
            "tickets":      tickets
        }
        const isBooked = bookTickets(flightInfo, BASE_URL);

        if (isBooked) {
            const redirectUrl = `${BASE_URL}/client/pages/my_trips.php`;
            window.location.replace(redirectUrl);
        }
    });

    
}




function setUpPassengers(username, ticketNumber, fields, baseUrl) {
    fillUserInfo(username, baseUrl);    // fill info about the registered user
    addInfoFieldSets(ticketNumber);     // add  fielsets for the rest of the users
    addFullNames(ticketNumber, fields); // fill the fields with information about the HTML elements containing 
}

async function setUpSeatMap(depAirport, destAirport, depDate) {
    await createSeatMap(depAirport, destAirport, depDate);    // pass them to seat map function 
    // after the seatmap is created select all the seats 
    const planeBody = document.getElementById('plane-body');    // get the plane body div
    const seats = planeBody.querySelectorAll(".seat");

    seats.forEach((seat) =>
        seat.addEventListener('click', (e) => {
            // if no passengers are selected nop
            // ONLY when a passenger is selected, can a seat be selected
            if (CURR_SEAT_DIV === null) return;

            // if the user tries to select a seat for a specific person,
            // and they click 2 times on the same seat, de-select the seat
            if (CURR_SEAT_DIV.innerText === seat.id) {
                seat.style.backgroundColor = "";
                CURR_SEAT_DIV.innerText = "--";
                const i = SELECTED_SEATS.indexOf(seat.id);   // find index of element to be removed
                SELECTED_SEATS.splice(i, 1);                 // remove 1 element from  selectedSeats at the selected index
                return;
            }

            // if the seat for the current passenger already has a value, 
            // and the same passenger tries to select another seat, do not let them
            // They MUST  first deselect that  seat and then choose another
            if (CURR_SEAT_DIV.innerText !== "--") {
                return;
            }

            // if a seat is selected by another passenger,
            // do not re-select it
            if (SELECTED_SEATS.includes(seat.id)) {
                console.log("already included");
                return;
            }

            seat.style.backgroundColor = "#93C572";
            CURR_SEAT_DIV.innerText = seat.id;
            SELECTED_SEATS.push(seat.id);
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

    chooseSeatsBtn.addEventListener('click', async (e) => {
        //TODO uncomment later
        // const isAllValid = await validateSubmitTime(bookingFields);
        let isAllValid = true;

        if (isAllValid) {
            //hide button
            chooseSeatsBtn.style.display = "none";
            showSeatMap();
            const passengerFieldsets = document.querySelectorAll(".passenger-info-fieldset");
            setUpPassengerSelection(passengerFieldsets);
        } else {
            showError(chooseSeatsErrorDiv, "Could not process names");
        }

    })

}

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
            if (CURR_SEAT_DIV === curFieldset.querySelector(".seat-info")) {
                curFieldset.style.backgroundColor = "";
                CURR_SEAT_DIV = null;
                return;
            }
            // TODO when the last information button is clicked for the price  
            // do not forget to clear all the green colors 

            // if the current fieldset is pressed remove the colors from the others
            passengerFieldsets.forEach((fieldset) => fieldset.style.backgroundColor = "");

            // make current fieldset green
            curFieldset.style.backgroundColor = "#93C572";

            // store the seat info div that is child of the current fieldset
            CURR_SEAT_DIV = curFieldset.querySelector(".seat-info");
        })
    );
}

function setUpSeatValidation(passengerFieldsets) {
    const showPricingBtn = document.getElementById('show-pricing-info-button');
    showPricingBtn.addEventListener('click', (e) => {

        // removePricingInfo();
        let isAllValid = true;

        // loop through the fieldsets and get the selected seat
        for (const fs of passengerFieldsets) {
            const seat = fs.querySelector('.seat-info');
            // if the seat is invalid break
            if (seat.innerText === "--") {
                isAllValid = false;
                break;
            }
        }

        const errDiv = document.getElementById('show-pricing-info-button-error-message');

        if (isAllValid) {
            clearError(errDiv);
            // showPricingBtn.style.display = "none";
            const distance = getDistance(AIR_INFO1['latitude'], AIR_INFO1['longitude'], AIR_INFO2['latitude'], AIR_INFO2['longitude']);
            const seatCostTable = getSeatCostTable();
            const fee = getFee(AIR_INFO1['fee'], AIR_INFO2['fee']);
            const flightCost = getFlightCost(distance);

            const tickets = setTickets(passengerFieldsets, seatCostTable, fee, flightCost);

            addPricingInfo(DEPARTURE_AIRPORT, DESTINATION_AIRPORT, DATE, tickets);
            showPricingInfo();
            showBookTicketsBtn();
        } else {
            showError(errDiv, "You must select at least 1 seat for each passenger");
        }
    });

}

/**
 * 
 * Sets the tickets to be booked
 * 
 * Tickets is an array of objects
 * Each object contains information about the customer to whom the ticket belongs
 * If seatCostTable, fee and flightCost have been passed as parameters each object also contains
 * pricing data current object (ticket)
 * 
 * The format of each ticket is as follows:
 * {
 *  "name":     {string} name,
 *  "surname":  {string} surname,
 *  "seat":     {string} seat
 * }
 * 
 * 
 * If seatCostTable, fee and flightCost have been passed as parameters the format is the following:
 * {
 *  "name":     {string} name,
 *  "surname":  {string} surname,
 *  "seat":     {string} seat code,
 *  "seatCost": {string} seat cost,
 *  "total":    {string} total cost 
 * }
 * 
 * @param {HTMLFieldSetElement} passengerFieldsets the elements containing, name surname and set info for each passenger 
 * @param {Object} seatCostTable contains information about the pricing for each seat (can be omm)
 * @param {number} fee the fee for the flight between 2 airports
 * @param {number} flightCost the cost of the flight between 2 airtports
 * @returns 
 */
function setTickets(passengerFieldsets, seatCostTable = null, fee = null, flightCost = null) {
    let tickets = [];
    const names = [];
    const surnames = [];
    const seats = [];
    // get info about each passenger
    passengerFieldsets.forEach((fs) => {
        const name = fs.querySelector('.name').value;
        const surname = fs.querySelector('.surname').value;
        const seat = fs.querySelector('.seat-info').innerText;

        // the current ticket
        const current = {
            "name": name,
            "surname": surname,
            "seat": seat,
        };

        // only if seatCostTable, fee, flightCost have been supplied add pricing data to the current ticket
        // otherwise only data about the name, surname and seat is written on the ticket
        if (seatCostTable !== null && fee !== null && flightCost !== null) {
            // take the row number from the seat
            const number = parseInt(seat.split('-')[1]);
            // set the cost for each seat
            if (number === 1 || number === 11 || number === 12) {
                current["seatCost"] = seatCostTable['leg'];
            }
            else if (number > 1 && number < 11) {
                current["seatCost"] = seatCostTable['front'];
            }
            else {
                current["seatCost"] = seatCostTable['other'];
            }
            // set the total for the current ticket
            current['total'] = fee + flightCost + parseFloat(current["seatCost"]);
        }

        tickets.push(current);
    });

    return tickets;
}

function addPricingInfo(depAirport, destAirport, date, tickets) {

    let total = 0;
    for (const current in tickets) {
        total += parseFloat(tickets[current]['total']);
        // total += ticket['total'];
    }

    const table = document.getElementById('passenger-info-table');
    const headerRow = document.getElementById('passenger-info-header-row');
    // in case the user wants to re-select seats, empty the previous results

    table.innerHTML = "";
    table.appendChild(headerRow);

    // get the columns for the flight information
    const depCol = document.getElementById('departure-airport')
    const destCol = document.getElementById('destination-airport');
    const depDateCol = document.getElementById('departure-date');
    const costCol = document.getElementById('total-cost');

    // fill the columns with the flight information
    depCol.innerText = depAirport;
    destCol.innerText = destAirport;
    depDateCol.innerText = date;
    costCol.innerText = total;

    for (const i in tickets) {
        const ticket = tickets[i];
        const row = document.createElement('tr');
        for (const field in ticket) {
            const col = document.createElement('td');
            col.innerText = ticket[field];
            row.appendChild(col);
        }
        table.appendChild(row);
    }
}

function getFee(fee1, fee2) {
    return Math.round((fee1 + fee2) * 100) / 100;
    // return parseFloat(fee1 + fee2).toFixed(2);
}

function getFlightCost(distance) {
    return Math.round((distance / 10) * 100) / 100;
    //    return parseFloat((distance / 10).toFixed(2));
}

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

function getSeatCostTable() {
    return {
        "leg": 20,
        "front": 10,
        "other": 0
    };
}

function hidePricingInfo() {
    const infDiv = document.getElementById('pricing-info');
    infDiv.style.display = "none";
}

function showPricingInfo() {
    const infDiv = document.getElementById('pricing-info');
    infDiv.style.display = "flex";
}

function showBookTicketsBtn() {
    const btn = document.getElementById("book-tickets-btn");
    btn.style.display = "flow-root";
}

function hideBookTicketsBtn() {
    const btn = document.getElementById("book-tickets-btn");
    btn.style.display = "none";
}
