import { registerUser } from "./registerUser.js";
import { isNameValid, isUsernameValid, isPasswordValid, isEmailValid } from "./fieldValidatorFunctions.js";

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
const fields = [
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


// get the register  button
const registerBtn = document.getElementById('register-button');

// add event listener to the register button to validate the values of the form fields
// if by chance any field has an invalid value do not register
// else submit form and register
registerBtn.addEventListener('click', async (e) => {
    // if the button is clicked without any field being checked do nothing
    e.preventDefault();

    // assume that all fields are valid ( for now ;) )
    let isAllValid = true;

    // loop through all the fields and check if they are valid
    for (const field of fields) {
        const inputElement = document.getElementById(field.inputId)
        const errorElement = document.getElementById(field.errorId);
        const isAsync = field.isAsync;
        let isValid = true;

        if (isAsync) // if the function is async await its response
            isValid = await field.validatorFunction(inputElement, errorElement);
        else
            isValid = field.validatorFunction(inputElement, errorElement);

        // if the field is not valid then set that not all fields are valid
        if (!isValid) {
            isAllValid = false;
            break;  // no need to check the rest of the elemenets, form won't be submitted
        }
    }

    // only if all the fields are valid submit the form
    if (isAllValid) {
        // document.getElementById('registration-form').requestSubmit();
        const values = {};
        for(const field of fields) {
            const element = document.getElementById(field.inputId);
            values[element.name] = element.value;
        }
        
        registerUser(values);

        // change later to have correct fields not strings
        // registerUser("dfgh", "dfgh", "sdfg", "s1sdsd", "asdf@sdf.com");

        // redirect to login page
        window.location.replace(`${BASE_URL}/client/pages/auth/login.php`)
    }
});


/**
* function responsible for the adding validation checks on the <input> field with id = inputId
* 
* This function performs:
*  Real time evaluation, by adding an event listener on the <input> element
*  Submit time evaluation, by returning true or false
*  @param {JSON} - A JSON containing:
* - inputId: {string} the id of the <input> element on which the validation is applied
* - errorId: {string} the id of the <div> element containing the error message for the <input> element
* - event: {string} the event to pass on to the eventListener
* - validatorFunction: {function} the function that performs the validation checks
* - isAsync: {boolean} true if the validator function is asynchronous false otherwise
* @returns {boolean} - true if the password is alright, false otherwise
*/
function setUpValidation() {
    for(const field of fields) {
        const inputElement = document.getElementById(field.inputId);
        const errorElement = document.getElementById(field.errorId);

        inputElement.addEventListener(field.event, async (e) => {
            if(field.isAsync) await field.validatorFunction(inputElement, errorElement);
            else field.validatorFunction(inputElement, errorElement);
        });
    }
}


// TODO CHANGE LATER TO NOT MAKE CALL
// Call the function
setUpValidation();
