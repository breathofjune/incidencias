let incidenciasTotales = [];

function cargarIncidencias() {
    fetch('api/incidencias.php')
        .then(res => res.json())
        .then(data => {
            const container = document.getElementById('incidencias-container');
            container.innerHTML = '';

            if (Array.isArray(data)) {
                if (data.length === 0) {
                    container.innerHTML = '<p>No tienes incidencias registradas.</p>';
                } else {
                    incidenciasTotales = data;

                    // Ordenar por estado: abierta > en proceso > cerrada
                    const ordenEstados = {
                        'abierta': 0,
                        'en proceso': 1,
                        'cerrada': 2
                    };

                    incidenciasTotales.sort((a, b) => ordenEstados[a.estado] - ordenEstados[b.estado]);

                    aplicarFiltros(); // Mostrar después de ordenar
                }
            } else if (data.message) {
                container.innerHTML = `<p>${data.message}</p>`;
            } else if (data.error) {
                container.innerHTML = `<p style="color:red;">Error: ${data.error}</p>`;
            }
        })
        .catch(err => {
            console.error('Error al obtener incidencias:', err);
            document.getElementById('incidencias-container').innerHTML = '<p style="color:red;">Error al cargar las incidencias.</p>';
        });
}

function aplicarFiltros() {
    const filtro = document.getElementById('filtro-estado')?.value || 'todos';
    const textoBusqueda = document.getElementById('buscador')?.value.toLowerCase() || '';

    const container = document.getElementById('incidencias-container');
    container.innerHTML = '';

    const filtradas = incidenciasTotales.filter(inc => {
        const coincideEstado = filtro === 'todos' || inc.estado === filtro;
        const coincideTexto = inc.titulo.toLowerCase().includes(textoBusqueda) || inc.descripcion.toLowerCase().includes(textoBusqueda) || inc.localizacion.toLowerCase().includes(textoBusqueda);
        return coincideEstado && coincideTexto;
    });

    if (filtradas.length === 0) {
        container.innerHTML = '<p>No se encontraron incidencias con estos filtros.</p>';
        return;
    }

    const list = document.createElement('ul');
    list.classList.add('grid-incidencias');

    filtradas.forEach(inc => {
        const li = document.createElement('li');
        li.classList.add(
            'tarjeta-incidencia',
            `estado-${inc.estado.replace(/\s/g, '-').toLowerCase()}`
        );

        li.innerHTML = `
            <strong>${inc.titulo}</strong><br>
            ${inc.descripcion.replace(/\n/g, '<br>')}<br>
            <em>Ubicación: ${inc.localizacion}</em><br>
            <em>Estado: ${inc.estado}</em><br>
            <div class="acciones-incidencia">
                <button class="boton boton-editar" onclick="editarIncidencia(${inc.id})">Editar</button>
                <button class="boton boton-borrar" onclick="eliminarIncidencia(${inc.id})">Eliminar</button>
            </div>
        `;

        list.appendChild(li);
    });

    container.appendChild(list);
}

function editarIncidencia(id) {
    window.location.href = `editar_incidencia.php?id=${id}`;
}

function eliminarIncidencia(id) {
    if (confirm('¿Estás seguro de que deseas eliminar esta incidencia?')) {
        window.location.href = `eliminar_incidencia.php?id=${id}`;
    }
}

document.addEventListener('DOMContentLoaded', () => {
    document.getElementById('filtro-estado')?.addEventListener('change', aplicarFiltros);
    document.getElementById('buscador')?.addEventListener('input', aplicarFiltros);

    cargarIncidencias();
});
