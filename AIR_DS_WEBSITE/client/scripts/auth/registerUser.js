export async function registerUser (values, BASE_URL) {
    const url = `${BASE_URL}/server/services/auth/check_registration_errors.php`;
    try {
        const response = await fetch(url, {
            method: "POST",
            headers: { 'Content-Type': "application/json"},
            body: JSON.stringify(values)
        });

        if (!response.ok) {
            console.error("Server returned error", data);
            throw new Error("HTTP error " + response.status);
        }

        const data = await response.json();
        console.log("Fetch succesful return data:", data)

        if (data['response'] !== "user registered")
            return false;
        return true;
    } catch (error) {
        console.error("Error fetching data: ", error);
        return false;
    }
}
