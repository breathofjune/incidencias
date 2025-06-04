document.addEventListener('DOMContentLoaded', () => {
    const input = document.getElementById('nuevas_imagenes');
    const contador = document.getElementById('contador-imagenes');

    if (input && contador) {
        input.addEventListener('change', () => {
            const num = input.files.length;
            if (num === 0) {
                contador.textContent = "Ningún archivo seleccionado";
            } else if (num === 1) {
                contador.textContent = "1 archivo seleccionado";
            } else {
                contador.textContent = `${num} archivos seleccionados`;
            }
        });
    }
});
