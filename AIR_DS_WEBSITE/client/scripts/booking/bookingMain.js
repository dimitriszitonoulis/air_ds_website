import { addFullNames, fields } from "./bookingFields.js";
import { validateRealTime, validateSubmitTime } from '../validationManager.js';
import { isNameValid } from "./bookingValidators.js";
import { showError } from "../displayMessages.js";
import { getAirportInfo, getFullName, getTakenSeats } from "./getBookingInfo.js";
import { createSeatMap } from "./createSeatMap.js";
import { addInfoFieldSets } from "./showNameForm.js";

// TODO delete later
const TICKET_NUMBER = 2;
const USERNAME = "giog";    // the username must be taken from the session variable
const DEPARTURE_AIRPORT = "ATH";
const DESTINATION_AIRPORT = "BRU";
const DATE = "2025-05-25 00:00:00";


//--------------------------------------------------------------------------------
//                          ADD INFO ABOUT REGISTERED USER
// Take the name and surname of the registered user from the db
const fullName = await getFullName({ 'username': USERNAME }, BASE_URL);

// fill the registered user's information
const registeredUserNameField = document.getElementById('name-0');
const registeredUserSurnameField = document.getElementById('surname-0');
registeredUserNameField.value = fullName['name'];
registeredUserSurnameField.value = fullName['surname'];
//--------------------------------------------------------------------------------


const seatForm = document.getElementById('seat-form');


// ------------------------------------------------------------------------------
//                                  ADD SEAT MAP
// create the seatmap
const values = {
    "dep_code": DEPARTURE_AIRPORT,
    "dest_code": DESTINATION_AIRPORT,
    "dep_date": DATE
};
let takenSeats = await getTakenSeats(values, BASE_URL);     // get taken seats 
createSeatMap(takenSeats);                                  // pass them to seat map function 
const planeBody = document.getElementById('plane-body');    // get the plane body div
const seatmapContainer = document.getElementById('seat-map-container');
// TODO uncomment later
// seatmapContainer.style.display = "none";

// ------------------------------------------------------------------------------


// ------------------------------------------------------------------------------
//                      ADD INFO ABOUT THE REST OF THE PEOPLE

// add  fielsets for the rest of the users
addInfoFieldSets(TICKET_NUMBER);

// fill the fields with information about the HTML elements containing 
addFullNames(TICKET_NUMBER, fields);

const bookingFields = { ...fields };

// assign validator function to names and surnames
// the same validator function is used for the names and surnames
for (const currentField in bookingFields) {
    bookingFields[currentField].validatorFunction = isNameValid;
}

// ------------------------------------------------------------------------------




// only validate names and surnames if the customer chose to buy more that 1 ticket
// if they bought only one ticket then the name and surname have passed validation
// when the customer registered,
// no need to redo check
// if (bookingFields !== null)
validateRealTime(bookingFields);

const chooseSeatsBtn = document.getElementById('choose-seats-button');
const chooseSeatsErrorDiv = document.getElementById('choose-seats-button-error-message');


let isAllValid = false;
isAllValid = true;

chooseSeatsBtn.addEventListener('click', async (e) => {
    // if the button is clicked without any field being checked do nothing 
    // e.preventDefault();

    // const isAllValid = validateSubmitTime(bookingFields);
    // isAllValid = validateSubmitTime(bookingFields); //TODO uncomment later

    if (isAllValid) {

        // show seatmap
        // planeBody.style.visibility = "visible";
        // TODO decide which to hide and which to unhide
        planeBody.style.display = "flex";
        seatmapContainer.style.display = "flex";

    } else {
        showError(chooseSeatsErrorDiv, "Could not process names");
    }

})

//-----------------------------------------------------------------------------------
//                                   SELECT SEAT CODE
// the fielset of the currenly selected passenger
let curSeatDiv = null;
let selectedSeats = [];

//TODO maybe only add the event listeners if the validation has succeded
// use query selector to be able to user for each afterwards
const passengerFieldsets = document.querySelectorAll(".passenger-info");
//select all the seats inside the seat map
const seats = planeBody.querySelectorAll(".seat");

chooseSeat(passengerFieldsets, seats);

function chooseSeat(passengerFieldsets, seats) {
    passengerFieldsets.forEach((curFieldset) =>
        curFieldset.addEventListener('click', (e) => {

            // if the current fieldset is already selected de-select it 
            if (curSeatDiv === curFieldset.querySelector(".seat-info")) {
                curFieldset.style.backgroundColor = "";
                curSeatDiv = null;
                // curUserId = null;
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
            // curSeatDiv = null;
            // curUserId = curFieldset
            // console.log(curUserId);

        })
    );

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
            // curSeatDiv = null;     // otherwise 1 passenger can choose multiple seats
    }));

}

//-----------------------------------------------------------------------------------

//-----------------------------------------------------------------------------------
//                                  SHOW FLIGHT INFO

// get info about distances from the db
const airport_codes = {
    "dep_code": DEPARTURE_AIRPORT,
    "dest_code": DESTINATION_AIRPORT
}

const info = await getAirportInfo(airport_codes, BASE_URL);

const airInfo1 = info[0];
const airInfo2 = info[1];

function degToRad (deg) { return (deg * Math.PI) / 180.0; };

const distance = getDistance(airInfo1['latitude'], airInfo1['longitude'], airInfo2['latitude'], airInfo2['longitude']);

function getDistance(lat1, lon1, lat2, lon2) {
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



const fee = airInfo1['fee'] + airInfo2['fee'];
const flightCost = distance / 10;
const seatCostTable =  { "leg": 20, "front": 10, "other": 0 };

// array associating each passenger with their seat
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

// get seat cost info for each passenger
tickets.forEach((current) => {
    const seat = current['seat'];
    const number = seat.split('-')[1]; // take the row number from the seat

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

let total = 0;
for (const ticket in tickets) {
    total += ticket['total'];
}


// console.log(passSeat);
// console.log(seatCostTable);




//-----------------------------------------------------------------------------------





// TODO must make validation again for seats
/**
 * make it like: seats: {
 * seat1: asdfasdf
 * ...
 * }
 */







