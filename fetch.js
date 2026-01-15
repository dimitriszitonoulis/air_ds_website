


/**
 * THIS FILE IS INLCUDED TO MAKE THE TESTING OF THE API EASIER
 * 
 */




let values;

// for testing register
// values = {
//     "name": "DimXi",
//     "surname": "gfsdgsdf",
//     "username": "sfgsdfgs",
//     "password": "1234",
//     "email": "hello@gmail.com"
// };

// for testing username availability
// for testing getting a user's full name given their username
// for testing getting a user's trips
values = {"username": "Dim"};

// for testing login
// values = {
//     "username": "Dx",
//     "password": "1234"
// };

// for testing how a script handles null values
// values = null;

// for testing to get the flight dates between 2 airports
// for testing getting the information for 2 airports
// values = {
//     "dep_code": "ATH",
//     "dest_code": "BRU"
// };

//for retrieving the taken seats
// values = {
//     "dep_code": "ATH",
//     "dest_code": "BRU",
//     "dep_date": "2025-06-05 00:00:00"
// };


// for canceling the trip
// values = {
//     "username": "Dim",
//     "dep_code": "ATH",
//     "dest_code": "BRU",
//     "dep_date":	"2025-07-28 00:00:00"
// };

// for testing ticket booking
values = {
    "dep_code": "ATH",
    "dest_code": "CDG",
    "dep_date": "2025-07-28 00:00:00",
    "ticket_num": 2,
    "username": "Dim",
    "tickets": [
        {
            "name": "fdfdf",
            "surname": "aswdef",
            "seat":"F-12"
        },
        {
            "name": "Dim",
            "surname": "asdf",
            "seat": "E-12"
        }
    ]
};


testAPI(values)

async function testAPI (values) {
    // TODO remove only for testing
    // let url = "http://localhost/WEB_ZITONOULIS_DIMITRIOS_E22054/AIR_DS_WEBSITE/server/api/auth/is_username_stored.php";
    // let url = "http://localhost/WEB_ZITONOULIS_DIMITRIOS_E22054/AIR_DS_WEBSITE/server/api/auth/check_registration_errors.php";
    // let url = "http://localhost/WEB_ZITONOULIS_DIMITRIOS_E22054/AIR_DS_WEBSITE/server/api/auth/check_login_errors.php";
    // let url = "http://localhost/WEB_ZITONOULIS_DIMITRIOS_E22054/AIR_DS_WEBSITE/server/api/reservation/get_flight_dates.php";
    // let url = "http://localhost/WEB_ZITONOULIS_DIMITRIOS_E22054/AIR_DS_WEBSITE/server/api/auth/get_user_info.php"
    // let url = "http://localhost/WEB_ZITONOULIS_DIMITRIOS_E22054/AIR_DS_WEBSITE/server/api/reservation/get_taken_seats.php";
    // let url = "http://localhost/WEB_ZITONOULIS_DIMITRIOS_E22054/AIR_DS_WEBSITE/server/api/reservation/get_airport_info.php";
    let url = "http://localhost/WEB_ZITONOULIS_DIMITRIOS_E22054/AIR_DS_WEBSITE/server/api/reservation/book_tickets.php";
    // let url = "http://localhost/WEB_ZITONOULIS_DIMITRIOS_E22054/AIR_DS_WEBSITE/server/api/trips/get_trips.php";
    // let url = "http://localhost/WEB_ZITONOULIS_DIMITRIOS_E22054/AIR_DS_WEBSITE/server/api/trips/cancel_trip.php";
    // const url = `${BASE_URL}server/api/auth/check_registration_errors.php`;

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


        console.dir(data, {depth:null});
        // console.log("Fetch succesful return data:", data)

        // if the user is registered this is true, otherwise false
        return data['result'];

    } catch (error) {
        console.error("Error fetching data: ", error);
        return false;
    }
}
