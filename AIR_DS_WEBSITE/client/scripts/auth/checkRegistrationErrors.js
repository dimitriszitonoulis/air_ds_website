import { fields } from "./fields.js"
import { isEmailValid, isNameValid, isPasswordValid, isPasswordValidRegister, isUsernameValidRegister } from "./fieldValidatorFunctions.js";
import { registerUser } from "./registerUser.js";
import { validateSubmitTime, validateRealTime} from "./validationManager.js";

/**
 * certain necessary fields for the form validation
 * these are passed on to:
 *      - @eventListener for the register button => submit time validation
 *      - @function setUpValidation() => real time validation
 */

// take the necessary fields from fields global variable and add their validator fucntions
// attention create shallow copy, do not modify the global variable
const registerFields = {
    "name": {...fields["name"], validatorFunction: isNameValid},
    "surname": {...fields["surname"], validatorFunction: isNameValid},
    "username": {...fields["username"], validatorFunction: isUsernameValidRegister},
    "password": {...fields["password"], validatorFunction: isPasswordValidRegister},
    "email": {...fields["email"], validatorFunction: isEmailValid}
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
