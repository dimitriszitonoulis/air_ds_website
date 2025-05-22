import { addFullNames, fields } from "./bookingFields.js";
import { validateRealTime, validateSubmitTime } from '../validationManager.js';
import { isNameValid } from "./bookingValidators.js";
import { showError } from "../displayMessages.js";
import { getFullName, getTakenSeats } from "./getBookingInfo.js";
import { createSeatMap } from "./createSeatMap.js";
import { addInfoFieldSets } from "./showNameForm.js";

// TODO delete later
const TICKET_NUMBER = 2;
const USERNAME = "giog";    // the username must be taken from the session variable
const DEPARTURE_AIRPORT = "ATH";
const DESTINATION_AIRPORT = "BRU";
const DATE = "2025-05-25 00:00:00";


// Take the name and surname of the registered user from the db
const fullName = await getFullName({'username': USERNAME}, BASE_URL);






// fill the registered user's information
const registeredUserNameField = document.getElementById('name-0');
const registeredUserSurnameField = document.getElementById('surname-0');
registeredUserNameField.value = fullName['name'];
registeredUserSurnameField.value = fullName['surname'];

const seatForm = document.getElementById('seat-form');
      
// create the seatmap
const values = {
    "dep_code": DEPARTURE_AIRPORT,
    "dest_code": DESTINATION_AIRPORT,
    "dep_date": DATE
}
let takenSeats = await getTakenSeats(values, BASE_URL);     // get taken seats 
createSeatMap(takenSeats);                                  // pass them to seat map function 
const planeBody = document.getElementById('plane-body');    // get the plane body div

// add  fielsets for the rest of the users
addInfoFieldSets(TICKET_NUMBER);

// fill the fields with information about the HTML elements containing 
addFullNames(TICKET_NUMBER, fields);

const bookingFields = {...fields};

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

// !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
// TODO delete later

//  const values = {
//             "dep_code": DEPARTURE_AIRPORT,
//             "dest_code": DESTINATION_AIRPORT,
//             "dep_date": DATE
//         }

//         let takenSeats = await getTakenSeats(values, BASE_URL);

//         createSeatMap(takenSeats);

// !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!

let isAllValid = false;

chooseSeatsBtn.addEventListener('click', async (e) => {
    // if the button is clicked without any field being checked do nothing 
    // e.preventDefault();
    
    // const isAllValid = validateSubmitTime(bookingFields);
    isAllValid = validateSubmitTime(bookingFields);

    // TODO uncomment later
    // if (isAllValid) {
    if (true) {
 
        // show seatmap
        planeBody.style.visibility = "visible";

    } else {
        showError(chooseSeatsErrorDiv, "Could not process names");
    }

})


//TODO maybe only add the event listeners if the validation has succeded







// TODO must make validation again for seats
/**
 * make it like: seats: {
 * seat1: asdfasdf
 * ...
 * }
 */







