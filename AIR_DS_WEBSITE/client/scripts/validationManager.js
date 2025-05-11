/**
 * @fileoverview
 * 
 * This file contains functions that apply the validator functions to the fields they validate
 * 
 */

/**
 * This function runs the validator functions on the fields they validate
 * If a field is invalid false is returned
 * 
 * 
 * @param {object} fields - An object containing:
 *  - inputId: {string} the id of the <input> element on which the validation is applied
 *  - errorId: {string} the id of the <div> element containing the error message for the <input> element
 *  - event: {string} the event to pass on to the eventListener
 *  - validatorFunction: {function} the function that performs the validation checks
 *  - isAsync: {boolean} true if the validator function is asynchronous false otherwise
* @returns {boolean} - true every field is valid, false otherwise
 */
export async function validateSubmitTime(fields) {
    let isAllValid = true;
    for (const key in fields) {
        const field = fields[key];
        let inputElements = 0;
        let errorElements = 0

        // if mutliple fields must be examined by the same validator function (ex choice of airports)
        if (field.isCollection === true) {
            // get a bunch of fields and pass them to the validator function
            // how they will be treated is the validator's responsibility
            inputElements = getCollection(field.inputId);
            errorElements = getCollection(field.errorId);
        } else {
            inputElements = document.getElementById(field.inputId);
            errorElements = document.getElementById(field.errorId);
        }

        const isAsync = field.isAsync;
        let isValid = true;

        if (isAsync) isValid = await field.validatorFunction(inputElements, errorElements);
        else isValid = field.validatorFunction(inputElements, errorElements);

        if (!isValid) 
            isAllValid = false;
    }
    return isAllValid;
}

/**
* This function performs:
*  - Real time evaluation, by adding an event listener on the <input> element
*  - Submit time evaluation, by returning true or false

*  @param {object} fields - An object containing:
* - inputId: {string} the id of the <input> element on which the validation is applied
* - errorId: {string} the id of the <div> element containing the error message for the <input> element
* - event: {string} the event to pass on to the eventListener
* - validatorFunction: {function} the function that performs the validation checks
* - isAsync: {boolean} true if the validator function is asynchronous false otherwise
*/
export function validateRealTime(fields) {
    for (const key in fields) {
        const field = fields[key];
        const inputElement = document.getElementById(field.inputId);
        const errorElement = document.getElementById(field.errorId);
        
        inputElement.addEventListener(field.event, async (e) => {
            if(field.isAsync) await field.validatorFunction(inputElement, errorElement);
            else field.validatorFunction(inputElement, errorElement);
        });
    }
}




function getCollection(ids) {
    const elements = [];
    for (const current in ids) {
        const id = ids[current];
        elements.push(getElementById(id));
    }
    return elements;
}
