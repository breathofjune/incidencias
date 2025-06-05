document.querySelector('form').addEventListener('submit', function (e) {
    const errores = [];

    const username = document.querySelector('input[name="username"]').value.trim();
    const password = document.querySelector('input[name="password"]').value;
    const pwdRepeat = document.getElementById('password_repeat').value;

    const patronUsername = /^[A-Za-z][A-Za-z0-9_]{4,14}$/;
    const patronPassword = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{6,}$/;

    if (!patronUsername.test(username)) {
        errores.push('El nombre de usuario debe tener entre 5 y 15 caracteres, empezar por una letra y solo usar letras, números o guiones bajos.');
    }

    if (!patronPassword.test(password)) {
        errores.push('La contraseña debe tener al menos 6 caracteres, una mayúscula, una minúscula y un número.');
    }

    if (password !== pwdRepeat) {
        errores.push('Las contraseñas no coinciden.');
    }

    let mensajeDiv = document.getElementById('erroresJS');
    if (!mensajeDiv) {
        mensajeDiv = document.createElement('div');
        mensajeDiv.id = 'erroresJS';
        mensajeDiv.style.color = 'red';
        const container = document.querySelector('.auth-container');
        container.insertBefore(mensajeDiv, container.children[2]);
    }
    mensajeDiv.innerHTML = '';

    if (errores.length > 0) {
        e.preventDefault();
        mensajeDiv.innerHTML = '<ul>' + errores.map(e => `<li>${e}</li>`).join('') + '</ul>';
    }
});
