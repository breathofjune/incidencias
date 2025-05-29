<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

require_once __DIR__ . '/../src/db.php';

$stmt = $db->prepare("SELECT * FROM incidencias WHERE user_id = :user_id ORDER BY fecha_creacion DESC");
$stmt->execute([':user_id' => $_SESSION['user_id']]);
$incidencias = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Mis Incidencias</title>
</head>
<body>
    <h1>Listado de mis incidencias</h1>
    <p><a href="dashboard.php">Volver al panel</a> | <a href="create_incidencia.php">Crear nueva incidencia</a></p>
    <?php if (count($incidencias) === 0): ?>
        <p>No tienes incidencias registradas.</p>
    <?php else: ?>
        <table border="1" cellpadding="5" cellspacing="0">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Título</th>
                    <th>Estado</th>
                    <th>Fecha creación</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($incidencias as $inc): ?>
                    <tr>
                        <td><?= $inc['id'] ?></td>
                        <td><?= htmlspecialchars($inc['titulo']) ?></td>
                        <td><?= htmlspecialchars($inc['estado']) ?></td>
                        <td><?= $inc['fecha_creacion'] ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</body>
</html>
