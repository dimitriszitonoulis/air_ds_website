/**
 * 
 * This function gets the username of a registered user and returns their name and surname
 * 
 * values = {"username": <username value>}
 * 
 * @param {object} values - object containing the username of a user
 * @param {String} BASE_URL - a string containing the base url
 * @returns {object} - an object like: {'name': <name>, 'surname': <surname>}
 */
export async function getFullName(values, BASE_URL) {
   const url = `${BASE_URL}server/api/auth/get_user_info.php`;

    try {
        const response = await fetch(url, {
            method: "POST",
            headers: { 'Content-Type': "application/json"},
            body: JSON.stringify(values)
        });

        const data = await response.json();

        if (!response.ok) {
            console.error("Server returned error", data);
            throw new Error("HTTP error " + response.status);
        }

        console.log("Fetch succesful return data:", data)
        
        return {'name': data['name'], 'surname': data['surname']}
    } catch (error) {
        console.error("Error fetching data: ", error);
        return false;
    }
}

/**
 * 
 * This function gets the departure airport code, the destination arirport code and the deparure date and 
 * returns the taken seats for the flight specified by those values
 * 
 * values = {
 *      'dep_code': <departure airtport code>,
 *      'dest_code': <destination airport code>
 *      'dep_date': <departure date>
 * }
 * 
 * @param {object} values - object containing the departure airport code, the destination airport code, the departure date
 * @param {String} BASE_URL - a string containing the base url
 * @returns {object} - an object like: {'name': <name>, 'surname': <surname>}
 */
export async function getTakenSeats (values, BASE_URL) {

    const url = `${BASE_URL}server/api/reservation/get_taken_seats.php`;

    try {
        const response = await fetch(url, {
            method: "POST",
            headers: { 'Content-Type': "application/json"},
            body: JSON.stringify(values)
        });

        const data = await response.json();

        if (!response.ok) {
            console.error("Server returned error", data);
            throw new Error("HTTP error " + response.status);
        }

        console.log("Fetch succesful return data:", data)

        // if the user is registered this is true, otherwise false
        return data['seats'];
        
    } catch (error) {
        console.error("Error fetching data: ", error);
        return false;
    }
}
 /**
 * values = {
 *      'dep_code': <departure airtport code>,
 *      'dest_code': <destination airport code>
 * }
*/
export async function getAirportInfo(values, BASE_URL) {
    const url = `${BASE_URL}server/api/reservation/get_airport_info.php`;

    try {
        const response = await fetch(url, {
            method: "POST",
            headers: { 'Content-Type': "application/json"},
            body: JSON.stringify(values)
        });

        const data = await response.json();

        if (!response.ok) {
            console.error("Server returned error", data);
            throw new Error("HTTP error " + response.status);
        }

        console.log("Fetch succesful return data:", data)

        // if the user is registered this is true, otherwise false
        return data['airport_info'];
        
    } catch (error) {
        console.error("Error fetching data: ", error);
        return false;
    }
}