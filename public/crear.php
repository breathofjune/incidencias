<?php
// Este script debe ser ejecutado después de que las tablas 'users', 'incidencias' e 'imagenes' ya existan.
// Ir a http://localhost:8000/crear.php

require_once __DIR__ . '/../src/db.php';

try {
    // Insertar el usuario 'Lucas'
    $stmt = $db->prepare("INSERT INTO users (username, password) VALUES (:username, :password)");
    $stmt->execute([
        ':username' => 'lucas',
        ':password' => password_hash('Azerty1', PASSWORD_DEFAULT)
    ]);
    $userId = $db->lastInsertId();

    // Insertar 25 incidencias de ejemplo
    for ($i = 1; $i <= 25; $i++) {
        $titulo = "Incidencia número $i";
        $descripcion = "Descripción detallada de la incidencia $i. Este es un ejemplo.";
        $localizacion = "Ubicación de la incidencia $i";
        $estado = "abierta";

        // Insertar incidencia
        $stmt = $db->prepare("
            INSERT INTO incidencias (user_id, titulo, descripcion, localizacion, estado, creado_por) 
            VALUES (:user_id, :titulo, :descripcion, :localizacion, :estado, :creado_por)
        ");
        $stmt->execute([
            ':user_id' => $userId,
            ':titulo' => $titulo,
            ':descripcion' => $descripcion,
            ':localizacion' => $localizacion,
            ':estado' => $estado,
            ':creado_por' => 'lucas'
        ]);
    }

    echo "¡25 incidencias creadas exitosamente!";
    echo '<br><a href="index.php">Ir al inicio</a>';

} catch (PDOException $e) {
    echo "Error insertando incidencias: " . $e->getMessage();
}
?>
