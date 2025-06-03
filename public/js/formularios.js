function activarMensajeRedireccion(formSelector = 'form', mensaje = '✅ Formulario enviado correctamente. Redirigiendo...', milisegundos = 2500) {
    document.addEventListener('DOMContentLoaded', () => {
        const form = document.querySelector(formSelector);
        const mensajeDiv = document.getElementById('mensaje-confirmacion');

        if (!form || !mensajeDiv) return;

        form.addEventListener('submit', (e) => {
            e.preventDefault();

            mensajeDiv.textContent = mensaje;
            mensajeDiv.classList.remove('oculto');
            mensajeDiv.classList.add('visible');

            const boton = form.querySelector('button[type="submit"]');
            if (boton) boton.disabled = true;

            setTimeout(() => {
                form.submit();
            }, milisegundos);
        });
    });
}

activarMensajeRedireccion();