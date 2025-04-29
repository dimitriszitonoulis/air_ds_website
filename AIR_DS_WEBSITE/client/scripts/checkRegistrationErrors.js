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

    usernames = data.map(user =>user.username)
    
    // if no userames are returned the username is availble
    if (usernames[0] === undefined) {
        return true;
    }
    else {
        return false;
    }
}