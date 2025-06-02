<?php
require_once __DIR__ . '/../src/db.php';

$stmt = $db->query("SELECT id, username FROM users");
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Usuarios registrados</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <h1>Usuarios registrados</h1>
    <ul>
        <?php foreach ($users as $user): ?>
            <li>ID: <?= $user['id'] ?> - Usuario: <?= htmlspecialchars($user['username']) ?></li>
        <?php endforeach; ?>
    </ul>
</body>
</html>
