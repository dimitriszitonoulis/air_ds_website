export const fields = {
    'airports': {
        inputId: ['departure-airport-input', 'destination-airport-input'],
        errorId: ['departure-airport-error-message', 'destination-aiport-error-message'],
        event: 'change',
        validatorFunction: undefined,
        // TODO maybe true
        isAsync: false, 
        isCollection: true
    },
    'date': {
        inputId: 'date-input',
        errorId: 'date-error-message',
        event: 'change',
        validatorFunction: undefined,
        isAsync: false,
        isCollection: false
    },
    'ticket': {
        inputId: 'ticket-input',
        errorId: 'ticket-error-message',
        event: 'change',
        validatorFunction: undefined,
        isAsync: false,
        isCollection: false
    }
}