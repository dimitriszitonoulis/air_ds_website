import { validateSubmitTime, validateRealTime } from "../validationManager.js";
import { clearError, showError } from "../displayMessages.js";
import { getTrips } from "./getTrips.js";
import { getAirportInfo } from "../booking/getBookingInfo.js";
import { cancelTrip } from "./cancelTrip.js";

//TODO remove later
// const USERNAME = "giog";    // the username must be taken from the session variable
const USERNAME = "Dim";


await main()

/**
 * Fetch the api to check if the passenger has any trips
 * If not:
 * - append h2 item saying there are no trips
 * - return
 * If they do:
 * - create a table element
 * - add a row containing the headers for the trip information
 * - add a row containing the values for the trip information
 * - add a row containing the headers for the passengers information
 * - add a row for each passenger containing the passenger's information
 * - append all rows to the table
 * - append the table to main
 */
async function main() {
    const values = {"username": USERNAME};
    let selectedTrip = null;
    let selectedDiv = null;
    // button to cancel the flight
    const cancelBtn = document.createElement('button');
    cancelBtn.id = "delete-btn";
    cancelBtn.innerText = "Cancel trip";
    // div that shows  message in case the flight is not cancelled
    const errorMessageDiv = document.createElement('div');
    errorMessageDiv.className = "error-message";
    errorMessageDiv.id = "cancel-trip-error-message";

    // fetch api for trips
    const trips = await getTrips(values, BASE_URL);
    const mainElement = document.querySelector('main');

    // if the passenger has no trips
    if (trips === null) {
        const h2 = document.createElement('h2');
        h2.innerText = "No trips to show";
        mainElement.appendChild(h2);
        return;
    }

    // adds table with the trips made by the customer
    const tripDivs = await addTripTables(trips, mainElement);
    
    tripDivs.forEach(div => {
        div.addEventListener('click', async() => {
            //save the previously selected div
            const previousSelectedDiv = selectedDiv;

            selectedDiv = div;
            div.style.backgroundColor = "royalblue";
            selectedTrip = {
                "dep_code": div.querySelector(".dep-code").innerText,
                "dest_code": div.querySelector(".dest-code").innerText,
                "dep_date": div.querySelector(".dep-date").innerText,
                "username": USERNAME
            }
            div.appendChild(cancelBtn);
            div.appendChild(errorMessageDiv);           


            // if no div was chosen previously
            // happens at the beggining where no div is selected initially
            if (previousSelectedDiv === null) return;

            previousSelectedDiv.style.backgroundColor = "";

        });
    });


    // if the trip is actually cancelled remove it from the tables 
    cancelBtn.addEventListener('click', async() => {
        const isCancelled = await cancelTrip(selectedTrip, BASE_URL);
        const errorMessageDiv = document.getElementById("cancel-trip-error-message");
        if (isCancelled) {
            clearError(errorMessageDiv);
            location.reload();
        } else {
            showError(errorMessageDiv, "You cannot cancel a trip less than 30 days away");
        }
    })
}

//TODO delete counter
// TODo add documentation
async function addTripTables(trips, mainElement) {
    let counter = 0;
    const tripDivs = [];

    for (const trip of trips) {
        const table = document.createElement('table');
        const tripDiv = document.createElement('div');
        tripDiv.className = "trip-info";

        const depAirport = trip['flight_info']['departure_airport'];
        const destAirport = trip['flight_info']['destination_airport'];
        const date = trip['flight_info']['date'];
        const passengers = trip['passengers'];

        // get the codes for the departure and destination airport
        const airport_codes = {
            "dep_code": depAirport,
            "dest_code": destAirport
        };
        // for each airport get: latitude, longitude and fee.
        const airportInfo = await getAirportInfo(airport_codes, BASE_URL);
        const air_info1 = airportInfo[0];
        const air_info2 = airportInfo[1];

        // get the fee and the cost of the flight
        const distance = getDistance(   air_info1['latitude'], air_info1['longitude'],
                                        air_info2['latitude'], air_info2['longitude']
        );
        const fee = getFee(air_info1['fee'], air_info2['fee']);
        const flightCost = getFlightCost(distance);

        // create elements for the rows of the table
        // row for the trip header and information
        const tripRowHeader = document.createElement('tr');
        const tripRow = document.createElement('tr');       // constains info about the trip, e.x. the destination

        // row for the passengers header and information
        const passengerRowHeader = document.createElement('tr');
        let passengerRow = document.createElement('tr');  // contains info about the passengers
        
        addTripRowHeaders(tripRowHeader);
        addTripRowValues(tripRow, depAirport, destAirport, date, fee, flightCost)
        
        addPassengerRowHeaders(passengerRowHeader);
        table.appendChild(tripRowHeader);
        table.appendChild(tripRow);
        table.appendChild(passengerRowHeader);
        
        for (const passenger of passengers) {
            passengerRow = document.createElement('tr');
            addPassengerRowValues(passengerRow, passenger, fee, flightCost);
            table.appendChild(passengerRow);
        }
        tripDiv.appendChild(table)
        mainElement.appendChild(tripDiv);
        tripDivs.push(tripDiv);

        if (counter > 5) break;
        counter++;
    }

    return tripDivs;
}

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
function addTripRowValues(row, depAirportValue, destAirportValue, dateValue, feeValue, flightCostValue) {
    // the rows must be added at the same order as the corresponding row headers
    const depAirport = getTableDataCell(depAirportValue, "dep-code");
    const destAirport = getTableDataCell(destAirportValue, "dest-code");
    const date = getTableDataCell(dateValue, "dep-date");
    const fee = getTableDataCell(feeValue);
    const flightCost = getTableDataCell(flightCostValue);

    row.appendChild(depAirport);
    row.appendChild(destAirport);
    row.appendChild(date);
    row.appendChild(fee);
    row.appendChild(flightCost);
}

/**
 *  Adds <th> elements to the given <tr> element for:
 * - Passenger name
 * - Passenger surnname 
 * - Passenger Seat
 * - Passenger seat cost
 * - Passenger Total cost
 *
 * !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!! 
 *                                 IMPORTANT 
 * the order by which the <th> elements are added 
 * should be the same as the order by which the td items are added later
 * !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!! 
 * 
 * @param {HTMLTableRowElement} row - the row where the <th> elements for the passengers are to be added 
 */
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

/**
 * Adds td elements to tr element for:
 * - Passenger name
 * - Passenger surnname 
 * - Passenger Seat
 * - Passenger seat cost
 * - Passenger Total cost
 * 
 * !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!! 
 *                                 IMPORTANT 
 * the order by which the <td> elements are added 
 * should be the same as the order by which the <th> elements where added before
 * !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!! 
 * 
 * @param {HTMLTableRowElement} row 
 * @param {object} passenger -  contains the info about the passenger, like:
 * {
 *  "name": "Dim",
 *  "surname": "Diz",
 *  "seat": "E-12",
 *  "price": "580.12"
*  }
 * @param {number} feeValue - the value of the fee for the flights between the 2 airports.
 * @param {number} flightCostValue - the value of the cost of the flight.
 */
function addPassengerRowValues(row, passenger, feeValue, flightCostValue) {
    const name = getTableDataCell(passenger['name']);
    const surname = getTableDataCell(passenger['surname']);
    const seat = getTableDataCell(passenger['seat']);
    
    //get the cost for the seat
    const seatCostValue = getSeatCost(passenger['seat']);
    const seatPrice = getTableDataCell(seatCostValue);
    
    // set the total for the current passenger
    const costValue = feeValue + flightCostValue + parseFloat(seatCostValue);
    const cost = getTableDataCell(costValue);

    row.appendChild(name.cloneNode(true));
    row.appendChild(surname.cloneNode(true));
    row.appendChild(seat.cloneNode(true));
    row.appendChild(seatPrice.cloneNode(true));
    row.appendChild(cost.cloneNode(true));
}

/**
 * @param {string} text 
 * @returns {HTMLTableHeaderCellElement} - a table header cell where innerText = the value of the input value
 */
function getTableHeader(text) {
    const th = document.createElement('th');
    th.innerText = text;
    return th;
}


/**
 * @param {string} text 
 * @returns {HTMLTableDataCellElement} - a table data cell where innerText = the value of the input value
 */
function getTableDataCell(text, className=null) {
    const td = document.createElement('td');
    if(className) td.className = className;
    td.innerText = text;
    return td;
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

function getSeatCost(seat) {
    const seatCostTable = getSeatCostTable();

    // take the row number from the seat
    const number = parseInt(seat.split('-')[1]);
    // set the cost for each seat
    if (number === 1 || number === 11 || number === 12) {
        return seatCostTable['leg'];
    }
    if (number > 1 && number < 11) {
        return seatCostTable['front'];
    }
    return seatCostTable['other'];
}

function getSeatCostTable() {
    return {
        "leg": 20,
        "front": 10,
        "other": 0
    };
}
