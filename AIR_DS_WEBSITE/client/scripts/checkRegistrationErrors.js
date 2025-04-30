/*
 * Checks to be made:
 * Name:
 *  Only characters
 * Surname:
 *  Only characters
 * Username:
 *  Letters or numbers
 *  Unique
 * Password:
 *  At least one number
 *  Length: 4 to 10 characters
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



checkUsername()



/**
 * Function responsible for evaluating the entered username 
 * This function performs:
 *  Real time evaluation, by adding an event listener on the username input element
 *  Submit time evaluation, by returning true (username is alright) or false (otherwise)
 * 
 * @returns {boolean} - true if the username is alright, false otherwise
 */
function checkUsername() {
    const usernameInput = document.getElementById('username-input');
    const errMessageDiv = document.getElementById('username-input-error-message');

    usernameInput.addEventListener('change', (e) => {
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
function isUsernameValid(usernameInput, errMessageDiv) {
    let username = usernameInput.value;

    // if input empty
    if (!username) 
        return false;

    // if the username is not available
    if (!isUsernameAvailable(username)) {
        showError(errMessageDiv, "username is taken.");
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
    try{
        // get all the usernames that match username 
        // (are the same as username or have the pattern: <username><other_characters>)
        const response = fetch(url, {
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


function showError(errMessageDiv, message) {
    errMessageDiv.innerText = message;
    errMessageDiv.style.visibility = "visible";
}

function clearError(errMessageDiv) {
    errMessageDiv.style.visibility = "hidden";
}