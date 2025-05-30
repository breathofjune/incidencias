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
</head>

<body>
    <h1>Bienvenido, <?= htmlspecialchars($_SESSION['username']) ?>!</h1>

    <a href="crear_incidencia.php">+ Nueva incidencia</a> |
    <a href="logout.php">Cerrar sesión</a>

    <h2>Mis incidencias</h2>

    <div id="incidencias-container">
        <p>Cargando incidencias...</p>
    </div>

    <script src="js/dashboard.js"></script>
</body>

</html>