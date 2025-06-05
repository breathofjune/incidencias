document.getElementById('formPerfil').addEventListener('submit', function (e) {
    const errores = [];

    const username = document.getElementById('username').value.trim();
    const pwd_actual = document.getElementById('pwd_actual').value;
    const pwd_nueva = document.getElementById('pwd_nueva').value;
    const pwd_repetir = document.getElementById('pwd_repetir').value;

    const patronUsername = /^[A-Za-z][A-Za-z0-9_]{4,14}$/;
    if (!patronUsername.test(username)) {
        errores.push('El nombre de usuario debe tener entre 5 y 15 caracteres, empezar por una letra y solo usar letras, números o guiones bajos.');
    }

    const patronPassword = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{6,}$/;
    const cambiandoPassword = pwd_nueva !== '' || pwd_repetir !== '' || pwd_actual !== '';

    if (cambiandoPassword) {
        if (pwd_actual === '') {
            errores.push('Debes introducir tu contraseña actual para cambiarla.');
        }

        if (!patronPassword.test(pwd_nueva)) {
            errores.push('La nueva contraseña debe tener al menos 6 caracteres, una mayúscula, una minúscula y un número.');
        }

        if (pwd_nueva !== pwd_repetir) {
            errores.push('Las contraseñas nuevas no coinciden.');
        }
    }

    if (errores.length > 0) {
        e.preventDefault();
        const divErrores = document.getElementById('erroresJS');
        divErrores.innerHTML = '<ul>' + errores.map(error => `<li>${error}</li>`).join('') + '</ul>';
    }
});
