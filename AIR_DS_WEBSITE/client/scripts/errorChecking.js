
// const buyTicketBtn = document.addEventListener('click', errorChecking());

function errorChecking() {
    checkTicketNumber()
}

// checks if the number of tickets is valid (>= 1)
function checkTicketNumber(){
    const numberOfTicketsElement = document.getElementById('number-of-tickets');
    const numberOfTicketsLabel = document.getElementById('number-of-tickets-label');
    const numberOfTicketsLabelText = numberOfTicketsLabel.innerText;

    numberOfTicketsElement.addEventListener('change', (e) => {
        let ticketNumber = numberOfTicketsElement.value;
        
        if(!isNumber(ticketNumber))
            numberOfTicketsLabel.innerText = numberOfTicketsLabelText + " (you must enter a number)";
        else if (ticketNumber < 1){
            numberOfTicketsLabel.innerText = numberOfTicketsLabelText + " (you must by at least 1 ticket)";
        }
        else {
            // in case user first enters text and then number
            // change the innerText to the intial value
            numberOfTicketsLabel.innerText = numberOfTicketsLabelText;
        }
    })
}

function areAirportsSame() {
    const airportElements = document.getElementsByClassName('airport-code-selection');
  
    const airportLabels = document.getElementsByClassName('airport-code-label');
    console.log(airportLabels[0].innerText);
    
    for(let i = 0; i < 2; i++){
        airportElements[i].addEventListener('change', (e) =>{
            if (airportElements[i].value === airportElements[(i+1) % 2].value) {
                // get the field set of the current select item
                let fieldset = airportElements[i].parentElement;
                let label = fieldset.getElementsByClassName('airport-code-label');
                console.log('label = ' + label);
                console.log(airportLabels[i].innerText);
                label.innerText = airportLabels[i].innerText + "the departure and the destination airport must be different";
            }

        })

    }
}

function isNumber(i){
    const regex = /^-?[0-9]+$/; //match all integers
    return i.match(regex);     
}

areAirportsSame();