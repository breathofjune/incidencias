<?php
require_once __DIR__ . '/../src/db.php';

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $password_repeat = $_POST['password_repeat'] ?? '';

    $errores = [];

    if (empty($username) || empty($password)) {
        $errores[] = "Por favor, rellena todos los campos.";
    }

    if (!preg_match('/^[A-Za-z][A-Za-z0-9_]{4,14}$/', $username)) {
        $errores[] = "El nombre de usuario debe tener entre 5 y 15 caracteres, empezar por una letra y solo usar letras, números o guiones bajos.";
    }

    if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{6,}$/', $password)) {
        $errores[] = "La contraseña debe tener al menos 6 caracteres, una mayúscula, una minúscula y un número.";
    }

    if ($password !== $password_repeat) {
        $errores[] = "Las contraseñas no coinciden.";
    }

    if (empty($errores)) {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        try {
            $stmt = $db->prepare("INSERT INTO users (username, password) VALUES (:username, :password)");
            $stmt->execute([
                ':username' => $username,
                ':password' => $hashedPassword
            ]);
            $message = "Usuario registrado con éxito.";
        } catch (PDOException $e) {
            if ($e->getCode() === '23000') {
                $message = "El usuario ya está registrado.";
            } else {
                $message = "Error: " . $e->getMessage();
            }
        }
    } else {
        $message = implode('<br>', $errores);
    }
}
?>


<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Registro</title>
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="css/auth.css">
</head>

<body>
    <div class="auth-container">
        <h1>Registro de usuario</h1>
        <form method="post">
            <label for="username">Usuario:</label>
            <input type="text" name="username" required><br><br>
            <small>Debe tener entre 5 y 15 caracteres, empezar por letra y usar solo letras, números o guiones
                bajos</small>

            <label for="password">Contraseña:</label>
            <input type="password" name="password" required><br><br>
            <small>Mínimo 6 caracteres, y al menos una mayúscula, una minúscula y un número</small>

            <label for="password_repeat">Repetir contraseña:</label>
            <input type="password" name="password_repeat" id="password_repeat" required><br><br>

            <button type="submit">Registrarse</button>
        </form>
        <p><?= htmlspecialchars($message) ?></p>
        <a href="/login.php">¿Ya tienes cuenta? Inicia sesión</a>
    </div>

    <script src="js/registro.js"></script>
</body>

</html>