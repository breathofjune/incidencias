let incidenciasTotales = [];
let paginaActual = 1;
const porPagina = 12;
let totalIncidencias = 0;

function formatearFecha(fecha) {
    const d = new Date(fecha);
    return d.toLocaleDateString('es-ES') + ' ' + d.toLocaleTimeString('es-ES', {hour: '2-digit', minute: '2-digit'});
}


function cargarIncidencias(pagina = 1) {
    const container = document.getElementById('incidencias-container');
    container.innerHTML = '<p>Cargando incidencias...</p>';
    fetch(`api/incidencias.php?page=${pagina}&limit=${porPagina}`)
        .then(res => res.json())
        .then(data => {
            if (data.error) {
                document.getElementById('incidencias-container').innerHTML = `<p style="color:red;">${data.error}</p>`;
                return;
            }
            incidenciasTotales = data.incidencias;
            totalIncidencias = data.total;
            paginaActual = data.page;

            const container = document.getElementById('incidencias-container');
            container.innerHTML = '';

            if (data.incidencias.length === 0) {
                container.innerHTML = '<p>No tienes incidencias registradas.</p>';
                return;
            }

            const list = document.createElement('ul');
            list.classList.add('grid-incidencias');

            data.incidencias.forEach(inc => {
                const li = document.createElement('li');
                li.classList.add('tarjeta-incidencia', `estado-${inc.estado.replace(/\s/g, '-').toLowerCase()}`);
                li.innerHTML = `
                    <strong>${inc.titulo}</strong><br>
                    ${inc.descripcion.replace(/\n/g, '<br>')}<br>
                    <em>Ubicación: ${inc.localizacion}</em><br>
                    <em>Estado: ${inc.estado}</em><br>
                    <em>Creada el: ${formatearFecha(inc.fecha_creacion)}</em><br>
                    ${inc.fecha_modificacion ? `<em>Modificada el: ${formatearFecha(inc.fecha_modificacion)}</em><br>` : ''}
                    <div class="acciones-incidencia">
                        <button class="boton boton-ver" onclick="verIncidencia(${inc.id})">Ver</button>
                        <button class="boton boton-editar" onclick="editarIncidencia(${inc.id})">Editar</button>
                        <button class="boton boton-borrar" onclick="eliminarIncidencia(${inc.id})">Eliminar</button>
                    </div>
                `;
                list.appendChild(li);
            });
            container.appendChild(list);

            mostrarPaginacion();
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
            <em>Creada el: ${formatearFecha(inc.fecha_creacion)}</em><br>
            ${inc.fecha_modificacion ? `<em>Modificada el: ${formatearFecha(inc.fecha_modificacion)}</em><br>` : ''}
            <div class="acciones-incidencia">
                <button class="boton boton-editar" onclick="editarIncidencia(${inc.id})">Editar</button>
                <button class="boton boton-borrar" onclick="eliminarIncidencia(${inc.id})">Eliminar</button>
            </div>
        `;

        list.appendChild(li);
    });

    container.appendChild(list);
}

function formatearFecha(fechaISO) {
    const fecha = new Date(fechaISO);
    return fecha.toLocaleString('es-ES', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
}

function verIncidencia(id) {
    window.location.href = `ver_incidencia.php?id=${id}`;
}

function editarIncidencia(id) {
    window.location.href = `editar_incidencia.php?id=${id}`;
}

function eliminarIncidencia(id) {
    if (confirm('¿Estás seguro de que deseas eliminar esta incidencia?')) {
        window.location.href = `eliminar_incidencia.php?id=${id}`;
    }
}

function mostrarPaginacion() {
    const container = document.getElementById('paginacion-container');
    if (!container) return;

    const totalPaginas = Math.ceil(totalIncidencias / porPagina);
    container.innerHTML = '';

    if (totalPaginas <= 1) return;

    // Botón Anterior
    const btnPrev = document.createElement('button');
    btnPrev.textContent = 'Anterior';
    btnPrev.disabled = paginaActual === 1;
    btnPrev.addEventListener('click', () => cargarIncidencias(paginaActual - 1));
    container.appendChild(btnPrev);

    // Botones numéricos
    for (let i = 1; i <= totalPaginas; i++) {
        const btn = document.createElement('button');
        btn.textContent = i;
        if (i === paginaActual) btn.disabled = true;
        btn.addEventListener('click', () => cargarIncidencias(i));
        container.appendChild(btn);
    }

    // Botón Siguiente
    const btnNext = document.createElement('button');
    btnNext.textContent = 'Siguiente';
    btnNext.disabled = paginaActual === totalPaginas;
    btnNext.addEventListener('click', () => cargarIncidencias(paginaActual + 1));
    container.appendChild(btnNext);
}

document.addEventListener('DOMContentLoaded', () => {
    document.getElementById('filtro-estado')?.addEventListener('change', aplicarFiltros);
    document.getElementById('buscador')?.addEventListener('input', aplicarFiltros);

    cargarIncidencias();
});