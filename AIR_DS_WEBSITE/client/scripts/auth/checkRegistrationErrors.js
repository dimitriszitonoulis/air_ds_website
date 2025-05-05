import { registerUser } from "./registerUser.js";
import { isNameValid, isUsernameValid, isPasswordValid, isEmailValid } from "./fieldValidatorFunctions.js";
import { validateSubmitTime, validateRealTime} from "./validatorManager.js";

/**
 * TODO
 * - For username availability maybe just return if any where found instead of getting the all the similar usernames 
 * - DO something when the server responsds that a user could not be registered
 * - checng e validataion function so that it does not call itself
 */ 




/**
 * certain necessary fields for the form validation
 * these are passed on to:
 *      - @function setUpValidation() => real time validation
 *      - @eventListener for the register button => submit time validation
 */
const registerFields = [
    {
        inputId: 'name-input',
        errorId: 'name-input-error-message',
        event: 'change',
        validatorFunction: isNameValid,
        isAsync: false
    },
    {
        inputId: 'surname-input',
        errorId: 'surname-input-error-message',
        event: 'change',
        validatorFunction: isNameValid,
        isAsync: false
    },
    {
        inputId: 'username-input',
        errorId: 'username-input-error-message',
        event: 'change',
        validatorFunction: isUsernameValid,
        isAsync: true
    },
    {
        inputId: 'password-input',
        errorId: 'password-input-error-message',
        event: 'change',
        validatorFunction: isPasswordValid,
        isAsync: false
    },
    {
        inputId: 'email-input',
        errorId: 'email-input-error-message',
        event: 'change',
        validatorFunction: isEmailValid,
        isAsync: false
    }
]


const registerBtn = document.getElementById('register-button');

// add event listener to the register button to validate the values of the form fields
// if by chance any field has an invalid value do not register
// else submit form and register
registerBtn.addEventListener('click', async (e) => {
    // if the button is clicked without any field being checked do nothing
    e.preventDefault();
   
    let isAllValid = await validateSubmitTime(registerFields);

    // if a field is invalid do nothing
    if (!isAllValid) return;
  
    // document.getElementById('registration-form').requestSubmit();
    const values = {};
    for(const field of registerFields) {
        const element = document.getElementById(field.inputId);
        values[element.name] = element.value;
    }
   
    const isRegistered = await registerUser(values, BASE_URL);

    if(isRegistered) {
        // redirect to login page
        // window.location.replace(`${BASE_URL}/client/pages/auth/login.php`)

        // DELETE LATER
        window.location.href = `${BASE_URL}/client/pages/auth/login.php`;  
    } else {
        console.error("registration failed")
    }
});

validateRealTime(registerFields);
