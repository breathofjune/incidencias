<?php
require_once __DIR__ . '/../src/db.php';

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username && $password) {
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
        $message = "Por favor, rellena todos los campos.";
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

            <label for="password">Contraseña:</label>
            <input type="password" name="password" required><br><br>

            <button type="submit">Registrarse</button>
        </form>
        <p><?= htmlspecialchars($message) ?></p>
        <a href="/login.php">¿Ya tienes cuenta? Inicia sesión</a>
    </div>
</body>

</html>