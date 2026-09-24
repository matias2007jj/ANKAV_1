// login.js - Mostrar u ocultar la contraseña al hacer clic

document.addEventListener('DOMContentLoaded', function () {
    const toggle = document.getElementById('toggle-password');
    const passwordInput = document.getElementById('password');

    if (toggle && passwordInput) {
        toggle.addEventListener('click', function () {
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggle.textContent = 'Ocultar contraseña';
            } else {
                passwordInput.type = 'password';
                toggle.textContent = 'Mostrar contraseña';
            }
        });
    }
});
