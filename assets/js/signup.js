const alpha20 = /^[A-Za-z\s'-]{1,20}$/;
const strongPw = /^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[^a-zA-Z0-9])(?!.*\s).{8,15}$/;

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

    const signupForm = document.getElementById('signup-form');
    if (signupForm) {
        signupForm.addEventListener('submit', function (e) {
            let valid = true;

            const firstName = document.getElementById('firstName');
            const lastName = document.getElementById('lastName');
            const email = document.getElementById('signupEmail');
            const pass = document.getElementById('signupPassword');
            const confirm = document.getElementById('signupPasswordConfirm');

            [firstName, lastName, email, pass, confirm].forEach(input => clearInvalid(input));

            // First name
            if (!firstName.value.trim()) {
                setInvalid(firstName, 'First name is required.');
                valid = false;
            } else if (!alpha20.test((firstName?.value || '').trim())) {
                setInvalid(firstName, 'First name must be between 1 and 20 characters.');
                valid = false;
            }


            // Last name
            if (!lastName.value.trim()) {
                setInvalid(lastName, 'Last name is required.');
                valid = false;
            }else if (!alpha20.test((lastName?.value || '').trim())) {
                setInvalid(lastName, 'Last name must be between 1 and 20 characters.');
                valid = false;
            }

            // Email (basic check via HTML5)
            if (!email.value.trim() || !email.checkValidity()) {
                setInvalid(email, 'Please enter a valid email.');
                valid = false;
            }

            // Password length
            if (!pass.value) {
                setInvalid(pass, 'Password is required.');
                valid = false;
            }else if (!strongPw.test(pass.value)) {
                setInvalid(pass, 'Password must be at least 8 characters long and contain at least one uppercase letter, one lowercase letter, one number, and one special character.');
                valid = false;
            }

            // Confirm password
            if (!confirm.value || confirm.value !== pass.value) {
                setInvalid(confirm, 'Passwords must match.');
                valid = false;
            }

            if (!valid) {
                e.preventDefault();
            }
        });
    }
});