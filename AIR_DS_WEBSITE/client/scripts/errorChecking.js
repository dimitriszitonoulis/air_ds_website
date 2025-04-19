
const buyTicketBtn = document.addEventListener('click', errorChecking());

function errorChecking() {
    checkTicketNumber()
}

function checkTicketNumber(){
    const numberOfTicketsElement = document.getElementById('number-of-tickets');
    const numberOfTicketsLabel = document.getElementById('number-of-tickets-label');
    const numberOfTicketsLabelText = numberOfTicketsLabel.innerText;

    numberOfTicketsElement.addEventListener('change', () => {
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

function isNumber(i){
    const regex = /^-?[0-9]+$/; //match all integers
    return i.match(regex);     
}

