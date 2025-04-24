
const buyTicketBtn = document.addEventListener('click', (e) => {
    errorChecking()
    // document.getElementById('form').requestSubmit();
});

function errorChecking() {
    checkTicketNumber();
    checkAirportCodes();
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
        difference = tripDateValue.getTime() - currentDate.getTime();

        // console.log(tripDateValue.getTime() +  " - " + currentDate.getTime() + " = " + difference);

        const minuteInMilliseconds = 60000;

        // if there is less than 1 minute between the departure time and now
        if (difference < minuteInMilliseconds) {
            errMessageDiv.innerText = "You can not buy tickets 1 min before the designated takeoff";
            errMessageDiv.style.visibility = "visible";
        }
        else{
            errMessageDiv.style.visibility = "hidden";
        }
    })

}

// checks if the number of tickets is valid (>= 1)
function checkTicketNumber() {
    const numberOfTicketsElement = document.getElementById('ticket-number');
    const errMessageDiv = document.getElementById("ticket-number-error-message");

    numberOfTicketsElement.addEventListener('change', (e) => {
        let ticketNumber = numberOfTicketsElement.value;
        if (!isNumber(ticketNumber)) {
            errMessageDiv.innerText = "you must enter a number";
            // errMessageDiv.hidden = false;
            errMessageDiv.style.visibility = "visible";
        }
        else if (ticketNumber < 1) {
            errMessageDiv.innerText = "you must buy at least 1 ticket";
            // errMessageDiv.hidden = false;
            errMessageDiv.style.visibility = "visible";
        }
        else {
            // in case user first enters text and then number
            // change the innerText to the initial value
            // errMessageDiv.hidden = true;
            errMessageDiv.style.visibility = "hidden";
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

    for (let i = 0; i < aiportElementsLength; i++) {
    
        airportElements[i].addEventListener('change', (e) => {
            // get the parent of the current <select> Element (fieldset)
            let fieldset = airportElements[i].parentElement;

            // there are 2 selection element for the airport codes
            // if i = 0, then (i + 1) % aiportElementsLength = (0 + 1) % 2 = 1
            // if i = 1, then (i + 1) % aiportElementsLength = (1 + 1) % 2 = 0
            if (airportElements[i].value === airportElements[(i + 1) % aiportElementsLength].value) {
                // add the warning inside the div
                for(let j = 0; j < errMessageDivs.length; j++){
                    errMessageDivs[j].innerText = "the departure and the destination airport must be different";
                    errMessageDivs[j].style.visibility = "visible";
                }
            }
            else {
                for(let j = 0; j < errMessageDivs.length; j++){
                    errMessageDivs[j].style.visibility = "hidden";
                }
            }
        })
    
    //     const airportElements = document.getElementsByClassName('airport-code-selection');
    // const aiportElementsLength = airportElements.length;
    // const airportLabels = document.getElementsByClassName('airport-code-label');
    // const airportLabelsText = [
    //     airportLabels[0].innerText,
    //     airportLabels[1].innerText
    // ];
        // airportElements[i].addEventListener('change', (e) => {
        //     // get the parent of the current <select> Element (fieldset)
        //     let fieldset = airportLabels[i].parentElement;

        //     let errMessageDiv = fieldset.getElementsByClassName('error-message')[0];
        //     // get the label inside the current fieldset
        //     // even though there is only one label inside the fieldset an HTMLCollection is returned.
        //     // In order to access the label's inner text the first element [0] of the HTMLCollection must be accessed
        //     let label = fieldset.getElementsByClassName('airport-code-label')[0];
        //     // console.log(label);

        //     // there are 2 selection element for the airport codes
        //     // if i = 0, then (i + 1) % aiportElementsLength = (0 + 1) % 2 = 1
        //     // if i = 1, then (i + 1) % aiportElementsLength = (1 + 1) % 2 = 0
        //     if (airportElements[i].value === airportElements[(i + 1) % aiportElementsLength].value) {
        //         // add the warning inside the label
        //         // label.innerText = airportLabelsText[i] + " (the departure and the destination airport must be different)";
        //         errMessageDiv.innerText = "the departure and the destination airport must be different";
        //         errMessageDiv.style.visibility = "visible";
        //     }
        //     else {
        //         // label.innerText = airportLabelsText[i]
        //         errMessageDiv.style.visibility = "hidden";
        //     }
        // })
    }
}


function isNumber(i) {
    const regex = /^-?[0-9]+$/; //match all integers
    return i.match(regex);
}


checkTicketNumber();
checkAirportCodes();
checkDate();



