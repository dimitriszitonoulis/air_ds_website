function setCookie() {

}

/**
 * Summary of getCookie
 * 
 * Function that retrieves a cookie based on its name.
 * Gets the cookie string from the browser
 * Seperates it into 2 parts, the 2nd of which contains the desired cookie
 * Takes the 2nd part which contains the cookie values and ";"
 * Removes the ";"
 * Retrives the values
 * 
 * @param {string} name - the name of the desired cookie 
 * @returns 
 */
function getCookie(name) {
    // cookie values are seperated with ;
    // add one ";" at the beggining of the cookie string to be uniform
    const value = `; ${document.cookie}`;
    // split the cookie string into parts based on the name of the cookie I want
    //parts is made up of 2 parts, the 2nd contains the cookie I want and the next cookie
    const parts = value.split(`; ${name}=`);

    let cookieValue = "";
    if (parts.length === 2){
        cookieValue = parts.pop()   // pop retuns the 2nd part of parts
                        .split(";") // seperates the 2nd part of parts with delimiter ;
                        .shift();   // get the 1st word which is the cookie I want
    }
    // in php setcookie() automatically encodes the cookie, so I need to decode it
    const decodedCookie = decodeURIComponent(cookieValue);
    return decodedCookie; //return the cookie as a string in json format
}

 