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

        // const isAsync = field.isAsync;
        let isValid = true;

        if (field.isAsync) isValid = await field.validatorFunction(inputElements, errorElements);
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
    let inputElements = 0;
    let errorElements = 0;

    for (const key in fields) {
        const field = fields[key];
        if (field.isCollection) {
            inputElements = getCollection(field.inputId);
            errorElements = getCollection(field.errorId);
            applyEventListeners(inputElements, errorElements, field.event, field.isASync, field.validatorFunction, field.isCollection);
        } else {           
            inputElements = document.getElementById(field.inputId);
            errorElements = document.getElementById(field.errorId);
            // console.log(field.isAsync, field.validatorFunction, field.isCollection);
            // console.log(field.event);
            applyEventListeners(inputElements, errorElements, field.event, field.isAsync, field.validatorFunction, field.isCollection);
        }
    }
}

function applyEventListeners(inputElements, errorElements, event, isAsync, validatorFunction, isCollection) {
    /**
     * Apply the event listener on each element of the collection
     * ATTENTION!
     * The validator function needs the whole collection as perameter 
     * don't just pass it the element to which the eventListener is added 
     */
    if(isCollection) {
        for (let i = 0; i < inputElements.length; i++) {
            // apply the event listener to the current inputElement
            inputElements[i].addEventListener(event, async (e) => {
                // pass all the input elements (and error) of the collection to the event listener 
                // (don't just pass the current element)
                if(isAsync) await validatorFunction(inputElements, errorElements);
                else validatorFunction(inputElements, errorElements);
            })
        }
        return;
    }

    // if inputElement not a collection
    inputElements.addEventListener(event, async (e) => {
        if(isAsync) await validatorFunction(inputElements, errorElements);
        else validatorFunction(inputElements, errorElements);
    });
}

/**
 * 
 * @param {Array} ids - an array containing ids as strings  
 * @returns {Array} - an array containing all the dom elements that have the ids from the parameter array
 */
function getCollection(ids) {
    const elements = [];
    for (const current in ids) {
        const id = ids[current];
        elements.push(document.getElementById(id));
    }
    return elements;
}