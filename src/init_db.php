<?php
require_once __DIR__ . '/db.php';

try {
    $db->exec("CREATE TABLE IF NOT EXISTS users (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        username TEXT NOT NULL UNIQUE,
        password TEXT NOT NULL
    )");

    $db->exec("CREATE TABLE IF NOT EXISTS incidencias (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        user_id INTEGER NOT NULL,
        titulo TEXT NOT NULL,
        descripcion TEXT NOT NULL,
        localizacion TEXT,
        estado TEXT NOT NULL DEFAULT 'abierta',
        fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id)
    )");

    echo "Tablas 'users' e 'incidencias' creadas o ya existentes.";

} catch (PDOException $e) {
    echo "Error creando tablas: " . $e->getMessage();
}
