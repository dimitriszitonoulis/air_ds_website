// the url is calculated based on the location of the html file that inlcuded the script

// URL IS INCORRECT it must not contain ..
// this has the absolute path
// it is incorrect because the url starts from where the html file is located
const url = `/server/database/services/get_airports.php`;

let result = fetch(url, {
    method: "POST",
    headers: {
        'Content-Type': 'application/json'
    },
    body: JSON.stringify({'username': "D"}) 
})
.then(response =>{
    if(!response.ok){
        throw new Error("HTTP error " + response.status);
    }
    return response.json();
})
.then(data => console.log("Success: " + data))
.catch(error => console.error('Error: ' + error));