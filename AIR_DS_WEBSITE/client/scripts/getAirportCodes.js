async function getAirportCodes() {
    // fetch airport codes from db
    const uri = "./server/database/services/get_airport_codes.php";
    let airportCodes = "";
    try {
        const response = await fetch(uri)
        if (!response.ok){
            throw new Error("HTTP error " + response.status);
        }
        airportCodes = await response.json();
    } catch (error) {
        console.error(error);
        return airportCodes;
    }
    
    // get the select elements for the departure and destination airports
    const selectElements = document.getElementsByClassName('airport-code-selection');
    
    // add an option element with value "--"
    for (selectElement of selectElements){
        let option = document.createElement('option');
        option.value = "-";
        option.innerText = "-";
        selectElement.appendChild(option.cloneNode(true));
    }

    // add the airport codes to the select elements
    for (airportCode of airportCodes) {
        // create the option element
        let option = document.createElement('option');
        option.value = airportCode;
        option.innerHTML = airportCode;
        // append option to the select elements
        for (selectElement of selectElements) {
            // if cloneNode is not used the <option> gets appended only to the 2nd select element
            selectElement.appendChild(option.cloneNode(true));
        }
    }
}

getAirportCodes()
