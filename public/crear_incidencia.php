<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

require_once __DIR__ . '/../src/db.php';

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo = trim($_POST['titulo'] ?? '');
    $descripcion = trim($_POST['descripcion'] ?? '');
    $localizacion = trim($_POST['localizacion'] ?? '');

    if ($titulo && $descripcion) {
        $stmt = $db->prepare("INSERT INTO incidencias (titulo, descripcion, localizacion, user_id) VALUES (:titulo, :descripcion, :localizacion, :user_id)");
        $stmt->execute([
            ':titulo' => $titulo,
            ':descripcion' => $descripcion,
            ':localizacion' => $localizacion,
            ':user_id' => $_SESSION['user_id']
        ]);

        $incidencia_id = $db->lastInsertId();

        if (!empty($_FILES['imagenes']['name'][0])) {
            $uploadDir = __DIR__ . '/uploads/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            foreach ($_FILES['imagenes']['tmp_name'] as $index => $tmpName) {
                $nombreOriginal = basename($_FILES['imagenes']['name'][$index]);
                $extension = pathinfo($nombreOriginal, PATHINFO_EXTENSION);
                $nombreSeguro = uniqid('img_', true) . '.' . strtolower($extension);
                $rutaDestino = $uploadDir . $nombreSeguro;

                if (move_uploaded_file($tmpName, $rutaDestino)) {
                    $stmtImg = $db->prepare("INSERT INTO imagenes (incidencia_id, ruta) VALUES (:incidencia_id, :ruta)");
                    $stmtImg->execute([
                        ':incidencia_id' => $incidencia_id,
                        ':ruta' => 'uploads/' . $nombreSeguro
                    ]);
                }
            }
        }

        $message = "Incidencia creada correctamente.";
        header('Location: dashboard.php');
    } else {
        $message = "Por favor, rellena todos los campos.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Crear Incidencia</title>
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="css/formularios.css">
</head>

<body>
    <h1>Nueva Incidencia</h1>

    <div id="mensaje-confirmacion" class="oculto"></div>
    <form action="crear_incidencia.php" method="POST" enctype="multipart/form-data">
        <label>Título:<br><input type="text" name="titulo" required></label><br><br>
        <label>Descripción:<br><textarea name="descripcion" required></textarea></label><br>
        <label>Localización:<br><input type="text" name="localizacion" required></label><br>
        <label for="imagenes">Imágenes:</label>
        <input type="file" name="imagenes[]" id="imagenes" multiple accept="image/*">
        <br>
        <button type="submit">Crear</button>
        <button type="button" onclick="window.location.href='dashboard.php';">Volver al panel</button>
    </form>

    <p><?= htmlspecialchars($message) ?></p>

    <script src="js/formularios.js"></script>

</body>

</html>