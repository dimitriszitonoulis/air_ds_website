export const fields = {

}




// add entries to the booking fields
// the ticket number is used to get the amount of names and surnames needed
export function addFullNames(ticketNumber, fields) {
    const extraPassengers = ticketNumber - 1;

    // the first user is the one with the account
    // start adding the other users
    for(let i = 1; i <= extraPassengers; i++) {
        // set name
        fields[`name-${i}`] = {};
        fields[`name-${i}`]['inputId'] = `name-${i}`;
        fields[`name-${i}`]['errorId'] = `fieldset-error-message-${i}`;
        fields[`name-${i}`]['event'] = 'change';
        fields[`name-${i}`]['validatorFunction'] = undefined;
        fields[`name-${i}`]['isAsync'] = false;
        fields[`name-${i}`]['isCollection'] = false;

        // set surname
        fields[`surname-${i}`] = {};
        fields[`surname-${i}`]['inputId'] = `surname-${i}`;
        fields[`surname-${i}`]['errorId'] = `fieldset-error-message-${i}`;
        fields[`surname-${i}`]['event'] = 'change';
        fields[`surname-${i}`]['validatorFunction'] = undefined;
        fields[`surname-${i}`]['isAsync'] = false;
        fields[`surname-${i}`]['isCollection'] = false;
    }
}

// export function addnameids(ticket_number, fields) {
//     const peoplenum = ticket_number;
    
//     // the first user is the one with the account
//     // start adding the other users
//     for(let i = 1; i < peoplenum; i++) {
//         // both name and surname share the same error message div
//         fields['names']['inputid'][`name-${i}`] = `name-${i}`;
//         fields['names']['errorid'][`name-${i}`] = `fieldset-error-message-${i}`;

//         fields['surnames']['inputid'][`surname-${i}`] = `surname-${i}`;
//         fields['surnames']['errorid'][`surname-${i}`] = `fieldset-error-message-${i}`;
//     }


// }

function addSeatIDs(TICKET_NUMBER, fields) {

}

// addFullNames(5, fields);

// console.log(fields)



