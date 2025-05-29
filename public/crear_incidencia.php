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
</head>

<body>
    <h1>Nueva Incidencia</h1>
    <form method="post">
        <label>Título:<br><input type="text" name="titulo" required></label><br><br>
        <label>Descripción:<br><textarea name="descripcion" required></textarea></label><br>
        <label>Localización:<br><input type="text" name="localizacion" required></label><br>
        <br>
        <button type="submit">Crear</button>
    </form>
    <p><?= htmlspecialchars($message) ?></p>
    <a href="dashboard.php">Volver al panel</a>
</body>

</html>