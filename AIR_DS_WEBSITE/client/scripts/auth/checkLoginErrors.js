import { fields } from "./fields.js";
import { validateSubmitTime, validateRealTime } from "./validationManager.js";
import { loginUser } from "./loginUser.js"
import { isPasswordValidLogin, isUsernameValidLogin } from "./fieldValidatorFunctions.js";


const loginFields = {
    // make a shallow copy the relative field and add an extra property
    // it means that the field is used for login, and should be checked accordingly
   "username": {...fields["username"], validatorFunction: isUsernameValidLogin},
   "password": {...fields["password"], validatorFunction: isPasswordValidLogin}
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
    // if (!isAllValid) return;
  
    // document.getElementById('registration-form').requestSubmit();
    const values = {};

    for (const key in loginFields) {
        const field = loginFields[key];
        const element = document.getElementById(field.inputId);
        values[element.name] = element.value;
    }
   
    
    // TODO maybe modify to do something with error message
    const isLoggedIn = await loginUser(values, BASE_URL);

    if (isLoggedIn) {
        // redirect to login page
        // window.location.replace(`${BASE_URL}/client/pages/auth/login.php`)

        // DELETE LATER
        // window.location.href = `${BASE_URL}/client/pages/auth/login.php`;  
    } else {
        // console.error("registration failed")
    }
});

validateRealTime(loginFields);


