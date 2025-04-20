
// const buyTicketBtn = document.addEventListener('click', errorChecking());

function errorChecking() {
    checkTicketNumber();
    checkAirportCodes();
}

// checks if the number of tickets is valid (>= 1)
function checkTicketNumber() {
    const numberOfTicketsElement = document.getElementById('number-of-tickets');
    const numberOfTicketsLabel = document.getElementById('number-of-tickets-label');
    const numberOfTicketsLabelText = numberOfTicketsLabel.innerText;

    numberOfTicketsElement.addEventListener('change', (e) => {
        let ticketNumber = numberOfTicketsElement.value;

        if (!isNumber(ticketNumber))
            numberOfTicketsLabel.innerText = numberOfTicketsLabelText + " (you must enter a number)";
        else if (ticketNumber < 1) {
            numberOfTicketsLabel.innerText = numberOfTicketsLabelText + " (you must by at least 1 ticket)";
        }
        else {
            // in case user first enters text and then number
            // change the innerText to the initial value
            numberOfTicketsLabel.innerText = numberOfTicketsLabelText;
        }
    })
}


// Checks that the codes of the departure and the destination airport are different
function checkAirportCodes() {
    const airportElements = document.getElementsByClassName('airport-code-selection');
    const aiportElementsLength = airportElements.length;
    const airportLabels = document.getElementsByClassName('airport-code-label');
    const airportLabelsText = [
        airportLabels[0].innerText,
        airportLabels[1].innerText
    ];

    for (let i = 0; i < aiportElementsLength; i++) {
        airportElements[i].addEventListener('change', (e) => {
            // get the parent of the current select Element (fieldset)
            let fieldset = airportLabels[i].parentElement;
            
            // get the label inside the current fieldset
            // even though there is only one label inside the fieldset and HTMLCollection is returned.
            // In order to access the label's inner text the first element [0] of the HTMLCollection must be accessed
            let label = fieldset.getElementsByClassName('airport-code-label')[0];
            console.log(label);

            // there are 2 selection element for the airport codes
            // if i = 0, then (i + 1) % aiportElementsLength = (0 + 1) % 2 = 1
            // if i = 1, then (i + 1) % aiportElementsLength = (1 + 1) % 2 = 0
            if (airportElements[i].value === airportElements[(i + 1) % aiportElementsLength].value) {
                // add the warning inside the label
                // TODO maybe place the warning in a new div
                label.innerText = airportLabelsText[i] + " (the departure and the destination airport must be different)";
            }
            else {
                label.innerText = airportLabelsText[i]
            }
        })
    }
}


function isNumber(i) {
    const regex = /^-?[0-9]+$/; //match all integers
    return i.match(regex);
}

