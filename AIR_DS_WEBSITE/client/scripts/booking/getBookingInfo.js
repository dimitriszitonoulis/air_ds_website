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
        return data['result'];

    } catch (error) {
        console.error("Error fetching data: ", error);
        return false;
    }
}