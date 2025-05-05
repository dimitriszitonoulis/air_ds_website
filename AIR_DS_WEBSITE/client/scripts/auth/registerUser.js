export async function registerUser (values, BASE_URL) {
    const url = `${BASE_URL}server/api/auth/check_registration_errors.php`;
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
