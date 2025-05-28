<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

require_once __DIR__ . '/../src/db.php';

$stmt = $db->prepare("SELECT * FROM incidencias WHERE user_id = :user_id ORDER BY id DESC");
$stmt->execute([':user_id' => $_SESSION['user_id']]);
$incidencias = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Panel de Usuario</title>
</head>

<body>
    <h1>Bienvenido, <?= htmlspecialchars($_SESSION['username']) ?>!</h1>

    <a href="crear_incidencia.php">+ Nueva incidencia</a> |
    <a href="logout.php">Cerrar sesión</a>

    <h2>Mis incidencias</h2>

    <?php if (count($incidencias) > 0): ?>
        <ul>
            <?php foreach ($incidencias as $inc): ?>
                <li>
                    <strong><?= htmlspecialchars($inc['titulo']) ?></strong><br>
                    <?= nl2br(htmlspecialchars($inc['descripcion'])) ?><br>
                    <em>Ubicación: <?= htmlspecialchars($inc['localizacion']) ?></em><br>
                    <em>Estado: <?= htmlspecialchars(ucfirst($inc['estado'])) ?></em><br>
                    <a href="editar_incidencia.php?id=<?= $inc['id'] ?>">Editar</a> |
                    <a href="eliminar_incidencia.php?id=<?= $inc['id'] ?>"
                        onclick="return confirm('¿Estás seguro de que deseas eliminar esta incidencia?');">Eliminar</a>
                </li>

            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <p>No has registrado ninguna incidencia todavía.</p>
    <?php endif; ?>
</body>

</html>