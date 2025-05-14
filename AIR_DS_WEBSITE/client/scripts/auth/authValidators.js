import { showError, clearError } from "../displayMessages.js"

/**
 * @fileoverview
 * 
 * This file contains all functions that check for a field's validity.
 * 
 * 
 * !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
 *                                  ATTENTION:
 *      These functions should not be used for security reasons, ONLY FOR UX.
 * !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
 * 
 * 
 * The functions with the name: is<fieldName>Valid share the following common logic:
 * @param {object} fieldInput - the input element for the name 
 * @param {object} errMessageDiv - The div used to display error messages for the name 
 * @returns {boolean} - true if the name is valid, false otherwise
 * 
 * With the given @parameters perform checks on the fieldInput.value
 * 
 * If a value does not pass a check:
 *      - show the relative error message
 *      - return false
 * 
 * If a value passes all the checks:
 *      - clear all previously shown error messages
 *      - return true
 * 
 */


// TODO maybe change the order of functions


/**
 *  function that checks the validity of the value in the name <input> field
 * 
 * ATTENTION:
 *      This function is used BOTH for the name as well as the surname input field (since they have the same requirements)
 * 
 * @param {object} nameInput - the input element for the name 
 * @param {object} errMessageDiv - The div used to display error messages for the name 
 * @returns {boolean} - true if the name is valid, false otherwise
 */
export function isNameValid(nameInput, errMessageDiv) {
    const name = nameInput.value;
    if (!name) {
        showError(errMessageDiv, "Field must not be empty");
        return false;
    }

    // if name contains something other than letters
    if (!isOnlyLetters(name)) {
        showError(errMessageDiv, 'This field must only contain letters');
        return false;
    }

    // all good
    clearError(errMessageDiv);
    return true;
}

export async function isUsernameValidRegister(usernameInput, errMessageDiv) {
    const username = usernameInput.value;
    const isInputValid = isUsernameInputValid(username, errMessageDiv);
    if (!isInputValid) {
        return false;
    }

    const isStored = await isUsernameStored(username);

    if (isStored) {
        showError(errMessageDiv, "Invalid username");
        return false;
    }

    clearError(errMessageDiv);
    return true;
}


export async function isUsernameValidLogin(usernameInput, errMessageDiv) {
    const username = usernameInput.value;
    const isInputValid = isUsernameInputValid(username, errMessageDiv);
    if (!isInputValid) return false;

    // TODO maybe remove since both the username and the password are checked after submitting
    // const isStored = await isUsernameStored(username);
    // TODO maybe remove along with above code
    // if (!isStored) {
    //     showError(errMessageDiv, "Invalid credentials");
    //     return false;
    // }

    clearError(errMessageDiv);
    return true;
}

// TODO maybe remove export later
export function isUsernameInputValid(username, errMessageDiv) {
    // if input empty
    if (!username) {
        showError(errMessageDiv, "Field must not be empty");
        return false;
    }
    if (!isAlphanumeric(username)) {
        showError(errMessageDiv, "The username must only contain letters and numbers");
        return false;
    }
    return true;
}

/**
 * function that ensures the validity of the password <input> field
 * A valid password is one that:
 *      - Is not empty
 *      - Has at least one number
 *      - Has 4 to 10 digits
 *  
 * @param {object} passwordInput - the <input> element containing the password
 * @param {object} errMessageDiv - the <div> containing the error message for the password
 * @returns 
 */
export function isPasswordValid(passwordInput, errMessageDiv) {
    const password = passwordInput.value;
    // check if the password is empty
    if (!password) {
        showError(errMessageDiv, "Field must not be empty");
        return false;
    }
    if (!constainsNumber(password)) {
        showError(errMessageDiv, "Password must contain at least one digit.");
        return false;
    }
    if (password.length < 4 || password.length > 10) {
        showError(errMessageDiv, "Password must have between 4 and 10 digits");
        return false;
    }
    // all good
    clearError(errMessageDiv);
    return true;
}

/**
 * function that ensures the validity of the email <input> field
 * A valid email is one that:
 *      - Is not empty
 *      - Contains the '@' character
 *  
 * @param {object} emailInput - the <input> element containing the email 
 * @param {object} errMessageDiv - the <div> containing the error message for the email
 * @returns 
 */
export function isEmailValid(emailInput, errMessageDiv) {
    const email = emailInput.value;
    // check if the email is empty
    if (!email) {
        showError(errMessageDiv, "Field must not be empty");
        return false;
    }

    // check if the email contains the @ character
    if (!containsATCharacter(email)) {
        showError(errMessageDiv, 'The email must contain the "@" character');
        return false;
    }

    // all good
    clearError(errMessageDiv);
    return true;
}


/**
 * Function that checks if the username is available
 * 
 * A username is AVAILABLE ONLY if it does NOT already exist in the database.
 * A username MATCHES the one entered by the user if it is the same, or if it is the same followed by other characters.
 * 
 * LOGIC:
 *  This functions fetches the server to see if the username is available.
 *  The server returns as a response an array. Each element of the array is a json, like: {username: "<username_value>"}.
 *      
 *  @example
 *      [ {username: "D"},
 *        {username: "Da"},  
 *        {username: "Db"} ] 
 * 
 *  If the array is undefined, then the server did not find the username in the database.
 *  If the array is not undefined, then it contains all the matching usernames in ascending order,
 *  based on the number of characters they have.
 * 
 * @param {string} username - the username entered by the user 
 * @returns {boolean} - true if the username does not exist in the database, false otherwise
 */
async function isUsernameStored(username) {
    const url = `${BASE_URL}server/api/auth/is_username_stored.php`;

    try {
        let response = await fetch(url, {
            method: "POST",
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ 'username': `${username}` })
        });

        const data = await response.json();

        if (!response.ok) {
            console.error("Server returned error", data);
            throw new Error("HTTP error " + response.status);
        }

        console.log("Fetch succesful return data:", data)

        const is_stored = data['result'];
        // if there is the same username in the database, this is false
        return is_stored;

    } catch (error) {
        console.error(error);
        return false;
    }
}

function isAlphanumeric(text) {
    const regex = /^[a-zA-Z0-9]+$/;
    return text.match(regex) !== null;
}

function constainsNumber(text) {
    const regex = /\d/;
    return text.match(regex) !== null;
}

function containsATCharacter(text) {
    const regex = /@/;
    return text.match(regex) !== null;
}

function isOnlyLetters(text) {
    const regex = /^[a-zA-Z]+$/;
    // const regex1 = /^[\p{L}]+$/u; // allows letters from all alphabets
    return text.match(regex) !== null;
}