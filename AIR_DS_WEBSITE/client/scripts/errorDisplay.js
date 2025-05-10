export function showError(element, message) {
    element.innerText = message;
    // element.style.color = "red";
    element.style.visibility = "visible";
}

export function clearError(element) {
    element.style.visibility = "hidden";
}

