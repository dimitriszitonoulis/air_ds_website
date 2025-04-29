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