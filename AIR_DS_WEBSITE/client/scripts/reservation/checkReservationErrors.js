

const reservationFields = {

};

const buyTicketBtn = document.getElementById('buy-tickets-button');
buyTicketBtn.addEventListener('click', async (e) => {
    // if the button is clicked without any field being checked do nothing
    e.preventDefault();

    const isAllValid = await validateSubmitTime(reservationFields);

    // if a field is invalid do nothing
    // if (!isAllValid) return;

    // get the values from the form elements
    const values = getValues(reservationFields);

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

function getValues(reservationFields) {
    let values = {};
    for (const key in reservationFields) {
        const field = reservationFields[key];
        if (field.isCollection) {
            for (const current in field.inputId) {
                // inputId is array in this case
                const element = document.getElementById(field.inputId[current]);
                values[element.name] = element.value;
            }
        } else {
            const element = document.getElementById(field.inputId);
            values[element.name] = element.value;
        }
    }
}

