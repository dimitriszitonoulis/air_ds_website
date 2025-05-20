import { showError, clearError } from '../displayMessages';

// checks if the names are valid
export function isNameValidBooking(nameElements, errMessageDivs) {
    const nameElementsLength = nameElements.length;

    isAllValid = true;

    for (let current = 0; current < nameElementsLength; current++) {
        const name = nameElements[current].value;

        if (!name) {
            showError(errMessageDiv, "Field must not be empty");
            isAllValid = false;
            continue;
        }

        // if name contains something other than letters
        if (!isOnlyLetters(name)) {
            showError(errMessageDiv, 'This field must only contain letters');
            isAllValid = false;
            continue;
        }

        if (name.length < 3 || name.length > 20) {
            showError(errMessageDiv, 'Name and Surname must be between 3 and 20 characters');
            isAllValid = false;
            continue;
        }

        // all good
        clearError(errMessageDiv);
    }

    // if all fields are valid then this variable is true
    return isAllValid;

}


function isOnlyLetters(text) {
    const regex = /^[a-zA-Z]+$/;
    // TODO see if this should be used
    // const regex1 = /^[\p{L}]+$/u; // allows letters from all alphabets
    return text.match(regex) !== null;
}