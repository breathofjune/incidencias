<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'No autorizado']);
    exit;
}

require_once __DIR__ . '/../../src/db.php';

$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$limit = isset($_GET['limit']) ? max(1, intval($_GET['limit'])) : 10;
$offset = ($page - 1) * $limit;

try {
    // Obtener total de incidencias para el usuario
    $stmtTotal = $db->prepare("SELECT COUNT(*) FROM incidencias WHERE user_id = :user_id");
    $stmtTotal->execute([':user_id' => $_SESSION['user_id']]);
    $total = $stmtTotal->fetchColumn();

    // Obtener incidencias paginadas
    $stmt = $db->prepare("SELECT id, titulo, descripcion, localizacion, estado, fecha_creacion, fecha_modificacion 
                        FROM incidencias 
                        WHERE user_id = :user_id 
                        ORDER BY 
                            CASE 
                                WHEN LOWER(estado) = 'abierta' THEN 1
                                WHEN LOWER(estado) = 'en proceso' THEN 2
                                WHEN LOWER(estado) = 'cerrada' THEN 3
                                ELSE 4
                            END,
                            COALESCE(fecha_modificacion, fecha_creacion) DESC,
                            fecha_creacion DESC
                        LIMIT :limit OFFSET :offset");

    $stmt->bindValue(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();

    $incidencias = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'total' => intval($total),
        'page' => $page,
        'per_page' => $limit,
        'incidencias' => $incidencias
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Error en la base de datos: ' . $e->getMessage()]);
}
