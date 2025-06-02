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
    <title>Panel de Usuario</title>
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
    </nav>
    <h2>Mis incidencias</h2>

    <div id="incidencias-container">
        <p>Cargando incidencias...</p>
    </div>

    <script src="js/dashboard.js"></script>
</body>

</html>