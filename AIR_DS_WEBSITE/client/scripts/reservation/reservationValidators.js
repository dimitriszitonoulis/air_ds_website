import {showError, clearError} from '../displayMessages.js' 

// TODO must be fixed to validate what was fetched from the db
export function isDateValid(tripDate, errMessageDiv) {
    // every time a change is made calculate the current time and the time given by the element 
    let currentDate = new Date();
    let tripDateValue = new Date(tripDate.value);

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

// function checkAirportCodes() {
//     const airportElements = document.getElementsByClassName('airport-selection');
//     const aiportElementsLength = airportElements.length;
//     const errMessageDivs = [
//         document.getElementById('departure-airport-error-message'),
//         document.getElementById('destination-aiport-error-message')
//     ]

//     // add event listeners on both airport select elements
//     for (let currentAirport = 0; currentAirport < aiportElementsLength; currentAirport++) {
//         // there are 2 selection element for the airport codes
//         // if currentAiport = 0, then (currentAiport + 1) % aiportElementsLength = (0 + 1) % 2 = 1
//         // if currentAiport = 1, then (currentAiport + 1) % aiportElementsLength = (1 + 1) % 2 = 0
//         let otherAirport = (currentAirport + 1) % aiportElementsLength

//         // for real time evaluation
//         airportElements[currentAirport].addEventListener('change', (e) => {
//             areAirportsValid(airportElements, currentAirport, otherAirport, errMessageDivs);
//         });
//     }

//     // for submit time evaluation
//     return areAirportsValid(airportElements, 0, 1, errMessageDivs);
// }

// /**
//  * Function that checks if the 2 aiport <select> elements have valid values.
//  * Valid values for the <select> elements are:
//  *      Every value except: --
//  *      The 2 aiports must have different values
//  * 
//  * If they have the same value then an error message is shown (using the error message divs), and false is returned.
//  * Otherwise, no message error message appears and true is returned.
//  * 
//  * ATTENTION:
//  * The order of the 2 index parameters does not matter.
//  * ex:
//  * The result of:
//  *      currentAirportIndex = 0 and otherAirportIndex = 1
//  * is the same of:
//  *      currentAirportIndex = 1 and otherAirportIndex = 0
//  * 
//  * @param {object} airportElements 
//  * @param {number} currentAirportIndex - The index of the current aiport <select> element
//  * @param {number} otherAirportIndex - The index of the other aiport <select> element 
//  * @param {HTMLCollection} errMessageDivs - Collection of the divs used to display error messages for the airports
//  * @returns {boolean} - true if the 2 <select> elements have different values, otherwise false
//  */
// function areAirportsValid(airportElements, currentAirportIndex, otherAirportIndex, errMessageDivs) {
//     // save the airport values 
//     const currentAirport = airportElements[currentAirportIndex].value
//     const otherAirport = airportElements[otherAirportIndex].value

//     // get the parent of the current <select> Element (fieldset)
//     let fieldset = airportElements[currentAirportIndex].parentElement;

//     //check if the strings are empty
//     if (!currentAirport || !otherAirport) {
//         for (airport of airportElements) {
//             airport.title = "You must choose an airport";
//         }
//         return false;
//     }

//     // check that the aiports are not: "-"
//     if (currentAirport === '-' || otherAirport === "-") {
//         for (airport of airportElements) {
//             airport.title = "You must choose an airport";
//         }
//         return false;
//     }

//     // compare the value of the 2 aiport <select> elements
//     // if they are the same show error message ON BOTH
//     if (currentAirport === otherAirport) {
//         // add the warning inside the div
//         for (let errMessageDiv of errMessageDivs) {
//             showError(errMessageDiv, "the departure and the destination airport must be different")
//         }
//         return false;
//     }

//     // airport elements are different => clear error messages
//     for (let j = 0; j < errMessageDivs.length; j++)
//         clearError(errMessageDivs[j]);
//     return true;
// }



// ------------------------------------------------------------
// MY functions

export function isAiportValid(airportElements, errMessageDivs) {
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

        // both aiports must not be: "-"
        if (currentAirport === '-' && otherAirport === "-") {
            for (const errMessageDiv of errMessageDivs) {
                showError(errMessageDiv, "You must choose an airport");
            }
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

        // airport elements are different => clear error messages
        for (let j = 0; j < errMessageDivs.length; j++)
            clearError(errMessageDivs[j]);
        return true;
    }
    // return areAirportsValid(airportElements, 0, 1, errMessageDivs);
}

function isNumber(i) {
    const regex = /^-?[0-9]+$/; //match all integers
    //returns the an array containing i if it matches else null 
    return i.match(regex);
}