import { fields } from "./fields.js"
import { registerUser } from "./registerUser.js";
import { validateSubmitTime, validateRealTime} from "./validationManager.js";

/**
 * TODO
 * - For username availability maybe just return if any where found instead of getting the all the similar usernames 
 */ 




/**
 * certain necessary fields for the form validation
 * these are passed on to:
 *      - @eventListener for the register button => submit time validation
 *      - @function setUpValidation() => real time validation
 */



const registerFields = {
    "name": fields["name"],
    "surname": fields["surname"],
    "username": fields["username"],
    "password": fields["password"],
    "email": fields["email"]
}

const registerBtn = document.getElementById('register-button');

// add event listener to the register button to validate the values of the form fields
// if by chance any field has an invalid value do not register
// else submit form and register
registerBtn.addEventListener('click', async (e) => {
    // if the button is clicked without any field being checked do nothing
    e.preventDefault();
   
    const isAllValid = await validateSubmitTime(registerFields);

    // if a field is invalid do nothing
    if (!isAllValid) return;
  
    // document.getElementById('registration-form').requestSubmit();
    const values = {};
    for (const key in registerFields) {
        const field = registerFields[key];
        const element = document.getElementById(field.inputId);
        values[element.name] = element.value;
    }

    
    // TODO maybe modify to do something with error message
    const isRegistered = await registerUser(values, BASE_URL);

    if(isRegistered) {
        // redirect to login page
        // window.location.replace(`${BASE_URL}/client/pages/auth/login.php`)

        // DELETE LATER
        // window.location.href = `${BASE_URL}/client/pages/auth/login.php`;  
    } else {
        // console.error("registration failed")
    }
});

validateRealTime(registerFields);
