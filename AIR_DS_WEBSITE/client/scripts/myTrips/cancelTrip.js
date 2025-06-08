
//TODO fix documentation
/**
 * values = {
 *      'username': username,
 *      'dep_code': departure airport code,
 *      'dest_code': destination airport code,
 *      'date': departure date
 * }
 * 
 * 
 * The api returns a JSON like: 
 * {
 *     result: boolean,
 *     message: string,
 *     http_response_code: int 
 * }
 * 
 * @param {object} values
 * @param {String} BASE_URL - a string containing the base url
 * @returns {boolean} - truf if the trip was deleted, otherwise false
 */
export async function cancelTrip (values, BASE_URL) {
    const url = `${BASE_URL}server/api/trips/cancel_trip.php`;

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

        return data['result'];

    } catch (error) {
        console.error("Error fetching data: ", error);
        return false;
    }
}