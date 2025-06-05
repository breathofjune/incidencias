<?php
session_start();
require_once __DIR__ . '/../src/db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$errores = [];
$exito = false;

// Cargar datos usuario
$stmt = $db->prepare("SELECT username, password FROM users WHERE id = :id");
$stmt->execute([':id' => $_SESSION['user_id']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

$username_actual = $user['username'];

// Procesar formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nuevo_username = trim($_POST['username']);
    $pwd_actual = $_POST['pwd_actual'] ?? '';
    $pwd_nueva = $_POST['pwd_nueva'] ?? '';
    $pwd_repetir = $_POST['pwd_repetir'] ?? '';

    // Validación del nombre de usuario
    if ($nuevo_username === '') {
        $errores[] = 'El nombre de usuario no puede estar vacío.';
    } elseif (!preg_match('/^[A-Za-z][A-Za-z0-9_]{4,14}$/', $nuevo_username)) {
        $errores[] = 'El nombre de usuario debe tener entre 5 y 15 caracteres, empezar por una letra y solo usar letras, números o guiones bajos.';
    }

    // Verificar si el nombre de usuario ya existe y es distinto al actual
    if ($nuevo_username !== $username_actual) {
        $stmt = $db->prepare("SELECT id FROM users WHERE username = :username AND id != :id");
        $stmt->execute([
            ':username' => $nuevo_username,
            ':id' => $_SESSION['user_id']
        ]);
        if ($stmt->fetch()) {
            $errores[] = 'Ese nombre de usuario ya está en uso.';
        }
    }

    // Si no hay errores, actualizar nombre de usuario
    if (empty($errores) && $nuevo_username !== $username_actual) {
        $stmt = $db->prepare("UPDATE users SET username = :username WHERE id = :id");
        $stmt->execute([
            ':username' => $nuevo_username,
            ':id' => $_SESSION['user_id']
        ]);
        $username_actual = $nuevo_username;
    }

    // Cambiar contraseña si se ha rellenado el formulario correspondiente
    if ($pwd_actual !== '' || $pwd_nueva !== '' || $pwd_repetir !== '') {
        if (!password_verify($pwd_actual, $user['password'])) {
            $errores[] = 'La contraseña actual no es correcta.';
        } elseif ($pwd_nueva !== $pwd_repetir) {
            $errores[] = 'Las nuevas contraseñas no coinciden.';
        } elseif (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{6,}$/', $pwd_nueva)) {
            $errores[] = 'La nueva contraseña debe tener al menos 6 caracteres, una mayúscula, una minúscula y un número.';
        } else {
            $nueva_hash = password_hash($pwd_nueva, PASSWORD_DEFAULT);
            $stmt = $db->prepare("UPDATE users SET password = :pwd WHERE id = :id");
            $stmt->execute([
                ':pwd' => $nueva_hash,
                ':id' => $_SESSION['user_id']
            ]);
        }
    }

    if (empty($errores)) {
        $exito = true;
    }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Editar perfil</title>
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="css/formularios.css">
</head>

<body>
    <h1>Editar perfil</h1>

    <?php if ($exito): ?>
        <p style="color: green;">Cambios guardados correctamente.</p>
    <?php endif; ?>

    <?php if (!empty($errores)): ?>
        <div style="color: red;">
            <ul>
                <?php foreach ($errores as $error): ?>
                    <li><?= htmlspecialchars($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form action="editar_perfil.php" method="POST" id="formPerfil">
        <label for="username">Nombre de usuario:</label>
        <input type="text" name="username" id="username" value="<?= htmlspecialchars($username_actual) ?>" required>
        <small>Debe tener entre 5 y 15 caracteres, empezar por letra y usar solo letras, números o guiones bajos</small>

        <h3>Cambiar contraseña</h3>
        <label for="pwd_actual">Contraseña actual:</label>
        <input type="password" name="pwd_actual" id="pwd_actual">

        <label for="pwd_nueva">Contraseña nueva:</label>
        <input type="password" name="pwd_nueva" id="pwd_nueva">
        <small>Mínimo 6 caracteres, y al menos una mayúscula, una minúscula y un número</small>

        <label for="pwd_repetir">Repetir contraseña nueva:</label>
        <input type="password" name="pwd_repetir" id="pwd_repetir">

        <button type="submit">Guardar cambios</button>
        <button type="button" onclick="window.location.href='dashboard.php';">Volver al panel</button>
    </form>

    <div id="erroresJS" style="color: red;"></div>

    <script src="js/editar_perfil.js"></script>
</body>

</html>