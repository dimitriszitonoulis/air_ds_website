
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

function checkDate(){
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
            showError(errMessageDiv,"You can not buy tickets 1 min before the designated takeoff")
        }
        else{
            clearError(errMessageDiv,"hidden");
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


// Checks that the codes of the departure and the destination airport are different
function checkAirportCodes() {
    const airportElements = document.getElementsByClassName('airport-selection');
    const aiportElementsLength = airportElements.length;
    const errMessageDivs =  [
        document.getElementById('departure-airport-error-message'),
        document.getElementById('destination-aiport-error-message')
    ]

    // add event listeners on both airport selectio elements
    for (let i = 0; i < aiportElementsLength; i++) {
        airportElements[i].addEventListener('change', (e) => {
            // get the parent of the current <select> Element (fieldset)
            let fieldset = airportElements[i].parentElement;

            // there are 2 selection element for the airport codes
            // if i = 0, then (i + 1) % aiportElementsLength = (0 + 1) % 2 = 1
            // if i = 1, then (i + 1) % aiportElementsLength = (1 + 1) % 2 = 0
            if (airportElements[i].value === airportElements[(i + 1) % aiportElementsLength].value) {
                // add the warning inside the div
                for(let j = 0; j < errMessageDivs.length; j++)
                    showError(errMessageDivs[j], "the departure and the destination airport must be different")
            }
            else {
                for(let j = 0; j < errMessageDivs.length; j++)
                    clearError(errMessageDivs[j])
            }
        });
    }
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



