import { validateSubmitTime, validateRealTime } from "../validationManager.js";
import { showMessage, clearError, showError, showRedirectMessage } from "../displayMessages.js";
import { getTrips } from "./getTrips.js";

//TODO remove later
// const USERNAME = "giog";    // the username must be taken from the session variable
const USERNAME = "Dim";

const table = document.createElement('table');
const infoDiv = document.getElementById('trips-info');
const values = {"username": USERNAME};

// fetch db api for trips
const trips = await getTrips(values, BASE_URL);


// const airportInfo = await getAirportInfo(airport_codes, BASE_URL);

if (trips === null) {
    const h2 = document.createElement('h2');
    h2.innerText = "No trips to show";
    // return;
}



for (const trip of trips) {

    const tripRowHeader = document.createElement('tr');
    const tripRow = document.createElement('tr');       // constains info about the trip, e.x. the destination

    const passengerRowHeader = document.createElement('tr');
    const passengerRow = document.createElement('tr');  // contains info about the passengers

    addTripRowHeaders(tripRowHeader);
    addTripRowValues(tripRow, trip['flight_info']['departure_airport'], trip['flight_info']['destination_airport'], trip['flight_info']['date'])

    addPassengerRowHeaders(passengerRowHeader);

    table.appendChild(tripRowHeader);
    table.appendChild(tripRow);
    table.appendChild(passengerRowHeader);
}
const main = document.querySelector('main');
main.appendChild(table);

/**
 *  Adds <th> elements to the given <tr> element for:
 * - The departure airport
 * - The destination airport 
 * - The day of the flights
 * - The airport fee
 * - The flight cost
 * 
 * IMPORTANT 
 * the order by which the <th> elements are added 
 * should be the same as the order by which the td items are added later
 * 
 * 
 * @param {HTMLTableRowElement} row - the row where the <th> elements for the trips are to be added 
 */
function addTripRowHeaders(row) {
    const departureHeading = getTableHeader("Departure");
    const destinationHeading = getTableHeader("Destination");
    const dateHeading = getTableHeader("Date");
    const fee = getTableHeader("Airport Fee");
    const flighCost = getTableHeader("Flight Cost");
    
    row.appendChild(departureHeading);
    row.appendChild(destinationHeading);
    row.appendChild(dateHeading);
    row.appendChild(fee);
    row.appendChild(flighCost);
}

/**
 * Adds <td> elements to the given <tr> element for:
 * - The departure airport
 * - The destination airport 
 * - The day of the flights
 * 
 * IMPORTANT 
 * the order by which the <td> elements are added 
 * should be the same as the order by which the <th> elements where added before
 * 
 * @param {HTMLTableRowElement} row - the row where the <td> elements for the trip are to be added
 * @param {string} depAirportValue - string containing the value for the departure airport
 * @param {string} destAirportValue - string containing the value for the destination airport
 * @param {string} dateValue - string containing the value for the date
 */
function addTripRowValues(row, depAirportValue, destAirportValue, dateValue) {
    // the rows must be added at the same order as the corresponding row headers
    const depAirport = getTableDataCell(depAirportValue);
    const destAirport = getTableDataCell(destAirportValue);
    const date = getTableDataCell(dateValue);

    row.appendChild(depAirport);
    row.appendChild(destAirport);
    row.appendChild(date);
}

function addPassengerRowHeaders(row) {
    const nameHeading = getTableHeader("Name");
    const surnameHeading = getTableHeader("Surname");
    const seatHeading = getTableHeader("Seat");
    const seatPriceHeading = getTableHeader("Seat Cost");
    const costHeading = getTableHeader("Total Cost");

    row.appendChild(nameHeading);
    row.appendChild(surnameHeading);
    row.appendChild(seatHeading);
    row.appendChild(seatPriceHeading);
   
    row.appendChild(costHeading);
}

function getTableHeader(text) {
    const th = document.createElement('th');
    th.innerText = text;
    return th;
}

function getTableDataCell(text) {
    const td = document.createElement('td');
    td.innerText = text;
    return td;
}

function setPricing() {
     
}


/* <table id="passenger-trips-header-row">
    <tr>
        <th>Departure</th>
        <th>Destination</th>
        <th>Date</th>
        <th>Cost</th>
        <th>Surname</th>
        <th>Seat</th>
        <th>Seat Price</th>
        <th>Ticket Price</th>
        <tr>
            <td id="departure-airport"></td>
            <td id="destination-airport"></td>
            <td id="departure-date"></td>
            <td id="total-cost"></td>
            <td id="passenger-surname"></td>
            <td id="passenger-seat"></td>
            <td id="paassenger-seat-price"></td>
            <td id="passenger-ticket-price"></td>
        </tr>
</table> */
