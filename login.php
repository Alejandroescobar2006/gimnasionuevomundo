<?php
require_once 'config.php';

// Si ya está logueado, redirigir al admin
if (isset($_SESSION['admin_logged']) && $_SESSION['admin_logged'] === true) {
    header('Location: admin.php');
    exit();
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario = trim($_POST['usuario']);
    $password = $_POST['password'];
    
    if (empty($usuario) || empty($password)) {
        $error = "Por favor complete todos los campos";
    } else {
        $conn = getConnection();
        
        // Buscar usuario en la base de datos
        $sql = "SELECT id, usuario, password FROM usuarios WHERE usuario = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $usuario);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($row = $result->fetch_assoc()) {
            // Verificar contraseña usando password_verify()
            if (password_verify($password, $row['password'])) {
                $_SESSION['admin_logged'] = true;
                $_SESSION['usuario_id'] = $row['id'];
                $_SESSION['usuario'] = $row['usuario'];
                header('Location: admin.php');
                exit();
            } else {
                $error = "Contraseña incorrecta";
            }
        } else {
            $error = "Usuario no encontrado";
        }
        
        $stmt->close();
        $conn->close();
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Jardín Infantil</title>
    <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Quicksand', sans-serif; }
        body { background: linear-gradient(135deg, #f9f3e6 0%, #ffe6d5 100%); min-height: 100vh; display: flex; align-items: center; justify-content: center; }
        .login-container { background: white; padding: 2rem; border-radius: 2rem; box-shadow: 0 10px 30px rgba(0,0,0,0.1); width: 100%; max-width: 400px; }
        h1 { color: #2d6a4f; margin-bottom: 1.5rem; text-align: center; }
        .form-group { margin-bottom: 1rem; }
        label { display: block; margin-bottom: 0.5rem; font-weight: 600; color: #2d6a4f; }
        input { width: 100%; padding: 0.8rem; border: 1px solid #ddd; border-radius: 0.8rem; font-size: 1rem; }
        button { width: 100%; padding: 0.8rem; background: linear-gradient(135deg, #40916c, #2d6a4f); color: white; border: none; border-radius: 0.8rem; font-size: 1rem; font-weight: 600; cursor: pointer; transition: transform 0.2s; }
        button:hover { transform: translateY(-2px); box-shadow: 0 5px 15px rgba(0,0,0,0.2); }
        .error { background: #f8d7da; color: #721c24; padding: 0.8rem; border-radius: 0.8rem; margin-bottom: 1rem; text-align: center; border-left: 4px solid #f5c6cb; }
        .info { text-align: center; margin-top: 1rem; font-size: 0.85rem; color: #6c757d; }
    </style>
</head>
<body>
    <div class="login-container">
        <h1><i class="fas fa-user-shield"></i> Acceso Administrador</h1>
        
        <?php if ($error): ?>
            <div class="error">
                <i class="fas fa-exclamation-triangle"></i> <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>
        
        <form method="POST" action="">
            <div class="form-group">
                <label><i class="fas fa-user"></i> Usuario</label>
                <input type="text" name="usuario" required autofocus>
            </div>
            <div class="form-group">
                <label><i class="fas fa-lock"></i> Contraseña</label>
                <input type="password" name="password" required>
            </div>
            <button type="submit">
                <i class="fas fa-sign-in-alt"></i> Ingresar
            </button>
        </form>
        
        <div class="info">
            <i class="fas fa-info-circle"></i> Credenciales: admin / admin123
        </div>
    </div>
</body>
</html>