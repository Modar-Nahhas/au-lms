document.addEventListener('DOMContentLoaded', function () {

    document.querySelectorAll('.toggle-password').forEach(icon => {
        icon.addEventListener('click', function () {
            const target = document.querySelector(this.dataset.target);
            const isPassword = target.getAttribute('type') === 'password';

            target.setAttribute('type', isPassword ? 'text' : 'password');

            // Toggle icon
            const eye = this.querySelector('i');
            if (isPassword) {
                eye.classList.remove('fa-eye');
                eye.classList.add('fa-eye-slash');
            } else {
                eye.classList.remove('fa-eye-slash');
                eye.classList.add('fa-eye');
            }
        });
    });

    const loginForm = document.getElementById('login-form');
    if (loginForm) {
        loginForm.addEventListener('submit', function (e) {

            let valid = true;

            const email = document.getElementById('loginEmail');
            const pass = document.getElementById('loginPassword');

            [email, pass].forEach(input => clearInvalid(input));

            if (!email.value.trim() || !email.checkValidity()) {
                setInvalid(email, 'Please enter a valid email.');
                valid = false;
            }

            if (!pass.value.trim()) {
                setInvalid(pass, 'Password is required.');
                valid = false;
            }

            if (!valid) {
                e.preventDefault();
            }
        });
    }
});