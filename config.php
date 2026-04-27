<?php
// Configuración de la base de datos
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'jardin_db');

// Configuración de rutas - RUTA CORRECTA
define('BASE_PATH', dirname(__FILE__));
define('BASE_URL', 'http://localhost/nuevomundo'); // ← CAMBIADO a nuevomundo
define('UPLOAD_PATH', BASE_PATH . '/uploads/');

// Función para obtener URL de imagen
function getImageUrl($imagen) {
    if (empty($imagen)) {
        return 'https://picsum.photos/id/13/400/250';
    }
    return BASE_URL . '/uploads/' . $imagen;
}

// Crear conexión
function getConnection() {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    if ($conn->connect_error) {
        die("Error de conexión: " . $conn->connect_error);
    }
    $conn->set_charset("utf8");
    return $conn;
}

// Iniciar sesión
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function isAdminLogged() {
    return isset($_SESSION['admin_logged']) && $_SESSION['admin_logged'] === true;
}

function requireLogin() {
    if (!isAdminLogged()) {
        header('Location: login.php');
        exit();
    }
}
?>