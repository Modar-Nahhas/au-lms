
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

