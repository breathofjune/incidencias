<?php
require_once __DIR__ . '/../src/db.php';

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username && $password) {
        $stmt = $db->prepare("SELECT * FROM users WHERE username = :username");
        $stmt->execute([':username' => $username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            session_start();
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            header("Location: dashboard.php");
            exit;
        } else {
            $message = "Usuario o contraseña incorrectos.";
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
    <title>Iniciar sesión</title>
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="css/auth.css">
</head>

<body>
    <div class="auth-container">
        <h1>Iniciar sesión</h1>
        <form method="post">
            <label for="username">Usuario:</label>
            <input type="text" name="username" required><br><br>

            <label for="password">Contraseña:</label>
            <input type="password" name="password" required><br><br>

            <button type="submit">Entrar</button>
        </form>
        <p><?= htmlspecialchars($message) ?></p>
        <a href="/register.php">¿No tienes cuenta? Regístrate</a>
    </div>
</body>

</html>