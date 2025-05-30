<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

require_once __DIR__ . '/../src/db.php';

$message = '';

// Validar que venga id por GET y sea numérico
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die('Incidencia no válida.');
}

$incidencia_id = (int) $_GET['id'];

// Comprobar que la incidencia pertenece al usuario
$stmt = $db->prepare("SELECT * FROM incidencias WHERE id = :id AND user_id = :user_id");
$stmt->execute([':id' => $incidencia_id, ':user_id' => $_SESSION['user_id']]);
$incidencia = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$incidencia) {
    die('Incidencia no encontrada o acceso denegado.');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo = trim($_POST['titulo'] ?? '');
    $descripcion = trim($_POST['descripcion'] ?? '');
    $localizacion = trim($_POST['localizacion'] ?? '');
    $estado = trim($_POST['estado'] ?? '');

    if ($titulo && $descripcion && $localizacion && $estado) {
        $update = $db->prepare("UPDATE incidencias SET titulo = :titulo, descripcion = :descripcion, localizacion = :localizacion, estado = :estado WHERE id = :id AND user_id = :user_id");
        $update->execute([
            ':titulo' => $titulo,
            ':descripcion' => $descripcion,
            ':localizacion' => $localizacion,
            ':estado' => $estado,
            ':id' => $incidencia_id,
            ':user_id' => $_SESSION['user_id']
        ]);
        $message = "Incidencia actualizada correctamente.";

        header('Location: dashboard.php');

        // Recargar datos para mostrar formulario actualizado
        $stmt->execute([':id' => $incidencia_id, ':user_id' => $_SESSION['user_id']]);
        $incidencia = $stmt->fetch(PDO::FETCH_ASSOC);
    } else {
        $message = "Por favor, rellena todos los campos.";
    }
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
    <form method="post">
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
        </label><br><br>

        <button type="submit">Actualizar</button>
    </form>

    <p><?= htmlspecialchars($message) ?></p>
    <a href="dashboard.php">Volver al panel</a>
</body>

</html>