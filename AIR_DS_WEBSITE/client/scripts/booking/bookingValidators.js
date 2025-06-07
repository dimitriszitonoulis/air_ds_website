import { showError, clearError } from '../displayMessages.js';

/**
 * 
 * This function performs real time valition to the supplied name
 * A name is valid if:
 *  - It is not null
 *  - It only contains letters
 *  - It has no more than 20 and no less than 3 characters
 * 
 * 
 * 
 * !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
 *                              ATTENTION
 * this function is also used for the validation of the surname
 * !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
 * 
 * 
 * @param {Array} nameElements - an array of HTML input elements containing the name values
 * @param {Array} errMessageDivs - An array of the HTML elements for the error messages
 * @returns 
 */
export function isNameValid(nameElement, errMessageDiv) {
    const name = nameElement.value;
        if (!name) {
        showError(errMessageDiv, "Name and Surname must not be empty");
        return false;
    }

    // if name contains something other than letters
    if (!isOnlyLetters(name)) {
        showError(errMessageDiv, 'Name and Surname must only contain letters');
        return false;
    }

    if (name.length < 3 || name.length > 20) {
        showError(errMessageDiv, 'Name and Surname must be between 3 and 20 characters');
        return false
    }

    // all good
    clearError(errMessageDiv);
    return true;
}


function isOnlyLetters(text) {
    const regex = /^[a-zA-Z]+$/;
    // TODO see if this should be used
    // const regex1 = /^[\p{L}]+$/u; // allows letters from all alphabets
    return text.match(regex) !== null;
}