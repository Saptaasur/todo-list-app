document.addEventListener('DOMContentLoaded', function () {
    const form = document.querySelector('form');
    form.addEventListener('submit', function (event) {
        const password = form.querySelector('input[name="password"]');
        const confirmPassword = form.querySelector('input[name="confirm_password"]');
        if (password.value !== confirmPassword.value) {
            alert('Passwords do not match!');
            event.preventDefault();
        }
    });
});
