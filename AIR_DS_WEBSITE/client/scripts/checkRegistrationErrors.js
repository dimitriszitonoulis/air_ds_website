/*
 * Checks to be made:
 * Name:
 *  Only characters
 * Surname:
 *  Only characters
 * Username:
 *  Letters or numbers => DONE
 *  Unique => DONE
 * Password:
 *  At least one number => DONE
 *  Length: 4 to 10 characters => DONE
 * Email:
 *  Must contain @ character
 *  Must be unique => no other user with that username
 * 
 * 
 * ATTENTION:
 *  - When user registers they must be redirected to login form/page
 * 
 *  - For UX when fetching the database (ex to check that the username is unique) the server must not send
 *    all of the stored data (the usernames of all users)
 *    But rather only what is needed for the check (if the username has been seen in the db)
 *    Maybe for the specific example the server can return the name of 
 *
 */



checkUsername();
checkPassword();
checkEmail();




/**
 * Function responsible for evaluating the entered username 
 * 
 * This function performs:
 *  Real time evaluation, by adding an event listener on the username input element
 *  Submit time evaluation, by returning true (username is alright) or false (otherwise)
 * 
 * @returns {boolean} - true if the username is alright, false otherwise
 */
function checkUsername() {
    const usernameInput = document.getElementById('username-input');
    const errMessageDiv = document.getElementById('username-input-error-message');

    /*
     * The function that checks the validity of the username is async
     * This happens because it awaits the result of another function that fetches the database 
     */
    usernameInput.addEventListener('change', async (e) => {
        // for real time evaluation
        isUsernameValid(usernameInput, errMessageDiv);
    });  
    // for submit time evaluation
    return isUsernameValid(usernameInput, errMessageDiv);
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
 *      e.x.
 *          [ {username: "D"},
 *            {username: "Da"},  
 *            {username: "Db"} ] 
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
    try{
        // fetch the usernames by sending a POST request where
        // _POST['username'] = username entered by user
        let response = await fetch(url, {
            method: "POST",
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({'username': `${username}`}) 
            });
            if(!response.ok){
                throw new Error("HTTP error " + response.status);
            }
        data = await response.json();
    } catch(error) {
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
 * funtion responsible for evaluating the entered password
 * 
 * This function performs:
 *  Real time evaluation, by adding an event listener on the password <input> element
 *  Submit time evaluation, by returning true (password is alright) or false (otherwise)
 * 
 * @returns {boolean} - true if the password is alright, false otherwise
 */
function checkPassword() {
    const passwordInput = document.getElementById('password-input');
    const errMessageDiv = document.getElementById("password-input-error-message");

    passwordInput.addEventListener('change', (e) => {
        // for real time evaluation
        isPasswordValid(passwordInput, errMessageDiv);
    })
    // for submit time evaluation
    return isPasswordValid(passwordInput, errMessageDiv);
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
    if(!constainsNumber(password)) {
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
 * function responsible for evaluating the entered email
 * 
 * This function performs:
 *  Real time evaluation, by adding an event listener on the email <input> element
 *  Submit time evaluation, by returning true (email is alright) or false (otherwise)
 * 
 * @returns {boolean} - true if the email is alright, false otherwise
 */
function checkEmail() {
    const emailInput = document.getElementById('email-input');
    const errMessageDiv = document.getElementById('email-input-error-message');

    emailInput.addEventListener('change', (e) => {
    // for real time evaluation
        isEmailValid(emailInput, errMessageDiv);
    })
    // for submit time evaluation
    return isEmailValid(emailInput, errMessageDiv);
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
    if(!email)
        return false;

    // check if the email contains the @ character
    if(!containsAtCharacter(email)) {
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
    return text.match(regex)!== null;
}

function containsAtCharacter(text) {
    const regex = /@/;
    return text.match(regex) !== null;
}