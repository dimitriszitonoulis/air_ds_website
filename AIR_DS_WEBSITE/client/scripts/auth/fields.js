import { isNameValid, isUsernameValid, isPasswordValid, isEmailValid } from "./fieldValidatorFunctions.js";

export const fields = {
    'name': {
        inputId: 'name-input',
        errorId: 'name-input-error-message',
        event: 'change',
        validatorFunction: isNameValid,
        isAsync: false
    },
    'surname': {
        inputId: 'surname-input',
        errorId: 'surname-input-error-message',
        event: 'change',
        validatorFunction: isNameValid,
        isAsync: false
    },
   'username':  {
        inputId: 'username-input',
        errorId: 'username-input-error-message',
        event: 'change',
        validatorFunction: isUsernameValid,
        isAsync: true
    },
    'password': {
        inputId: 'password-input',
        errorId: 'password-input-error-message',
        event: 'change',
        validatorFunction: isPasswordValid,
        isAsync: false
    },
    'email':{
        inputId: 'email-input',
        errorId: 'email-input-error-message',
        event: 'change',
        validatorFunction: isEmailValid,
        isAsync: false
    }
};