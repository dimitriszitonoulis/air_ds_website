import { fields } from "./authFields.js"
import { isEmailValid, isNameValid, isPasswordValid, isUsernameValidRegister } from "./authValidators.js";
import { registerUser } from "./registerUser.js";
import { validateSubmitTime, validateRealTime } from "../validationManager.js";
import { showMessage, hideMessage, appendMessage, clearError, showError, showRedirectMessage } from "../messageDisplay.js";

/**
 * certain necessary fields for the form validation
 * these are passed on to:
 *      - @eventListener for the register button => submit time validation
 *      - @function setUpValidation() => real time validation
 */

// take the necessary fields from fields global variable and add their validator fucntions
// attention create shallow copy, do not modify the global variable
const registerFields = {
    "name": { ...fields["name"], validatorFunction: isNameValid },
    "surname": { ...fields["surname"], validatorFunction: isNameValid },
    "username": { ...fields["username"], validatorFunction: isUsernameValidRegister },
    "password": { ...fields["password"], validatorFunction: isPasswordValid },
    "email": { ...fields["email"], validatorFunction: isEmailValid }
}

const registerBtn = document.getElementById('register-button');
const registerBtnErrorDiv = document.getElementById('registration-button-error-message');

// add event listener to the register button to validate the values of the form fields
// if by chance any field has an invalid value do not register
// else submit form and register
registerBtn.addEventListener('click', async (e) => {
    // if the button is clicked without any field being checked do nothing
    e.preventDefault();

    const isAllValid = await validateSubmitTime(registerFields);

    // if a field is invalid do nothing
    if (!isAllValid) return;

    // get the values from the form elements
    const values = {};
    for (const key in registerFields) {
        const field = registerFields[key];
        const element = document.getElementById(field.inputId);
        values[element.name] = element.value;
    }

    // TODO maybe modify to do something with error message
    // const isRegistered = await registerUser(values, BASE_URL);
    const isRegistered = await registerUser(values, BASE_URL);

    if (isRegistered) {
        // clear previous errors
        clearError(registerBtnErrorDiv);

        const message = `Registered sucessfully\n You will be redirected to the login page shortly`;
        showMessage(registerBtnErrorDiv, "white", message);
        
        const redirectUrl = `${BASE_URL}/client/pages/auth/login.php`
        showRedirectMessage(registerBtnErrorDiv, ".", redirectUrl, 3, 1000);
    } else {
        showError(registerBtnErrorDiv, "Could not register user");
    }
});

validateRealTime(registerFields);
