import { addFullNames, fields } from "./bookingFields.js";
import { validateRealTime} from '../validationManager.js';
import { isNameValid } from "./bookingValidators.js";
import { clearError, showError } from "../displayMessages.js";
import { getAirportInfo, getFullName, getTakenSeats } from "./getBookingInfo.js";
import { createSeatMap, hideSeatMap, showSeatMap } from "./createSeatMap.js";
import { addInfoFieldSets, fillUserInfo } from "./showNameForm.js";
import { bookTickets } from "./bookTickets.js";

// TODO delete later
// const TICKET_NUMBER = 2;
// const USERNAME = "Dim";    // the username must be taken from the session variable
// const DEPARTURE_AIRPORT = "ATH";
// const DESTINATION_AIRPORT = "BRU";
// const DATE = "2025-06-26 00:00:00";

// get info about distances from the db
const airport_codes = {
    "dep_code":     DEPARTURE_AIRPORT,
    "dest_code":    DESTINATION_AIRPORT
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

    submitBooking(passengerFieldsets);
}

/**
 * Form the information to be sent to the api and send it
 * 
 * If the tickets are booked redirect to my trips page
 * 
 * @param {HTMLFieldSetElement} passengerFieldsets contains elements from which passenger name, surname and seat can be extracted
 */
async function submitBooking() {
    const bookBtn = document.getElementById("book-tickets-btn");
    const passengerFieldsets = document.querySelectorAll(".passenger-info-fieldset");

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

        console.log(flightInfo);

        const isBooked = await bookTickets(flightInfo, BASE_URL);
        if (isBooked) {
            const redirectUrl = `${BASE_URL}client/pages/my_trips.php`;
            window.location.replace(redirectUrl);
        }
    });

    
}

function setUpPassengers(username, ticketNumber, fields, baseUrl) {
    fillUserInfo(username, baseUrl);    // fill info about the registered user
    addInfoFieldSets(ticketNumber);     // add  fielsets for the rest of the users
    addFullNames(ticketNumber, fields); // fill the fields with information about the HTML elements containing 
}

/**
 * Sets up the seat map
 * 
 * - Create the seat map and add it to the DOM
 * - Select all the seats and add eventListener to them
 * 
 * Seat selection:
 * - A seat can be selected only when a passenger is selected
 * - If a seat is clicked twice it is de-selected
 * - If a seat alredy has a value the previous seat must be de-selected
 * - If a seat is already selected by another passenger do not select it
 *   
 * 
 * @param {string} depAirport   the code of the departure airport
 * @param {string} destAirport  the code of the destination airport
 * @param {string} depDate         the departure date
 */
async function setUpSeatMap(depAirport, destAirport, depDate) {

    await createSeatMap(depAirport, destAirport, depDate);

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
                SELECTED_SEATS.splice(i, 1);                 // remove 1st element from  selectedSeats at the selected index
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

            // if everything is alright select the seat
            seat.style.backgroundColor = "#93C572";
            CURR_SEAT_DIV.innerText = seat.id;
            SELECTED_SEATS.push(seat.id);
        }));

}

/**
 * 
 * Set up the validation for the passenger info
 * 
 * The validation is performed only if the customer chose to buy more than 1 tickets
 * This is because the passenger info is filled automatically based on the account information,
 * and the passenger info has already undergone validation when the passnger registered
 * 
 * When the passenger chooses to select the seats show the seat map and set up the passenger selection
 * 
 * @param {HTMLFieldSetElement} passengerFieldsets contains elements from which passenger name, surname and seat can be extracted
 */
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

/**
 * Sets up the passenger selection
 * 
 * Each passenger can be selected by clicking on their fieldset
 * 
 * Passenger selection:
 * - If a passenger is clicked twice they are de-selected
 * - If a passenger is selected
 *      - it changes color
 *      - the colors from the other passengers get removed
 *      - CURR_SEAT_DIV has the value of the seat div inside the current passenger's fieldset
 * - If no passenger is selected then CURR_SEAT_DIV = null
 * @param {HTMLFieldSetElement} passengerFieldsets contains elements from which passenger name, surname and seat can be extracted
 */
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
            // TODO do not forget to clear all the green colors 

            // if the current fieldset is pressed remove the colors from the others
            passengerFieldsets.forEach((fieldset) => fieldset.style.backgroundColor = "");

            // make current fieldset green
            curFieldset.style.backgroundColor = "#93C572";

            // store the seat info div that is child of the current fieldset
            CURR_SEAT_DIV = curFieldset.querySelector(".seat-info");
        })
    );
}

/**
 * 
 * Set up the validation for the seats
 * 
 * When the pricing button is clicked check the validity of each seat
 * A seat is valid if it has a value other than: "--"
 * 
 * If all the seats are valid:
 * - Calculate the pricing info
 * - Show the pricing table
 * - Show the book tickets button 
 * 
 * 
 * @param {HTMLFieldSetElement} passengerFieldsets contains elements from which passenger name, surname and seat can be extracted
 */
function setUpSeatValidation(passengerFieldsets) {
    const showPricingBtn = document.getElementById('show-pricing-info-button');

    showPricingBtn.addEventListener('click', (e) => {
        const errDiv = document.getElementById('show-pricing-info-button-error-message');
        let isAllValid = true;

        // // TODO add code to deselect all previoulsy selected passengers
        // // if the current fieldset is pressed remove the colors from the others
        // passengerFieldsets.forEach((fieldset) => fieldset.style.backgroundColor = "");
        // CURR_SEAT_DIV = null;

        // loop through the fieldsets and get the selected seat
        for (const fs of passengerFieldsets) {
            const seat = fs.querySelector('.seat-info');
            // if the seat is invalid break
            if (seat.innerText === "--") {
                isAllValid = false;
                break;
            }
        }

        if (isAllValid) {
            clearError(errDiv); // clear previous error messages
            // showPricingBtn.style.display = "none";
            const distance = getDistance(AIR_INFO1['latitude'], AIR_INFO1['longitude'], AIR_INFO2['latitude'], AIR_INFO2['longitude']);
            const seatCostTable = getSeatCostTable();
            const fee = getFee(AIR_INFO1['fee'], AIR_INFO2['fee']);
            const flightCost = getFlightCost(distance);
            const tickets = setTickets(passengerFieldsets, seatCostTable, fee, flightCost);

            addPricingInfo(DEPARTURE_AIRPORT, DESTINATION_AIRPORT, DATE, fee, flightCost, tickets);
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
 * @param {HTMLFieldSetElement} passengerFieldsets  the elements containing, name surname and seat info for each passenger 
 * @param {Object} seatCostTable                    contains information about the pricing for each seat (can be omm)
 * @param {number} fee                              the fee for the flight between 2 airports
 * @param {number} flightCost                       the cost of the flight between 2 airtports
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

/**
 * Adds values and cells to pricing info table
 * First, add info about the flight and the total cost
 * Then loop through the ticket array and add information about each passenger
 * 
 * @param {string}  depAirport the code of the departure airport
 * @param {string} destAirport the code of the destination airport
 * @param {string}        date the departure date
 * @param {number}         fee the fee for the flight between 2 airports
 * @param {number}  flightCost the cost of the flight between 2 airtports
 * @param {Array}      tickets array of objects
 * Each object contains information about
 * - the customer to whom the ticket belongs
 * - pricing data for the customer
 * Each object (ticket) has the following format
 * {
 *  "name":     {string} name,
 *  "surname":  {string} surname,
 *  "seat":     {string} seat code,
 *  "seatCost": {string} seat cost,
 *  "total":    {string} total cost 
 * }
 */
function addPricingInfo(depAirport, destAirport, date, fee, flightCost, tickets) {

    let total = 0;
    for (const current in tickets) {
        total += parseFloat(tickets[current]['total']);
    }

    // const table = document.getElementById('passenger-info-table');
    const table = document.getElementById('pricing-info-table');

    // save the elements of the table
    const airportInfoHeaderRow = document.getElementById('airport-info-header-row');
    const airportInfoValuesRow = document.getElementById('airport-info-values-row');
    const passengerHeaderRow = document.getElementById('passenger-info-header-row');
    const totalCostRow = document.getElementById('total-cost-row');

    // in case the user wants to re-select seats, empty the previous results
    table.innerHTML = "";

    // append the previously saved table elements (header rows etc)
    table.appendChild(airportInfoHeaderRow);
    table.appendChild(airportInfoValuesRow);
    table.appendChild(passengerHeaderRow);
    table.appendChild(totalCostRow);

    // get the columns for the flight information
    const depCol = document.getElementById('departure-airport')
    const destCol = document.getElementById('destination-airport');
    const depDateCol = document.getElementById('departure-date');
    const feeCol = document.getElementById('fee');
    const flightCostCol = document.getElementById('flight-cost');
    const costCol = document.getElementById('total-cost');

    // fill the columns with the flight information
    depCol.innerText = depAirport;
    destCol.innerText = destAirport;
    depDateCol.innerText = date;
    feeCol.innerText = fee;
    flightCostCol.innerText = flightCost;
    costCol.innerText = total;

    for (const i in tickets) {
        const ticket = tickets[i];
        const row = document.createElement('tr');
        for (const field in ticket) {
            const col = document.createElement('td');
            col.innerText = ticket[field];
            row.appendChild(col);
        }
        table.insertBefore(row, totalCostRow);
    }
}

function getFee(fee1, fee2) {
    return Math.round((fee1 + fee2) * 100) / 100;
}

function getFlightCost(distance) {
    return Math.round((distance / 10) * 100) / 100;
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
