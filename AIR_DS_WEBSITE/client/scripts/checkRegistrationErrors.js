/*
 * Checks to be made:
 * Email:
 *  Must contain @ character
 *  Must be unique => no other user with that username
 * 
 * ATTENTION:
 *  - When user registers they must be redirected to login form/page
 * 
 *  - For UX when fetching the database (ex to check that the username is unique) the server must not send
 *    all of the stored data (the usernames of all users)
 *    But rather only what is needed for the check (if the username has been seen in the db)
 *    Maybe for the specific example the server can return the name of 
 *
 *  MAYBE perform the check for the email availability only after the user has registered for security reasons
 * Why should it fail? 
 * The system should say "Thank you! A verification link will be sent to this address, 
 * we hope you enjoy our site." 
 * The system will send an account activation link to the email address if it doesn't exist, 
 * and a warning/re-activation link if it does. 
 */




const fields = [
    {
        inputId: 'name-input',
        errorId: 'name-input-error-message',
        event: 'change',
        validatorFunction: isNameValid,
        isAsync: false
    },
    {
        inputId: 'surname-input',
        errorId: 'surname-input-error-message',
        event: 'change',
        validatorFunction: isNameValid,
        isAsync: false
    },
    {
        inputId: 'username-input',
        errorId: 'username-input-error-message',
        event: 'change',
        validatorFunction: isUsernameValid,
        isAsync: true
    },
    {
        inputId: 'password-input',
        errorId: 'password-input-error-message',
        event: 'change',
        validatorFunction: isPasswordValid,
        isAsync: false
    },
    {
        inputId: 'email-input',
        errorId: 'email-input-error-message',
        event: 'change',
        validatorFunction: isEmailValid,
        isAsync: false
    }
]


/**
* function responsible for the adding validation checks on the <input> field with id = inputId
* 
* This function performs:
*  Real time evaluation, by adding an event listener on the <input> element
*  Submit time evaluation, by returning true or false
*  @param {JSON} - A JSON containing:
* - inputId: {string} the id of the <input> element on which the validation is applied
* - errorId: {string} the id of the <div> element containing the error message for the <input> element
* - event: {string} the event to pass on to the eventListener
* - validatorFunction: {function} the function that performs the validation checks
* - isAsync: {boolean} true if the validator function is asynchronous false otherwise
* @returns {boolean} - true if the password is alright, false otherwise
*/
function setUpValidation() {
    for(const field of fields) {
        const inputElement = document.getElementById(field.inputId);
        const errorElement = document.getElementById(field.errorId);

        inputElement.addEventListener(field.event, async (e) => {
            if(field.isAsync)
                await field.validatorFunction(inputElement, errorElement);
            else
                field.validatorFunction(inputElement, errorElement);
        });
    }
}


// TODO CHANGE LATER TO NOT MAKE CALL
// Call the function
setUpValidation();


// get the submit button
const registerBtn = document.getElementById('register-button');

registerBtn.addEventListener('click', async (e) => {
    e.preventDefault();

    let isAllValid = true;

    // loop through all the fields and check if they are valid
    for (const field of fields) {
        const inputElement = document.getElementById(field.inputId)
        const errorElement = document.getElementById(field.errorId);
        const isAsync = field.isAsync;
        let isValid = true;

        if (isAsync) // if the function is async await its response
            isValid = await field.validatorFunction(inputElement, errorElement);
        else
            isValid = field.validatorFunction(inputElement, errorElement);

        // if the field is not valid then set that not all fields are valid
        if (!isValid)
            isAllValid = false;
    }

    // only if all the fields are valid submit the form
    if (isAllValid) {
        document.getElementById('registration-form').requestSubmit();
    }
});


/**
 *  function that checks the validity of the value in the password <input> field
 * 
 * ATTENTION:
 *      This function is used BOTH for the name as well as the surname input field (since they have the same requirements)
 * 
 * @param {object} nameInput - the input element for the name 
 * @param {object} errMessageDiv - The div used to display error messages for the name 
 * @returns {boolean} - true if the name is valid, false otherwise
 */
function isNameValid(nameInput, errMessageDiv) {
    const name = nameInput.value;

    // if input empty
    if (!name)
        return false;

    // if name contains something other than letters
    if (!isOnlyLetters(name)) {
        showError(errMessageDiv, 'This field must only contain letters');
        return false;
    }

    // all good
    clearError(errMessageDiv);
    return true;
}


/**
 * Function that checks if the username in the userame input field is valid.
 * 
 * If the username is not valid then an error message is shown in the page and false is returned.
 * Otherwise, the error message (if it appeared) gets cleared and true is returned
 * 
 * LOGIC:
 *  - checks if the username is empty
 *  - checks if the username is available 
 *  
 * @param {object} usernameInput - the input element for the username
 * @param {object} errMessageDiv - The div used to display error messages for the username
 * @returns {boolean} - true if the username is valid, false otherwise
 */
async function isUsernameValid(usernameInput, errMessageDiv) {
    let username = usernameInput.value;

    // if input empty
    if (!username)
        return false;

    if (!isAlphanumeric(username)) {
        showError(errMessageDiv, "The username must only contain letters and numbers");
        return false;
    }

    // isUsernameAvailable() is an async function that returns true if the username is available, else false
    // await for its response 
    const isAvailable = await isUsernameAvailable(username);

    // if the username is not available
    if (!isAvailable) {
        showError(errMessageDiv, "Username is not available.");
        return false;
    }

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
async function isUsernameAvailable(username) {
    // fetch from db
    const url = `${BASE_URL}/server/database/services/db_is_username_stored.php`;

    let usernames = "";
    let data = "";

    // get all the usernames that match username 
    // (are the same as username or have the pattern: <username><other_characters>)
    try {
        // fetch the usernames by sending a POST request where
        // _POST['username'] = username entered by user
        let response = await fetch(url, {
            method: "POST",
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ 'username': `${username}` })
        });
        if (!response.ok) {
            throw new Error("HTTP error " + response.status);
        }
        data = await response.json();
    } catch (error) {
        console.error(error);
        return false;
    }

    usernames = data.map(user => user.username)

    // if no usernames are returned the username is availble
    if (usernames[0] === undefined)
        return true;
    else
        return false;
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
function isPasswordValid(passwordInput, errMessageDiv) {
    let password = passwordInput.value;

    // check if the password is empty
    if (!password)
        return false;

    // check password contains at least one digit
    if (!constainsNumber(password)) {
        showError(errMessageDiv, "Password must contain at least one digit.");
        return false;
    }

    // check password is 4 - 10 digits
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
function isEmailValid(emailInput, errMessageDiv) {
    const email = emailInput.value;

    // check if the email is empty
    if (!email)
        return false;

    // check if the email contains the @ character
    if (!containsATCharacter(email)) {
        showError(errMessageDiv, 'The email must contain the "@" character');
        return false;
    }

    // all good
    clearError(errMessageDiv);
    return true;
}

function showError(element, message) {
    element.innerText = message;
    element.style.visibility = "visible";
}

function clearError(element) {
    element.style.visibility = "hidden";
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



