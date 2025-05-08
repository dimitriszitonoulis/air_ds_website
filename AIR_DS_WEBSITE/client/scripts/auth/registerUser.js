// TODO remove, only for testing
// let values = {
//     "name": "asdfa",
//     "surname": "asdf",
//     "username": "asdf23",
//     "password": "1234",
//     "email": "DX@gmail.com"
// }
registerUser(values, 'WEB_ZITONOULIS_DIMITRIOS_E22054/AIR_DS_WEBSITE/')
export async function registerUser (values, BASE_URL) {
    // TODO remove only for testing
    // let url = "http://localhost/WEB_ZITONOULIS_DIMITRIOS_E22054/AIR_DS_WEBSITE/server/api/auth/check_registration_errors.php";
    const url = `${BASE_URL}server/api/auth/check_registration_errors.php`;
    try {
        const response = await fetch(url, {
            method: "POST",
            headers: { 'Content-Type': "application/json"},
            body: JSON.stringify(values)
        });

        // console.log(await response.text());

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
