import { fields } from "./fields.js";
import { validateSubmitTime } from "./validationManager.js";
import { loginUser } from "./loginUser.js"
import { isPasswordValid, isUsernameValidLogin } from "./fieldValidatorFunctions.js";

// take the necessary fields from fields global variable and add their validator fucntions
// attention create shallow copy, do not modify the global variable
const loginFields = {
   "username": {...fields["username"], validatorFunction: isUsernameValidLogin},
//    "username": {...fields["username"], validatorFunction: isUsernameInputValid},
//    "password": {...fields["password"], validatorFunction: isPasswordValidLogin}
   "password": {...fields["password"], validatorFunction: isPasswordValid}
};


const loginBtn = document.getElementById('login-button');

// add event listener to the register button to validate the values of the form fields
// if by chance any field has an invalid value do not register
// else submit form and register
loginBtn.addEventListener('click', async (e) => {
    // if the button is clicked without any field being checked do nothing
    e.preventDefault();
   
    const isAllValid = await validateSubmitTime(loginFields);

    // TODO uncomment the check later
    // if a field is invalid do nothing
    if (!isAllValid) return;
  
    const values = {};
    for (const key in loginFields) {
        const field = loginFields[key];
        const element = document.getElementById(field.inputId);
        values[element.name] = element.value;
    }
   
    // TODO maybe modify to do something with error message
    const isLoggedIn = await loginUser(values, BASE_URL);

    // Add code that fields are invalid if not alredy logged in 
    if (isLoggedIn) {
        // TODO uncomment later

        // redirect to login page
        // window.location.replace(`${BASE_URL}/client/pages/auth/login.php`)

        // DELETE LATER
        // window.location.href = `${BASE_URL}/client/pages/auth/login.php`;  
    } else {
        // console.error("registration failed")
    }
});
