fetch('api/incidencias.php')
    .then(res => res.json())
    .then(data => {
        const container = document.getElementById('incidencias-container');
        container.innerHTML = '';

        if (Array.isArray(data)) {
            if (data.length === 0) {
                container.innerHTML = '<p>No tienes incidencias registradas.</p>';
            } else {
                const list = document.createElement('ul');
                list.classList.add('grid-incidencias');

                data.forEach(inc => {
                    const li = document.createElement('li');
                    li.classList.add('tarjeta-incidencia');

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

// Funciones para los botones
function editarIncidencia(id) {
    window.location.href = `editar_incidencia.php?id=${id}`;
}

function eliminarIncidencia(id) {
    if (confirm('¿Estás seguro de que deseas eliminar esta incidencia?')) {
        // Usamos redirección simple, pero podrías hacerlo por fetch también
        window.location.href = `eliminar_incidencia.php?id=${id}`;
    }
}
