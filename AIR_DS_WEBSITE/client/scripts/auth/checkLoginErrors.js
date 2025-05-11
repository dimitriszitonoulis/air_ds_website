import { fields } from "./authFields.js";
import { validateSubmitTime } from "./validationManager.js";
import { loginUser } from "./loginUser.js"
import { isPasswordValid, isUsernameValidLogin } from "./authValidators.js";
import { showError, clearError, showMessage, showRedirectMessage } from "../messageDisplay.js";

// take the necessary fields from fields global variable and add their validator fucntions
// attention create shallow copy, do not modify the global variable
const loginFields = {
    "username": { ...fields["username"], validatorFunction: isUsernameValidLogin },
    //    "username": {...fields["username"], validatorFunction: isUsernameInputValid},
    //    "password": {...fields["password"], validatorFunction: isPasswordValidLogin}
    "password": { ...fields["password"], validatorFunction: isPasswordValid }
};


const loginBtn = document.getElementById('login-button');
const loginBtnErrorDiv = document.getElementById('login-button-error-message');

// add event listener to the register button to validate the values of the form fields
// if by chance any field has an invalid value do not register
// else submit form and register
loginBtn.addEventListener('click', async (e) => {
    // if the button is clicked without any field being checked do nothing
    e.preventDefault();

    const isAllValid = await validateSubmitTime(loginFields);

    if (!isAllValid) {
        showError(loginBtnErrorDiv, "Invalid credentials");
        // return;
    }

    // get the values from the form elements
    const values = {};
    for (const key in loginFields) {
        const field = loginFields[key];
        const element = document.getElementById(field.inputId);
        values[element.name] = element.value;
    }

    // TODO maybe modify to do something with error message
    const isLoggedIn = await loginUser(values, BASE_URL);
    // let isLoggedIn = await loginUser(values, BASE_URL);

    // TODO remove
    // isLoggedIn = true;

    // Add code that fields are invalid if not alredy logged in 
    if (isLoggedIn) {
        clearError(loginBtnErrorDiv);

        // TODO maybe redirect to my home and have a message there
        const message = `Logged in sucessfully\n You will be redirected to the home page shortly`
        showMessage(loginBtnErrorDiv, "white", message);

        const redirectUrl = `${BASE_URL}/client/pages/home.php`
        showRedirectMessage(loginBtnErrorDiv, ".", redirectUrl, 3, 1000);
    } else {
        showError(loginBtnErrorDiv, "Invalid credentials")
        // console.error("registration failed")
    }
});
