export function showError(element, message) {
    element.innerText = message;
    // maybe not needed, this is done from css
    element.style.color = "red";
    element.style.visibility = "visible";
}

export function clearError(element) {
    element.style.visibility = "hidden";
}

export function showMessage(element, color, message) {
    element.innerText = message;
    element.style.color = color;
    element.style.visibility = "visible";
}

export function hideMessage(element) {
    element.style.visibility = "hidden";
}

export function appendMessage(element, messageToAppend) {
    element.innerText += messageToAppend;
}

export function showRedirectMessage(messageElement, appendedMessage, url, repeats, timeBetweenRepeats) {
    let countDown = repeats;

    const intervalId = setInterval(() => {
        appendMessage(messageElement, appendedMessage);
        countDown--;

        if (countDown === 0){
            clearInterval(intervalId);
            window.location.replace(url);
        }
    }, timeBetweenRepeats);
}