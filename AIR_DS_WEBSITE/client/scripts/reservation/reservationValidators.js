import { showError, clearError } from '../displayMessages.js'

// TODO must be fixed to validate what was fetched from the db
export function isDateValid(tripDateElement, errMessageDiv) {
    const tripDate = tripDateElement.value;
    // every time a change is made calculate the current time and the time given by the element 
    let currentDate = new Date();
    let tripDateValue = new Date(tripDate);

    if (!tripDate) {
        showError(errMessageDiv, "This field is required");
        return false;
    }

    // get the difference of the 2 dates in milliseconds
    // the order of the 2 dates in the subtraction MATTERS
    let difference = tripDateValue.getTime() - currentDate.getTime();
    const minuteInMilliseconds = 60000;

    // if there is less than 1 minute between the departure time and now
    if (difference < minuteInMilliseconds) {
        showError(errMessageDiv, "You can not buy tickets 1 min before the designated takeoff")
        return false;
    }

    // all is good
    clearError(errMessageDiv);
    return true;
}

export function isTicketNumberValid(numberOfTicketsElement, errMessageDiv) {
    let ticketNumber = numberOfTicketsElement.value;
    // is input empty
    if (!ticketNumber) {
        showError(errMessageDiv, "Field must not be empty");
        return false;
    }

    // IN ORDER FOR THIS TO WORK THE INPUT TYPE IN THE ELEMENT MUST BE TEXT!

    // is input number?
    if (!isNumber(ticketNumber)) {
        showError(errMessageDiv, "You must enter a number")
        return false;
    }
    // is number >= 1
    if (ticketNumber < 1) {
        showError(errMessageDiv, "You must buy at least 1 ticket")
        return false;
    }

    // each plane has 186 seats
    if (ticketNumber > 186) {
        showError(errMessageDiv, "You cannot buy more than 186 tickets per flight.")
        return false;
    }

    // all is good
    clearError(errMessageDiv);
    return true;
}

/**
 * Summary of isAirportValidSubmitTime
 * Checks if the airports are valid at submit time
 * In order for the airports to be valid for reat time validation they must:
 *  - Not have the same value
 * 
 * If the airports are not valid an error message is shown
 * 
 * @param {Array} airportElements - array of HTML elements for the airport selection
 * @param {*} errMessageDivs - array of HTML elements containing the error message divs of the airport selection elements
 * @returns {boolean} - true if the airport fields are valid, otherwise false
 */
export function isAiportValidRealTime(airportElements, errMessageDivs) {
    const aiportElementsLength = airportElements.length;

    for (let current = 0; current < aiportElementsLength; current++) {
        // there are 2 selection element for the airport codes
        // if currentAiport = 0, then (currentAiport + 1) % aiportElementsLength = (0 + 1) % 2 = 1
        // if currentAiport = 1, then (currentAiport + 1) % aiportElementsLength = (1 + 1) % 2 = 0
        const other = (current + 1) % aiportElementsLength

        // save the airport values 
        const currentAirport = airportElements[current].value
        const otherAirport = airportElements[other].value

        // save the error message divs
        const currentErrMessageDiv = errMessageDivs[current];
        const otherErrMessageDiv = errMessageDivs[other];

        // compare the value of the 2 airport <select> elements
        // if they are the same show error message ON BOTH
        if (currentAirport === otherAirport && currentAirport !== '-') {
            // add the warning inside the div
            for (let errMessageDiv of errMessageDivs) {
                showError(errMessageDiv, "The departure and the destination airport must be different")
            }
            return false;
        }      
    }

    // airport elements are different => clear error messages
    for (let j = 0; j < errMessageDivs.length; j++)
        clearError(errMessageDivs[j]);
    return true;
}

/**
 * Summary of isAirportValidSubmitTime
 * 
 * Checks if the airports are valid at submit time
 * In order for the airports to be valid for submit time validation they must:
 *  - Be different than "-"
 *  - Not have the same value
 * 
 * If the airports are not valid an error message is shown
 *
 * @param {Array} airportElements - array of HTML elements for the airport selection
 * @param {*} errMessageDivs - array of HTML elements containing the error message divs of the airport selection elements
 * @returns {boolean} - true if the airport fields are valid, otherwise false
 */
export function isAirportValidSubmitTime(airportElements, errMessageDivs) {
    const aiportElementsLength = airportElements.length;

    for (let current = 0; current < aiportElementsLength; current++) {
        // there are 2 selection element for the airport codes
        // if currentAiport = 0, then (currentAiport + 1) % aiportElementsLength = (0 + 1) % 2 = 1
        // if currentAiport = 1, then (currentAiport + 1) % aiportElementsLength = (1 + 1) % 2 = 0
        const other = (current + 1) % aiportElementsLength

        // save the airport values 
        const currentAirport = airportElements[current].value
        const otherAirport = airportElements[other].value

        // save the error message divs
        const currentErrMessageDiv = errMessageDivs[current];
        const otherErrMessageDiv = errMessageDivs[other];

        // both aiports must not be: "-" at the same time
        if (currentAirport === '-' && otherAirport === "-") {
            for (const errMessageDiv of errMessageDivs) {
                showError(errMessageDiv, "You must choose an airport");
            }
            return false;
        }

        // current airport must not be: "-"
        if(currentAirport === "-") {
            showError(currentErrMessageDiv, "You must choose an airport");
            return false;
        }

        // compare the value of the 2 airport <select> elements
        // if they are the same show error message ON BOTH
        if (currentAirport === otherAirport) {
            // add the warning inside the div
            for (let errMessageDiv of errMessageDivs) {
                showError(errMessageDiv, "The departure and the destination airport must be different")
            }
            return false;
        }      
    }

    // airport elements are different => clear error messages
    for (let j = 0; j < errMessageDivs.length; j++)
        clearError(errMessageDivs[j]);
    return true;
}

/**
 * Summary of isAirportValidNoErrors
 * Checks if the airports are valid at submit time
 * In order for the airports to be valid for submit time validation they must:
 *  - Be different than "-"
 *  - Not have the same value
 *
 * If the airports are not valid NOTHING is shown
 * 
 * @param {Array} airportElements - array of HTML elements for the airport selection
 * @param {*} errMessageDivs - array of HTML elements containing the error message divs of the airport selection elements
 * @returns {boolean} - true if the airport fields are valid, otherwise false
 */
export function isAirportsValidNoMessages(airportElements) {
    const aiportElementsLength = airportElements.length;

    // TODO check if the early return cause problems with the validation
    // add event listeners on both airport select elements
    for (let current = 0; current < aiportElementsLength; current++) {
        // there are 2 selection element for the airport codes
        // if currentAiport = 0, then (currentAiport + 1) % aiportElementsLength = (0 + 1) % 2 = 1
        // if currentAiport = 1, then (currentAiport + 1) % aiportElementsLength = (1 + 1) % 2 = 0
        const other = (current + 1) % aiportElementsLength

        // save the airport values 
        const currentAirport = airportElements[current].value
        const otherAirport = airportElements[other].value

        // save the error message divs
        const currentErrMessageDiv = errMessageDivs[current];
        const otherErrMessageDiv = errMessageDivs[other];

        // both aiports must not be: "-" at the same time
        if (currentAirport === '-' && otherAirport === "-")return false;

        // current airport must not be: "-"
        if(currentAirport === "-") return false;

        // compare the value of the 2 airport <select> elements
        // if they are the same show error message ON BOTH
        if (currentAirport === otherAirport) return false;
    }

    return true;
}


function isNumber(i) {
    const regex = /^-?[0-9]+$/; //match all integers
    //returns the an array containing i if it matches else null 
    return i.match(regex);
}