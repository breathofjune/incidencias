<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'No autorizado']);
    exit;
}

require_once __DIR__ . '/../../src/db.php';

try {
    $stmt = $db->prepare("SELECT id, titulo, descripcion, localizacion, estado, fecha_creacion, fecha_modificacion FROM incidencias WHERE user_id = :user_id");
    $stmt->execute([':user_id' => $_SESSION['user_id']]);
    $incidencias = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (empty($incidencias)) {
        echo json_encode(['message' => 'No existen incidencias registradas.']);
    } else {
        echo json_encode($incidencias);
    }
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Error en la base de datos']);
}
