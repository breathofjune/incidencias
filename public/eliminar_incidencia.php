<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

require_once __DIR__ . '/../src/db.php';

$id = $_GET['id'] ?? null;

if ($id) {
    // Comprobamos que la incidencia pertenece al usuario logueado
    $stmt = $db->prepare("SELECT * FROM incidencias WHERE id = :id AND user_id = :user_id");
    $stmt->execute([
        ':id' => $id,
        ':user_id' => $_SESSION['user_id']
    ]);
    $incidencia = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($incidencia) {
        // Eliminar
        $stmt = $db->prepare("DELETE FROM incidencias WHERE id = :id");
        $stmt->execute([':id' => $id]);
    }
}

// Redirige de vuelta al panel
header('Location: dashboard.php');
exit;
