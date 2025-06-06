<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

require_once __DIR__ . '/../src/db.php';

$message = '';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die('Incidencia no válida.');
}

$incidencia_id = (int) $_GET['id'];

$stmt = $db->prepare("SELECT * FROM incidencias WHERE id = :id");
$stmt->execute([':id' => $incidencia_id]);
$incidencia = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$incidencia) {
    die('Incidencia no encontrada o acceso denegado.');
}

// Procesar el formulario de edición
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $titulo = trim($_POST['titulo'] ?? '');
    $descripcion = trim($_POST['descripcion'] ?? '');
    $localizacion = trim($_POST['localizacion'] ?? '');
    $estado = trim($_POST['estado'] ?? '');

    if ($titulo && $descripcion && $localizacion && $estado) {
        $update = $db->prepare("UPDATE incidencias SET titulo = :titulo, descripcion = :descripcion, localizacion = :localizacion, estado = :estado, fecha_modificacion = CURRENT_TIMESTAMP, modificado_por = :modificado_por WHERE id = :id");
        $update->execute([
            ':titulo' => $titulo,
            ':descripcion' => $descripcion,
            ':localizacion' => $localizacion,
            ':estado' => $estado,
            ':id' => $incidencia_id,
            ':modificado_por' => $_SESSION['username']
        ]);
        $message = "Incidencia actualizada correctamente.";

        $stmt->execute([':id' => $incidencia_id]);
        $incidencia = $stmt->fetch(PDO::FETCH_ASSOC);
    } else {
        $message = "Por favor, rellena todos los campos.";
    }

    // Eliminar imágenes marcadas
    if (!empty($_POST['eliminar_imagenes']) && is_array($_POST['eliminar_imagenes'])) {
        foreach ($_POST['eliminar_imagenes'] as $imgId) {
            $stmt = $db->prepare("SELECT ruta FROM imagenes WHERE id = :id AND incidencia_id = :incidencia_id");
            $stmt->execute([
                ':id' => intval($imgId),
                ':incidencia_id' => $incidencia_id
            ]);
            $imagen = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($imagen && file_exists($imagen['ruta'])) {
                unlink($imagen['ruta']);
            }

            $stmt = $db->prepare("DELETE FROM imagenes WHERE id = :id AND incidencia_id = :incidencia_id");
            $stmt->execute([
                ':id' => intval($imgId),
                ':incidencia_id' => $incidencia_id
            ]);
        }
    }

    // Subir nuevas imágenes
    if (!empty($_FILES['nuevas_imagenes']['name'][0])) {
        $uploadDir = __DIR__ . '/uploads/';
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        foreach ($_FILES['nuevas_imagenes']['tmp_name'] as $index => $tmpName) {
            $originalName = basename($_FILES['nuevas_imagenes']['name'][$index]);
            $extension = pathinfo($originalName, PATHINFO_EXTENSION);

            $uniqueName = uniqid('img_', true) . '.' . $extension;
            $destination = $uploadDir . $uniqueName;

            if (move_uploaded_file($tmpName, $destination)) {
                $rutaBD = 'uploads/' . $uniqueName;

                $stmt = $db->prepare("INSERT INTO imagenes (incidencia_id, ruta) VALUES (:incidencia_id, :ruta)");
                $stmt->execute([
                    ':incidencia_id' => $incidencia_id,
                    ':ruta' => $rutaBD
                ]);
            }
        }
    }

    header("Location: editar_incidencia.php?id=$incidencia_id&exito=1");
    exit;
}

if (isset($_GET['exito']) && $_GET['exito'] == 1) {
    $message = '✅ Incidencia actualizada correctamente.';
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Editar Incidencia</title>
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="css/formularios.css">
</head>

<body>
    <h1>Editar Incidencia</h1>

    <?php if (!empty($message)): ?>
        <div id="mensaje-confirmacion" class="visible"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <form method="post" enctype="multipart/form-data">
        <label>Título:<br>
            <input type="text" name="titulo" required value="<?= htmlspecialchars($incidencia['titulo']) ?>">
        </label><br><br>

        <label>Descripción:<br>
            <textarea name="descripcion" required><?= htmlspecialchars($incidencia['descripcion']) ?></textarea>
        </label><br><br>

        <label>Localización:<br>
            <input type="text" name="localizacion" required
                value="<?= htmlspecialchars($incidencia['localizacion']) ?>">
        </label><br><br>

        <label>Estado:<br>
            <select name="estado" required>
                <?php
                $estados = ['abierta', 'en proceso', 'cerrada'];
                foreach ($estados as $estado_option) {
                    $selected = ($incidencia['estado'] === $estado_option) ? 'selected' : '';
                    echo "<option value=\"$estado_option\" $selected>" . ucfirst($estado_option) . "</option>";
                }
                ?>
            </select>
        </label><br>

        <h3>Imágenes actuales</h3>
        <?php
        $stmtImgs = $db->prepare("SELECT id, ruta FROM imagenes WHERE incidencia_id = :id");
        $stmtImgs->execute([':id' => $incidencia['id']]);
        $imagenes = $stmtImgs->fetchAll(PDO::FETCH_ASSOC);
        ?>

        <?php if (!empty($imagenes)): ?>
            <div class="galeria-imagenes">
                <?php foreach ($imagenes as $img): ?>
                    <div class="imagen-contenedor">
                        <img src="<?= htmlspecialchars($img['ruta']) ?>" alt="Imagen incidencia">
                        <label>
                            <input type="checkbox" name="eliminar_imagenes[]" value="<?= $img['id'] ?>">
                            Eliminar
                        </label>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p>No hay imágenes asociadas a esta incidencia.</p>
        <?php endif; ?>

        <h3>Subir nuevas imágenes</h3>
        <label for="nuevas_imagenes" class="boton-subir-imagenes">
            📷 Seleccionar imágenes
        </label>
        <input type="file" id="nuevas_imagenes" name="nuevas_imagenes[]" accept="image/*" multiple hidden>
        <span id="contador-imagenes">Ningún archivo seleccionado</span>

        <br>
        <button type="submit">Actualizar</button>
        <button type="button" onclick="window.location.href='dashboard.php';">Volver al panel</button>
    </form>

<script src="js/imagenes.js"></script>

</body>

</html>