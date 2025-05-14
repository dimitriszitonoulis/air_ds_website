import { validateRealTime, validateSubmitTime } from "../validationManager.js";
import { fields } from "./reservationFields.js";
import {  isAiportValidRealTime, isAirportValidSubmitTime, isDateValid, isTicketNumberValid } from "./reservationValidators.js";
import { showError } from "../displayMessages.js";


const reservationFields = {
    'date': { ...fields['date'], validatorFunction: isDateValid },
    'ticket': { ...fields['ticket'], validatorFunction: isTicketNumberValid }
};

// Real time validation
reservationFields['airports'] = { ...fields['airports'], validatorFunction: isAiportValidRealTime };


validateRealTime(reservationFields);


reservationFields['airports'] = { ...fields['airports'], validatorFunction: isAirportValidSubmitTime };


const purchaseBtn = document.getElementById('purchase-button');
const purchaseBtnErrorDiv = document.getElementById('purchase-button-error-message');

purchaseBtn.addEventListener('click', async (e) => {
    // if the button is clicked without any field being checked do nothing
    e.preventDefault();

    const isAllValid = await validateSubmitTime(reservationFields);

    // if a field is invalid do nothing
    // if (!isAllValid) return;

    // get the values from the form elements
    const values = getValues(reservationFields);


    // console.log(document.getElementById(reservationFields.date.inputId).value);



    if (isAllValid) {
        const url = `${BASE_URL}client/pages/book_flight.php`
        window.location.replace(url);

    } else {
        showError(purchaseBtnErrorDiv, "Could not purchase tickets");
    }
});


function getValues(reservationFields) {
    let values = {};
    for (const key in reservationFields) {
        const field = reservationFields[key];
        if (field.isCollection) {
            for (const current in field.inputId) {
                // inputId is array in this case
                const element = document.getElementById(field.inputId[current]);
                values[element.name] = element.value;
            }
        } else {
            const element = document.getElementById(field.inputId);
            values[element.name] = element.value;
        }
    }
}

