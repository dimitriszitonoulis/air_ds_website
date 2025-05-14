getAirportCodes()

async function getAirportCodes() {
    
    let airports = await fetch_airports();
    console.log(airports);
    
    // get the select elements for the departure and destination airports
    const selectElements = document.getElementsByClassName('airport-selection');
    
    // add an option element with value "-"
    for (let selectElement of selectElements){
        let option = document.createElement('option');
        option.value = "-";
        option.innerText = "-";
        // if cloneNode is not used the <option> gets appended only to the 2nd select element
        selectElement.appendChild(option.cloneNode(true));
    }

    // add the airport codes to the select elements
    for (let airport of airports) {
        // create the option element
        let option = document.createElement('option');
        option.value = `${airport['name']} (${airport['code']})`;
        option.innerHTML = `${airport['name']} (${airport['code']})`;
        // append option to the select elements 
        for (selectElement of selectElements) {
            // if cloneNode is not used the <option> gets appended only to the 2nd select element
            selectElement.appendChild(option.cloneNode(true));
        }
    }
}

/**
 * Summary of fetch_airports()
 * Fetches the server to get the the names and codes of the airports
 * 
 * @returns {object} - contains key value pairs like:
 *  {
 *      name: airport name,
 *      code: airport code
 *  }
 */
async function fetch_airports() {
// fetch airport codes from db
    const url = `${BASE_URL}server/api/get_airports.php`;
    let airports = "";
    try {
        const response = await fetch(url, {
            headers:{
                 'Content-Type': 'application/json'
            }
        })
        if (!response.ok){
            throw new Error("HTTP error " + response.status);
        }
        airports = await response.json();
        return airports;
    } catch (error) {
        console.error(error);
        return airports;
    }
}

