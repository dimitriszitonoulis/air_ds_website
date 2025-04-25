
const buyTicketBtn = document.getElementById('buy-tickets-button');

buyTicketBtn.addEventListener('click', (e) => {
    errorChecking();
    document.getElementById('form').requestSubmit();
});

function errorChecking() {
    checkTicketNumber();
    checkAirportCodes();
    checkDate();
}

function checkDate() {
    const tripDate = document.getElementById('departure-date');
    const errMessageDiv = document.getElementById('departure-date-error-message');

    tripDate.addEventListener('change', (e) => {
        // every time a change is made calculate the current time and the time given by the element 
        let currentDate = new Date();
        let tripDateValue = new Date(tripDate.value);
        // get the difference of the 2 dates in milliseconds
        // the order of the 2 dates in the subtraction MATTERS
        let difference = tripDateValue.getTime() - currentDate.getTime();

        // console.log(tripDateValue.getTime() +  " - " + currentDate.getTime() + " = " + difference);

        const minuteInMilliseconds = 60000;

        // if there is less than 1 minute between the departure time and now
        if (difference < minuteInMilliseconds) {
            showError(errMessageDiv, "You can not buy tickets 1 min before the designated takeoff")
        }
        else {
            clearError(errMessageDiv, "hidden");
        }
    })
}

// checks if the number of tickets is valid (>= 1)
function checkTicketNumber() {
    const numberOfTicketsElement = document.getElementById('ticket-number');
    const errMessageDiv = document.getElementById("ticket-number-error-message");

    numberOfTicketsElement.addEventListener('change', (e) => {
        let ticketNumber = numberOfTicketsElement.value;
        if (!isNumber(ticketNumber))
            showError(errMessageDiv, "you must enter a number")
        else if (ticketNumber < 1)
            showError(errMessageDiv, "you must buy at least 1 ticket")

        else {
            // in case user first enters text and then number
            // change the innerText to the initial value
            clearError(errMessageDiv);
        }
    })
}


function checkAirportCodes() {
    const airportElements = document.getElementsByClassName('airport-selection');
    const aiportElementsLength = airportElements.length;
    const errMessageDivs = [
        document.getElementById('departure-airport-error-message'),
        document.getElementById('destination-aiport-error-message')
    ]

    // add event listeners on both airport select elements
    for (let currentAirport = 0; currentAirport < aiportElementsLength; currentAirport++) {
        // there are 2 selection element for the airport codes
        // if currentAiport = 0, then (currentAiport + 1) % aiportElementsLength = (0 + 1) % 2 = 1
        // if currentAiport = 1, then (currentAiport + 1) % aiportElementsLength = (1 + 1) % 2 = 0
        let otherAirport = (currentAirport + 1) % aiportElementsLength
        
        // For real time evaluation
        airportElements[currentAirport].addEventListener('change', (e) => {
            areAirportsValid(airportElements, currentAirport, otherAirport, errMessageDivs);
        });
    }
    
    // for submit time evaluation
    return areAirportsValid(airportElements, 0, 1, errMessageDivs);
}

/**
 * Function that checks if the 2 aiport <select> elements have valid values.
 * Valid values for the <select> elements are:
 *      Every value except: --
 *      The 2 aiports must have different values
 * 
 * If they have the same value then an error message is shown (using the error message divs), and false is returned.
 * Otherwise, no message error message appears and true is returned.
 * 
 * ATTENTION:
 * The order of the 2 index parameters does not matter.
 * ex:
 * The result for:
 *      currentAirportIndex = 0 and otherAirportIndex = 1
 * is the same as:
 *      currentAirportIndex = 1 and otherAirportIndex = 0
 * 
 * @param {object} airportElements 
 * @param {number} currentAirportIndex - The index of the current aiport <select> element
 * @param {*} otherAirportIndex - The index of the other aiport <select> element 
 * @returns {boolean} - true if the 2 <select> elements have different values, otherwise false
 */
function areAirportsValid(airportElements, currentAirportIndex, otherAirportIndex, errMessageDivs) {
    // save the airport values 
    const currentAirport = airportElements[currentAirportIndex].value
    const otherAirport = airportElements[otherAirportIndex].value

    // get the parent of the current <select> Element (fieldset)
    let fieldset = airportElements[currentAirportIndex].parentElement;
    
    //check if the strings are empty
    if (!currentAirport || !otherAirport) {
        console.log("currentAiport = " + currentAirport);
        console.log("otherAirport = " + otherAirport);
        return false;
    }

    // compare the value of the 2 aiport <select> elements
    // if they are the same show error message ON BOTH
    if (currentAirport === otherAirport) {
        // add the warning inside the div
        for (let j = 0; j < errMessageDivs.length; j++)
            showError(errMessageDivs[j], "the departure and the destination airport must be different");
        // isValid = false;    // the choice of aiports is not valid
        return false;
    } 

    // airport elements are different
    for (let j = 0; j < errMessageDivs.length; j++)
        clearError(errMessageDivs[j]);

    return true;
}

function showError(element, message) {
    element.innerText = message;
    element.style.visibility = "visible";
}

function clearError(element, message) {
    element.style.visibility = "hidden";
}


function isNumber(i) {
    const regex = /^-?[0-9]+$/; //match all integers
    return i.match(regex);
}


checkTicketNumber();
checkAirportCodes();
checkDate();



