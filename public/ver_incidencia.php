<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

require_once __DIR__ . '/../src/db.php';

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id <= 0) {
    echo "ID de incidencia no válido.";
    exit;
}

// Obtener la incidencia
$stmt = $db->prepare("SELECT * FROM incidencias WHERE id = :id AND user_id = :user_id");
$stmt->execute([
    ':id' => $id,
    ':user_id' => $_SESSION['user_id']
]);
$incidencia = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$incidencia) {
    echo "Incidencia no encontrada.";
    exit;
}

// Obtener imágenes asociadas
$stmtImgs = $db->prepare("SELECT ruta FROM imagenes WHERE incidencia_id = :id");
$stmtImgs->execute([':id' => $id]);
$imagenes = $stmtImgs->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Detalle de Incidencia</title>
    <link rel="stylesheet" href="css/styles.css">
</head>

<body>
    <h1>Detalles de la Incidencia</h1>

    <p><strong>Título:</strong> <?= htmlspecialchars($incidencia['titulo']) ?></p>
    <p><strong>Descripción:</strong><br><?= nl2br(htmlspecialchars($incidencia['descripcion'])) ?></p>
    <p><strong>Localización:</strong> <?= htmlspecialchars($incidencia['localizacion']) ?></p>
    <p><strong>Estado:</strong> <?= htmlspecialchars($incidencia['estado']) ?></p>
    <p><strong>Fecha de creación:</strong> <?= htmlspecialchars($incidencia['fecha_creacion']) ?></p>
    <?php if ($incidencia['fecha_modificacion']): ?>
        <p><strong>Fecha de modificación:</strong> <?= htmlspecialchars($incidencia['fecha_modificacion']) ?></p>
    <?php endif; ?>

    <?php if (!empty($imagenes)): ?>
        <h3>Imágenes asociadas</h3>
        <div style="display: flex; flex-wrap: wrap; gap: 10px;">
            <?php foreach ($imagenes as $img): ?>
                <?php if (!empty($img['ruta'])): ?>
                    <img src="<?= htmlspecialchars($img['ruta']) ?>" alt="Imagen"
                        style="max-width: 200px; height: auto; border: 1px solid #ccc;">
                <?php endif; ?>

            <?php endforeach; ?>
            <?php foreach ($imagenes as $img): ?>
                <p>Ruta guardada: <?= $img['ruta'] ?></p>
                <img src="<?= $img['ruta'] ?>" style="max-width: 200px;">
            <?php endforeach; ?>

        </div>
    <?php else: ?>
        <p>No hay imágenes asociadas a esta incidencia.</p>
    <?php endif; ?>

    <br>
    <button onclick="window.location.href='dashboard.php'">Volver al panel</button>
</body>

</html>