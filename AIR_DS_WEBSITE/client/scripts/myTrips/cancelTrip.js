/**
 * 
 * This function gets the username of the logged in user and 
 * returns trips made by that user
 * 
 * values = {
 *      'username': <username>,
 * }
 * 
 * 
 * The api returns a JSON like: 
 * 
 * {
 * 
 *      "departure_airport": <departure aiport code>,
 *      "destination_airport": <destination airport code,
 *      "date": <departure date>,
 *      "name": <name>,
 *      "surname": <surname>,
 *      "seat": <seat code>,
 *      "price": <ticket price>
 * }
 * 
 * @param {object} values - object containing the username
 * @param {String} BASE_URL - a string containing the base url
 * @returns {object} - an object like: {'name': <name>, 'surname': <surname>}
 */
export async function cancelTrip (values, BASE_URL) {

    const url = `${BASE_URL}server/api/trips/.php`;

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

        // return data['trips'];

    } catch (error) {
        console.error("Error fetching data: ", error);
        return false;
    }
}