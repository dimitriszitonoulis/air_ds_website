async function getAirportCodes() {
    // fetch airport codes from db
    const url = `${BASE_URL}/server/database/services/get_airports.php`;
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
    } catch (error) {
        console.error(error);
        return airports;
    }
    
    // get the select elements for the departure and destination airports
    const selectElements = document.getElementsByClassName('airport-selection');
    
    // add an option element with value "--"
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

getAirportCodes()
