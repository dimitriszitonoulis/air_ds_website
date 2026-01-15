import { getFullName } from "./getBookingInfo.js";

/**
 * Fills the name and surname for the logged in user
 * 
 * @param {string} username the username of a registered user 
 * @param {string} baseUrl the base url as defined in config/config.php
 */
export async function fillUserInfo(username, baseUrl) {
    // Take the name and surname of the registered user from the db
    const fullName = await getFullName({ 'username': username }, baseUrl);

    // fill the registered user's information
    const registeredUserNameField = document.getElementById('name-0');
    const registeredUserSurnameField = document.getElementById('surname-0');
    registeredUserNameField.value = fullName['name'];
    registeredUserSurnameField.value = fullName['surname'];
}

/**
 * Adds a fieldset for each passenger
 * Each fieldset contains:
 * - Name label and input field
 * - Surname label and input field
 * - Div for error messages for the name and surname fields
 * - Div for seat information
 *      - div containing the word: Seat
 *      - div containing the seat code for the passenger
 * 
 * @param {number} ticket_number the number of tickets to be purchased
 */
export function addInfoFieldSets(ticket_number) {
    // create as many fieldsets as the tickets
    let extraPassengers = 0;

    // the first one is the registered user
    extraPassengers = ticket_number - 1;

    const seatForm = document.getElementById('book-flight-form');
    const chooseSeatsDiv = document.getElementById('choose-seats-div');

    // there is already the default fieldset for the user with the account
    // add fieldsets for the rest of the users
    // The counter is used for the generation of ids, 0 belongs to the logged in user so counter starts from 1
    for (let counter = 1; counter <= extraPassengers; counter++) {
        const fieldset = document.createElement('fieldset');
        fieldset.className = "passenger-info-fieldset";

        // create necessary fields (input and label for name, surname, error message)
        const nameLabel = document.createElement('label');
        const nameInput = document.createElement('input');
        const surnameLabel = document.createElement('label');
        const surnameInput = document.createElement('input');
        const errorMessageDiv = document.createElement('div');
        
        // configure the fields attributes
        const nameFieldId = `name-${counter}`;
        const surnameFieldId = `surname-${counter}`;
        const errorFieldId = `fieldset-error-message-${counter}`
        const errorFieldClass = `error-message`;

        // set the fields
        setLabel(nameLabel, nameFieldId, "Name");
        setInput(nameInput, nameFieldId, "name", "text");
        setLabel(surnameLabel, surnameFieldId, "Surname");
        setInput(surnameInput, surnameFieldId, "surname", "text");
        setErrorMessage(errorMessageDiv, errorFieldId, errorFieldClass, "Empty")

        // append the elements to the fieldset
        fieldset.appendChild(nameLabel);
        fieldset.appendChild(nameInput);
        fieldset.appendChild(surnameLabel);
        fieldset.appendChild(surnameInput);
        fieldset.appendChild(errorMessageDiv);

        // create necessary fields for the seat
        const seatDiv = document.createElement('div');
        const seatLabel = document.createElement('div');
        const seatInput = document.createElement('div');
        
        // configure and set field attributes
        seatLabel.id = `seat-info-label-${counter}`;
        seatLabel.className = 'seat-info-label';
        seatLabel.innerText = 'Seat';
        seatInput.id = `seat-${counter}`;
        seatInput.className = 'seat-info';
        seatInput.innerText = '--';

        seatDiv.appendChild(seatLabel);
        seatDiv.appendChild(seatInput);

        // append seat field to fieldset
        fieldset.appendChild(seatDiv);

        // append the fieldset to the form
        seatForm.insertBefore(fieldset, chooseSeatsDiv);
    }
}

// sets label attributes
function setLabel(label, forName, innerText) {
    if (forName) label.htmlFor = forName;
    if (innerText) label.innerText = innerText;
}

//sets input attributes
function setInput(input, id, className, type) {
    if (id) input.id = id;
    if (className) input.className = className;
    if (type) input.type = type;
}

//sets error message attributes
function setErrorMessage(div, id, className, innerTextValue) {
    if (id) div.id = id;
    if (className) div.className = className;
    if (innerTextValue) div.innerText = innerTextValue;
}