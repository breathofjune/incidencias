<?php
// crear database
// http://localhost:8000/index.php?init_db

if (isset($_GET['init_db'])) {
    require_once __DIR__ . '/../src/init_db.php';
    exit;
}


session_start();

if (isset($_SESSION['user_id'])) {
    // Usuario con sesión activa
    header("Location: dashboard.php");
} else {
    // Usuario no logueado
    header("Location: login.php");
}
exit;
