export async function bookTickets(values, BASE_URL) {
    let url = `${BASE_URL}server/api/reservation/book_tickets.php`;
    
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

        // if the user tickets are booked this is true, otherwise false
        return data['result'];

    } catch (error) {
        console.error("Error fetching data: ", error);
        return false;
    }
}