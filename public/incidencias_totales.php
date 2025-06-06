<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$username = $_SESSION['username'];
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Incidencias totales</title>
    <link rel="stylesheet" href="css/dashboard.css">
    <link rel="stylesheet" href="css/styles.css">
</head>

<body>
    <h1>Bienvenido, <?= htmlspecialchars($_SESSION['username']) ?>!</h1>

    <nav class="acciones">
        <form action="crear_incidencia.php" method="get">
            <button type="submit" class="boton boton-verde">+ Nueva incidencia</button>
        </form>
        <form action="logout.php" method="post">
            <button type="submit" class="boton boton-rojo">Cerrar sesión</button>
        </form>
        <form action="editar_perfil.php" method="get">
            <button type="submit" class="boton boton-azul">Editar perfil</button>
        </form>
        <button type="button" class="boton boton-azul" onclick="window.location.href='dashboard.php';">Volver al panel</button>
    </nav>
    <h2>Todas las incidencias</h2>

    <div id="filtros-dashboard">
        <label for="filtro-estado">Filtrar por estado:</label>
        <select id="filtro-estado">
            <option value="todos">Todos</option>
            <option value="abierta">Abiertas</option>
            <option value="en proceso">En proceso</option>
            <option value="cerrada">Cerradas</option>
        </select>

        <label for="buscador">Buscar:</label>
        <input type="text" id="buscador" placeholder="Buscar por título, descripción o ubicación...">
    </div>

    <div id="incidencias-container"></div>
    <div id="paginacion-container" class="paginacion"></div>


    <div id="incidencias-container"></div>

    <script src="js/incidencias_totales.js"></script>
</body>

</html>