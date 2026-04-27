<?php
require_once 'config.php';
requireLogin();

$conn = getConnection();
$mensaje = '';
$error = '';

if (!file_exists('uploads/')) {
    mkdir('uploads/', 0777, true);
}

// SUBIR IMAGEN
if (isset($_POST['subir_imagen']) && isset($_FILES['imagen'])) {
    if ($_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
        $archivo = $_FILES['imagen'];
        $extension = strtolower(pathinfo($archivo['name'], PATHINFO_EXTENSION));
        $nuevo_nombre = time() . '_' . rand(1000, 9999) . '.' . $extension;
        $ruta = 'uploads/' . $nuevo_nombre;
        
        if (move_uploaded_file($archivo['tmp_name'], $ruta)) {
            $_SESSION['mensaje'] = "✅ Imagen subida: $nuevo_nombre";
            header('Location: admin.php');
            exit();
        }
    }
}

// ELIMINAR IMAGEN
if (isset($_GET['eliminar_imagen'])) {
    $imagen_eliminar = $_GET['eliminar_imagen'];
    $ruta_imagen = 'uploads/' . $imagen_eliminar;
    if (file_exists($ruta_imagen)) {
        unlink($ruta_imagen);
        $_SESSION['mensaje'] = "✅ Imagen eliminada";
    }
    header('Location: admin.php');
    exit();
}

// ELIMINAR PUBLICACIÓN
if (isset($_GET['eliminar'])) {
    $id = $_GET['eliminar'];
    $result = $conn->query("SELECT imagen FROM novedades WHERE id = $id");
    if ($row = $result->fetch_assoc()) {
        $ruta_imagen = 'uploads/' . $row['imagen'];
        if (file_exists($ruta_imagen)) {
            unlink($ruta_imagen);
        }
    }
    $conn->query("DELETE FROM novedades WHERE id = $id");
    $_SESSION['mensaje'] = "✅ Publicación eliminada";
    header('Location: admin.php');
    exit();
}

// PUBLICAR NOVEDAD
if (isset($_POST['publicar_novedad'])) {
    $stmt = $conn->prepare("INSERT INTO novedades (titulo, preview_texto, texto_completo, imagen, fecha, tipo) VALUES (?, ?, ?, ?, ?, 'novedad')");
    $stmt->bind_param("sssss", $_POST['titulo_novedad'], $_POST['preview_novedad'], $_POST['texto_completo_novedad'], $_POST['imagen_novedad'], $_POST['fecha_novedad']);
    if ($stmt->execute()) {
        $_SESSION['mensaje'] = "✅ Novedad publicada";
    }
    header('Location: admin.php');
    exit();
}

// PUBLICAR ACTIVIDAD
if (isset($_POST['publicar_actividad'])) {
    $stmt = $conn->prepare("INSERT INTO novedades (titulo, preview_texto, texto_completo, imagen, fecha, tipo) VALUES (?, ?, ?, ?, ?, 'actividad')");
    $stmt->bind_param("sssss", $_POST['titulo_actividad'], $_POST['preview_actividad'], $_POST['texto_completo_actividad'], $_POST['imagen_actividad'], $_POST['fecha_actividad']);
    if ($stmt->execute()) {
        $_SESSION['mensaje'] = "✅ Actividad publicada";
    }
    header('Location: admin.php');
    exit();
}

// Mostrar mensajes
if (isset($_SESSION['mensaje'])) {
    $mensaje = $_SESSION['mensaje'];
    unset($_SESSION['mensaje']);
}

// Obtener imágenes
$imagenes = [];
if (is_dir('uploads/')) {
    $archivos = scandir('uploads/');
    foreach ($archivos as $archivo) {
        if ($archivo != '.' && $archivo != '..') {
            $ext = strtolower(pathinfo($archivo, PATHINFO_EXTENSION));
            if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
                $imagenes[] = $archivo;
            }
        }
    }
    rsort($imagenes);
}

$novedades = $conn->query("SELECT * FROM novedades WHERE tipo = 'novedad' ORDER BY created_at DESC");
$actividades = $conn->query("SELECT * FROM novedades WHERE tipo = 'actividad' ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Gimnasio Nuevo Mundo</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }
        body {
            background: linear-gradient(135deg, #f0f7ff 0%, #e8f0fe 100%);
            padding: 20px;
        }
        .container {
            max-width: 1200px;
            margin: auto;
        }
        .panel {
            background: white;
            border-radius: 20px;
            padding: 25px;
            margin-bottom: 25px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.08);
            border: 1px solid rgba(137, 207, 240, 0.3);
        }
        h1 {
            color: #1a3a5c;
            font-size: 1.8rem;
            margin-bottom: 10px;
        }
        h2 {
            color: #1a3a5c;
            border-left: 5px solid #FFD700;
            padding-left: 15px;
            margin-bottom: 20px;
            font-size: 1.4rem;
        }
        h3 {
            color: #1a3a5c;
            margin: 15px 0 10px 0;
        }
        input, textarea, select {
            width: 100%;
            padding: 12px;
            margin: 5px 0 15px 0;
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            font-size: 14px;
            transition: all 0.3s;
        }
        input:focus, textarea:focus, select:focus {
            outline: none;
            border-color: #89CFF0;
            box-shadow: 0 0 0 3px rgba(137, 207, 240, 0.1);
        }
        button {
            background: linear-gradient(135deg, #89CFF0, #5BA3D9);
            color: white;
            border: none;
            padding: 12px 28px;
            border-radius: 12px;
            cursor: pointer;
            font-weight: 600;
            font-size: 14px;
            transition: all 0.3s;
        }
        button:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(137, 207, 240, 0.4);
        }
        .image-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
            gap: 15px;
            margin-top: 15px;
        }
        .image-card {
            background: #f8fafc;
            border-radius: 12px;
            padding: 10px;
            text-align: center;
            cursor: pointer;
            position: relative;
            transition: all 0.3s;
            border: 2px solid transparent;
        }
        .image-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        .image-card img {
            width: 100%;
            height: 120px;
            object-fit: cover;
            border-radius: 10px;
        }
        .image-card.selected {
            border-color: #FFD700;
            background: #fffef5;
        }
        .image-card .btn-eliminar-img {
            background: #e76f51;
            padding: 5px 10px;
            font-size: 11px;
            margin-top: 8px;
            width: 100%;
        }
        .image-card .btn-eliminar-img:hover {
            background: #e55a3a;
            transform: none;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #e2e8f0;
        }
        th {
            background: #f8fafc;
            color: #1a3a5c;
            font-weight: 600;
        }
        .btn-eliminar {
            background: #e76f51;
            padding: 6px 15px;
            font-size: 12px;
        }
        .btn-eliminar:hover {
            background: #e55a3a;
        }
        .mensaje {
            background: #d4edda;
            color: #155724;
            padding: 15px;
            border-radius: 12px;
            margin-bottom: 20px;
            border-left: 4px solid #28a745;
        }
        .error {
            background: #f8d7da;
            color: #721c24;
            padding: 15px;
            border-radius: 12px;
            margin-bottom: 20px;
            border-left: 4px solid #dc3545;
        }
        hr {
            margin: 20px 0;
            border: none;
            height: 2px;
            background: linear-gradient(90deg, #89CFF0, #FFD700, #89CFF0);
        }
        .nav-links {
            margin-bottom: 20px;
        }
        .nav-links a {
            color: #5BA3D9;
            text-decoration: none;
            margin-right: 15px;
            font-weight: 500;
        }
        .nav-links a:hover {
            color: #FFD700;
        }
        @media (max-width: 768px) {
            body {
                padding: 10px;
            }
            .panel {
                padding: 15px;
            }
            .image-grid {
                grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
            }
            table {
                font-size: 12px;
            }
            th, td {
                padding: 8px;
            }
            button {
                padding: 10px 20px;
                font-size: 12px;
            }
        }
    </style>
</head>
<body>
<div class="container">
    <div class="panel">
        <h1>💪 Gimnasio Nuevo Mundo - Administración 🏋️</h1>
        <div class="nav-links">
            <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Cerrar Sesión</a>
            <a href="index.php"><i class="fas fa-home"></i> Ver página principal</a>
        </div>
    </div>

    <?php if ($mensaje): ?>
        <div class="mensaje"><?php echo $mensaje; ?></div>
    <?php endif; ?>

    <!-- Subir imagen -->
    <div class="panel">
        <h2><i class="fas fa-cloud-upload-alt"></i> Subir Imagen</h2>
        <form method="POST" enctype="multipart/form-data">
            <input type="file" name="imagen" accept="image/*" required>
            <button type="submit" name="subir_imagen">Subir Imagen</button>
        </form>
    </div>

    <!-- Galería -->
    <div class="panel">
        <h2><i class="fas fa-images"></i> Galería de Imágenes</h2>
        <?php if (empty($imagenes)): ?>
            <p>No hay imágenes. Sube una usando el formulario de arriba.</p>
        <?php else: ?>
            <div class="image-grid" id="imageGrid">
                <?php foreach ($imagenes as $img): ?>
                    <div class="image-card" onclick="seleccionarImagen('<?php echo $img; ?>', this)">
                        <img src="uploads/<?php echo $img; ?>">
                        <small style="display:block; margin-top:5px;"><?php echo substr($img, 0, 20); ?></small>
                        <button class="btn-eliminar-img" onclick="event.stopPropagation(); eliminarImagen('<?php echo $img; ?>')">
                            <i class="fas fa-trash"></i> Eliminar
                        </button>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <hr>

    <!-- Publicar Novedad -->
    <div class="panel">
        <h2><i class="fas fa-newspaper"></i> Publicar Novedad</h2>
        <form method="POST">
            <label>Título</label>
            <input type="text" name="titulo_novedad" required placeholder="Ej: Nuevo horario de clases">
            
            <label>Texto de vista previa (corto)</label>
            <textarea name="preview_novedad" rows="2" required placeholder="Texto que aparecerá en la tarjeta..."></textarea>
            
            <label>Texto completo</label>
            <textarea name="texto_completo_novedad" rows="5" required placeholder="Contenido completo de la novedad..."></textarea>
            
            <label>Fecha</label>
            <input type="date" name="fecha_novedad" value="<?php echo date('Y-m-d'); ?>" required>
            
            <label>Imagen (haz clic en una imagen de la galería)</label>
            <input type="text" name="imagen_novedad" id="imagen_novedad" readonly required placeholder="Selecciona una imagen">
            
            <button type="submit" name="publicar_novedad">Publicar Novedad</button>
        </form>
    </div>

    <!-- Publicar Actividad -->
    <div class="panel">
        <h2><i class="fas fa-calendar-alt"></i> Publicar Actividad</h2>
        <form method="POST">
            <label>Título</label>
            <input type="text" name="titulo_actividad" required placeholder="Ej: Torneo de pesas">
            
            <label>Texto de vista previa (corto)</label>
            <textarea name="preview_actividad" rows="2" required placeholder="Texto que aparecerá en la tarjeta..."></textarea>
            
            <label>Texto completo</label>
            <textarea name="texto_completo_actividad" rows="5" required placeholder="Descripción detallada de la actividad..."></textarea>
            
            <label>Fecha</label>
            <input type="date" name="fecha_actividad" value="<?php echo date('Y-m-d'); ?>" required>
            
            <label>Imagen (haz clic en una imagen de la galería)</label>
            <input type="text" name="imagen_actividad" id="imagen_actividad" readonly required placeholder="Selecciona una imagen">
            
            <button type="submit" name="publicar_actividad">Publicar Actividad</button>
        </form>
    </div>

    <hr>

    <!-- Lista Novedades -->
    <div class="panel">
        <h2><i class="fas fa-list"></i> Novedades Publicadas</h2>
        <?php if ($novedades->num_rows == 0): ?>
            <p>No hay novedades publicadas.</p>
        <?php else: ?>
            <table>
                <thead>
                    <tr><th>Título</th><th>Imagen</th><th>Fecha</th><th>Acción</th></tr>
                </thead>
                <tbody>
                    <?php while($row = $novedades->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['titulo']); ?></td>
                        <td><img src="uploads/<?php echo $row['imagen']; ?>" width="40" height="40" style="object-fit:cover; border-radius:8px;"></td>
                        <td><?php echo $row['fecha']; ?></td>
                        <td><a href="?eliminar=<?php echo $row['id']; ?>" onclick="return confirm('¿Eliminar esta publicación?')"><button class="btn-eliminar">Eliminar</button></a></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>

    <!-- Lista Actividades -->
    <div class="panel">
        <h2><i class="fas fa-list"></i> Actividades Publicadas</h2>
        <?php if ($actividades->num_rows == 0): ?>
            <p>No hay actividades publicadas.</p>
        <?php else: ?>
            <table>
                <thead>
                    <tr><th>Título</th><th>Imagen</th><th>Fecha</th><th>Acción</th></tr>
                </thead>
                <tbody>
                    <?php while($row = $actividades->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['titulo']); ?></td>
                        <td><img src="uploads/<?php echo $row['imagen']; ?>" width="40" height="40" style="object-fit:cover; border-radius:8px;"></td>
                        <td><?php echo $row['fecha']; ?></td>
                        <td><a href="?eliminar=<?php echo $row['id']; ?>" onclick="return confirm('¿Eliminar esta publicación?')"><button class="btn-eliminar">Eliminar</button></a></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</div>

<script>
function seleccionarImagen(nombre, elemento) {
    document.querySelectorAll('.image-card').forEach(card => {
        card.classList.remove('selected');
    });
    elemento.classList.add('selected');
    document.getElementById('imagen_novedad').value = nombre;
    document.getElementById('imagen_actividad').value = nombre;
}

function eliminarImagen(nombre) {
    if (confirm('¿Eliminar esta imagen permanentemente?')) {
        window.location.href = '?eliminar_imagen=' + encodeURIComponent(nombre);
    }
}
</script>
</body>
</html>

<?php $conn->close(); ?>