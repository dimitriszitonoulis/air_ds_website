import { validateSubmitTime, validateRealTime } from "../validationManager.js";
import { showMessage, clearError, showError, showRedirectMessage } from "../displayMessages.js";
import { getTrips } from "./getTrips.js";
import { createElement } from "react";

//TODO remove later
const USERNAME = "giog";    // the username must be taken from the session variable

const infoDiv = document.getElementById('trips-info');
const values = {"username": USERNAME};

const trips = await getTrips(values, BASE_URL);

if (trips === null) {
    const h2 = document.createElement('h2');
    h2.innerText = "No trips to show";
    // return;
}


for (const trip of trips) {
    const table = document.createElement('table');

    const tripRowHeader = document.createElement('tr');
    const tripRow = document.createElement('tr');       // constains info about the trip like the destination

    const passengerHeader = document.createElement('tr');
    const passengerRow = document.createElement('tr');  // contains info about the passengers

    // const departureHeading = document.createElement('th');
    // const destHeading = document.createElement('th');

    // const dateHeading = document.createElement('th');
    // const costHeading = document.createElement('th');
    // const nameHeading = document.createElement('th');
    // const surnameHeading = document.createElement('th');
    // const seatHeading = document.createElement('th');
    // const seatPriceHeading = document.createElement('th');








    // const departureHeading = getTableHeader("Departure");
    // const destinationHeading = getTableHeader("Destination");
    // const dateHeading = getTableHeader("Date");

    // const nameHeading = getTableHeader("Name");
    // const surnameHeading = getTableHeader("Surname");
    // const seatHeading = getTableHeader("Seat");
    // const seatPriceHeading = getTableHeader("Seat Cost");
    // const costHeading = getTableHeader("Total Cost");

    // passengerHeader.appendChild(departureHeading);
    // passengerHeader.appendChild(destinationHeader);
    // passengerHeader.appendChild(dateHeading);







}

function addTripRowHeaders(row) {
    const departureHeading = getTableHeader("Departure");
    const destinationHeading = getTableHeader("Destination");
    const dateHeading = getTableHeader("Date");

    row.appendChild(departureHeading);
    row.appendChild(destinationHeader);
    row.appendChild(dateHeading);

}

function addTripRowValues(row, trips) {
    
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
