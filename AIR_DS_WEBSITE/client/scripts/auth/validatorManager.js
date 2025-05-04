/**
 * @fileoverview
 * 
 */


export async function setUpSubmitTimeValidation(fields) {
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
        
        return isAllValid;
    }
}



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
export function setUpRealTimeValidation(fields) {
    for(const field of fields) {
        const inputElement = document.getElementById(field.inputId);
        const errorElement = document.getElementById(field.errorId);

        inputElement.addEventListener(field.event, async (e) => {
            if(field.isAsync) await field.validatorFunction(inputElement, errorElement);
            else field.validatorFunction(inputElement, errorElement);
        });
    }
}
