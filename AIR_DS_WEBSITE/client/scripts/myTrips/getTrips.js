
// TODO fix documentation
/**
 * 
 * This function gets username of the logged in user and 
 * returns trips made by that user
 * 
 * values = {
 *      'username': <username>,
 * }
 * 
 * @param {object} values - object containing the username
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
        return data;

        
    } catch (error) {
        console.error("Error fetching data: ", error);
        return false;
    }
}