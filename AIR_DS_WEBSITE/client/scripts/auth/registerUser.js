export function registerUser (values) {
    const url = `${BASE_URL}/server/services/auth/check_registration_errors.php`;
   
    fetch(url, {
        method: "POST",
        headers: { 'Content-Type': "application/json"},
        body: JSON.stringify(values)
    })
    .then(response => {
        if(!response.ok) {
            throw new Error("HTTP error " + response.status);
        }
        return response.json();
    })
    .then(data => {
        console.log("Fetch succesful return data:", data);
    })
    .catch(error => console.error("Error fetching data: ", error));
}
