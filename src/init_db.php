<?php
//crear database
//http://localhost:8000/index.php?init_db

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
        fecha_modificacion DATETIME,
        FOREIGN KEY (user_id) REFERENCES users(id)
    )");

    $db->exec("CREATE TABLE IF NOT EXISTS imagenes (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        incidencia_id INTEGER NOT NULL,
        RUTA TEXT NOT NULL,
        FOREIGN KEY (incidencia_id) REFERENCES incidencias(id) ON DELETE CASCADE
);
");

    echo "Tablas 'users', 'incidencias' e imagenes creadas o ya existentes.";
    echo '<br><a href="index.php">Ir al inicio<a>';

} catch (PDOException $e) {
    echo "Error creando tablas: " . $e->getMessage();
}
