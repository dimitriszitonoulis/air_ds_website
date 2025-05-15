
// TODO add the variable from php
// get the number of tickets from the home form
// default value for now
const ticketNum = 5;

// create as many fieldsets as the tickets
let fieldsetNum = 1;

fieldsetNum += ticketNum;

addInfoFieldSets();


function addInfoFieldSets() {
    const seatForm = document.getElementById('seat-form');
    // TODO maybe take the field values from here and add the to the fields variable
    // there is already the default fieldset for the user with the account
    // add fieldsets for the rest of the users
    for (let counter = 1; counter < fieldsetNum; counter++) {
        // create necessary fields
        const fieldset = document.createElement('fieldset');
        const nameLabel = document.createElement('label');
        const nameInput = document.createElement('input');
        const surnameLabel = document.createElement('label');
        const surnameInput = document.createElement('input');
        const errorMessageDiv = document.createElement('div');

        // configure the fields and add the necessary attributes
        const nameFieldId = `name-input-${counter}`;
        const surnameFieldId = `surname-input-${counter}`;
        // the fieldset-1-error-message is taken by the dafault fieldset
        const errorFieldId = `fieldset-${counter + 1}-error-message`
        const errorFieldClass = `error-message`;

        setLabel(nameLabel, nameFieldId, "Name");
        setNameInput(nameInput, nameFieldId, "name", "text")
        setLabel(surnameLabel, surnameFieldId, "Surname");
        setNameInput(surnameInput, surnameFieldId, "surname", "text");
        setErrorMessage(errorMessageDiv, errorFieldId, errorFieldClass, "Empty")

        // TODO maybe use insert before
        // append the elements to the fieldset
        fieldset.appendChild(nameLabel);
        fieldset.appendChild(nameInput);
        fieldset.appendChild(surnameLabel);
        fieldset.appendChild(surnameInput);
        fieldset.appendChild(errorMessageDiv);

        // append the fieldset to the form
        seatForm.appendChild(fieldset);
    }
}




// sets label attributes
function setLabel(label, forName, innerText) {
    if (forName) label.htmlFor = forName;
    if (innerText) label.innerText = innerText;
}

//sets input attributes
function setNameInput(input, id, className, type) {
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