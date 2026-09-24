// Mostrar / ocultar contraseña
document.addEventListener('DOMContentLoaded', function () {
    var input = document.getElementById('password');
    var btn = document.getElementById('togglePassword');

    if (!input || !btn) return;

    btn.addEventListener('click', function () {
        var mostrar = input.type === 'password';

        input.type = mostrar ? 'text' : 'password';
        btn.classList.toggle('is-on', mostrar);
        btn.setAttribute('aria-label', mostrar ? 'Ocultar contraseña' : 'Mostrar contraseña');
    });
});
