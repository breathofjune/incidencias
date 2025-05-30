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
                data.forEach(inc => {
                    const li = document.createElement('li');
                    li.innerHTML = `
                        <strong>${inc.titulo}</strong><br>
                        ${inc.descripcion.replace(/\n/g, '<br>')}<br>
                        <em>Ubicación: ${inc.localizacion}</em><br>
                        <em>Estado: ${inc.estado}</em><br>
                        <div class="acciones">
                            <a href="editar_incidencia.php?id=${inc.id}">Editar</a>
                            <a href="eliminar_incidencia.php?id=${inc.id}" onclick="return confirm('¿Estás seguro de que deseas eliminar esta incidencia?');">Eliminar</a>
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
