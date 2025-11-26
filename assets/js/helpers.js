
// Helper to mark a field invalid with message
function setInvalid(input, message) {
    input.classList.add('is-invalid');
    if (message) {
        const fb = input.parentElement.querySelector('.invalid-feedback');
        if (fb) fb.textContent = message;
        fb.classList.remove('d-none');
    }
}

function clearInvalid(input) {
    input.classList.remove('is-invalid');
    const fb = input.parentElement.querySelector('.invalid-feedback');
    if (fb) fb.textContent = '';
    fb.classList.add('d-none');
}

function showAdminMessage(message, type = "warning") {
    const box = document.getElementById("admin-message");
    if (!box) return;

    // Remove all possible alert-* classes
    box.classList.remove("alert-success", "alert-danger", "alert-warning", "alert-info");

    // Add the chosen alert class
    box.classList.add("alert-" + type);

    // Set the message
    box.textContent = message;

    // Show element
    box.classList.remove("d-none");
}

function hideAdminMessage() {
    const box = document.getElementById("admin-message");
    if (!box) return;

    box.textContent = "";
    box.classList.add("d-none");
}

