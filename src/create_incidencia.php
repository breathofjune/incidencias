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

    if ($titulo && $descripcion) {
        $stmt = $db->prepare("INSERT INTO incidencias (user_id, titulo, descripcion) VALUES (:user_id, :titulo, :descripcion)");
        $stmt->execute([
            ':user_id' => $_SESSION['user_id'],
            ':titulo' => $titulo,
            ':descripcion' => $descripcion,
        ]);
        $message = "Incidencia creada con éxito.";
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
    <h1>Crear nueva incidencia</h1>
    <form method="post">
        <label>Título:<br><input type="text" name="titulo" required></label><br><br>
        <label>Descripción:<br><textarea name="descripcion" rows="5" cols="30" required></textarea></label><br><br>
        <button type="submit">Crear incidencia</button>
    </form>
    <p><?= htmlspecialchars($message) ?></p>
    <p><a href="dashboard.php">Volver al panel</a> | <a href="list_incidencias.php">Ver mis incidencias</a></p>
</body>
</html>
