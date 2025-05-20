import { validateRealTime, validateSubmitTime } from "../validationManager.js";
import { fields } from "./reservationFields.js";
import { isAiportValidRealTime, isAirportValidSubmitTime, isDateValid, isTicketNumberValid } from "./reservationValidators.js";
import { showError } from "../displayMessages.js";

// set the validators for date and ticket elements
// the validators for airports will be set later
const reservationFields = {
    'date': { ...fields['date'], validatorFunction: isDateValid },
    'ticket': { ...fields['ticket'], validatorFunction: isTicketNumberValid }
};

// Real time validation
reservationFields['airports'] = { ...fields['airports'], validatorFunction: isAiportValidRealTime };
validateRealTime(reservationFields);

// change validator for submit time validation
reservationFields['airports'] = { ...fields['airports'], validatorFunction: isAirportValidSubmitTime };

const purchaseBtn = document.getElementById('purchase-button');
const purchaseBtnErrorDiv = document.getElementById('purchase-button-error-message');

const homeForm = document.getElementById('purchase-tickets-form');

purchaseBtn.addEventListener('click', async (e) => {
    // if the button is clicked without any field being checked do nothing
    e.preventDefault();

    const isAllValid = await validateSubmitTime(reservationFields);

    // if a field is invalid do nothing
    // if (!isAllValid) return;

    // get the values from the form elements
    const values = getValues(reservationFields);

    if (isAllValid) {
        homeForm.requestSubmit(); 
        // const url = `${BASE_URL}client/pages/book_flight.php`
        // window.location.replace(url);

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
                // inputId is js object in this case
                const element = document.getElementById(field.inputId[current]);
                values[element.name] = element.value;
            }
        } else {
            const element = document.getElementById(field.inputId);
            values[element.name] = element.value;
        }
    }
}

