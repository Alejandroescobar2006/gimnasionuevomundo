<?php
require_once 'config.php';

$conn = getConnection();

// Obtener SOLO novedades
$sql_novedades = "SELECT * FROM novedades WHERE tipo = 'novedad' ORDER BY created_at DESC";
$result_novedades = $conn->query($sql_novedades);

// Obtener SOLO actividades
$sql_actividades = "SELECT * FROM novedades WHERE tipo = 'actividad' ORDER BY created_at DESC";
$result_actividades = $conn->query($sql_actividades);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes">
    <title>Jardín Infantil Nuevo Mundo - Actividades y Novedades</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
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
            min-height: 100vh;
        }

        /* Scroll personalizado */
        ::-webkit-scrollbar {
            width: 10px;
        }
        ::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }
        ::-webkit-scrollbar-thumb {
            background: #89CFF0;
            border-radius: 10px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #5BA3D9;
        }

        .container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 1rem;
        }

        /* Header renovado */
        .main-header {
            background: linear-gradient(135deg, #FFD700 0%, #FFED4E 50%, #FFD700 100%);
            border-radius: 2rem;
            padding: 2rem;
            margin-bottom: 2rem;
            color: #1a3a5c;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            position: relative;
            overflow: hidden;
        }
        .main-header::before {
            content: "🌸🌿🍎";
            position: absolute;
            bottom: -20px;
            right: -20px;
            font-size: 120px;
            opacity: 0.15;
            pointer-events: none;
        }

        /* Estilos para el logo en el header */
        .main-header {
            position: relative;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .main-header .img-logo {
            flex-shrink: 0;
        }

        .main-header .nuevo-logo {
            max-width: 100px;
            height: 100px;
            border-radius: 50%;
            object-fit: cover;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
        }

        .main-header .nuevo-logo:hover {
            transform: scale(1.05);
        }

        .main-header .texto-header {
            text-align: center;
            flex: 1;
        }

        .main-header h1 {
            font-size: 2rem;
            font-weight: 800;
            margin-bottom: 0.5rem;
            letter-spacing: -0.5px;
        }
        .main-header p {
            font-size: 1rem;
            opacity: 0.9;
            font-weight: 500;
        }

        /* Secciones */
        .novedades-section, .actividades-section {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(2px);
            border-radius: 2rem;
            padding: 2rem;
            margin-bottom: 3rem;
            box-shadow: 0 10px 30px rgba(0,0,0,0.08);
            border: 1px solid rgba(137, 207, 240, 0.3);
        }
        .section-title {
            font-size: 2rem;
            font-weight: 700;
            color: #1a3a5c;
            margin-bottom: 1.5rem;
            border-left: 6px solid #FFD700;
            padding-left: 1rem;
            display: flex;
            align-items: center;
            gap: 0.8rem;
        }
        .section-title i {
            color: #89CFF0;
            font-size: 1.8rem;
        }

        /* Swiper mejorado */
        .swiper {
            width: 100%;
            padding: 1rem 0.5rem 2rem;
        }
        .swiper-slide {
            background: white;
            border-radius: 1.5rem;
            overflow: hidden;
            cursor: pointer;
            position: relative;
            transition: all 0.35s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 8px 20px rgba(0,0,0,0.1);
            border: 1px solid rgba(137, 207, 240, 0.2);
        }
        .swiper-slide:hover {
            transform: translateY(-8px);
            box-shadow: 0 15px 35px rgba(0,0,0,0.15);
        }
        .slide-img {
            width: 100%;
            height: 240px;
            object-fit: cover;
            background: #e8f0fe;
        }
        .slide-content {
            padding: 1.2rem;
        }
        .slide-title {
            font-size: 1.2rem;
            font-weight: 700;
            color: #1a3a5c;
            margin-bottom: 0.3rem;
        }
        .slide-date {
            font-size: 0.7rem;
            color: #89CFF0;
            margin: 0.3rem 0;
            display: flex;
            align-items: center;
            gap: 0.3rem;
            font-weight: 600;
        }
        .slide-preview {
            color: #4a5568;
            font-size: 0.85rem;
            line-height: 1.45;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        .new-badge {
            position: absolute;
            top: 12px;
            right: 12px;
            background: #FFD700;
            color: #1a3a5c;
            font-size: 0.7rem;
            font-weight: 700;
            padding: 0.3rem 0.9rem;
            border-radius: 30px;
            z-index: 10;
            display: flex;
            align-items: center;
            gap: 0.3rem;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }

        /* Modal */
        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.85);
            backdrop-filter: blur(8px);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 1000;
            visibility: hidden;
            opacity: 0;
            transition: all 0.3s ease;
        }
        .modal-overlay.active {
            visibility: visible;
            opacity: 1;
        }
        .modal-container {
            background: white;
            max-width: 600px;
            width: 90%;
            border-radius: 2rem;
            max-height: 85vh;
            overflow-y: auto;
            animation: modalPop 0.35s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 25px 50px rgba(0,0,0,0.3);
        }
        @keyframes modalPop {
            from {
                transform: scale(0.9);
                opacity: 0;
            }
            to {
                transform: scale(1);
                opacity: 1;
            }
        }
        .modal-portada {
            width: 100%;
            max-height: 250px;
            object-fit: cover;
            border-radius: 2rem 2rem 0 0;
        }
        .modal-body {
            padding: 1.5rem;
        }
        .modal-title {
            font-size: 1.6rem;
            font-weight: 700;
            color: #1a3a5c;
        }
        .modal-date {
            color: #89CFF0;
            margin: 0.5rem 0 1rem 0;
            font-size: 0.85rem;
            display: flex;
            align-items: center;
            gap: 0.4rem;
            font-weight: 600;
        }
        .modal-texto-completo {
            color: #4a5568;
            line-height: 1.7;
            font-size: 0.95rem;
            white-space: pre-wrap;
        }
        .close-modal {
            background: linear-gradient(135deg, #89CFF0, #5BA3D9);
            color: white;
            border: none;
            padding: 0.8rem;
            border-radius: 3rem;
            margin-top: 1.5rem;
            width: 100%;
            cursor: pointer;
            font-weight: 600;
            font-size: 1rem;
            transition: all 0.3s;
        }
        .close-modal:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(137, 207, 240, 0.4);
        }

        /* Info secciones */
        .info-seccion {
            margin-bottom: 2.5rem;
            padding: 1.8rem;
            background: white;
            border-radius: 1.5rem;
            border: 1px solid rgba(137, 207, 240, 0.3);
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
        }
        .info-seccion h2 {
            font-size: 1.7rem;
            font-weight: 700;
            color: #1a3a5c;
            border-left: 6px solid #FFD700;
            padding-left: 1rem;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.8rem;
        }
        .info-seccion h2 i {
            color: #89CFF0;
        }
        .info-seccion p {
            font-size: 0.98rem;
            line-height: 1.65;
            color: #4a5568;
        }

        /* Tabla de horarios bonita */
        .horarios-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1rem;
            background: #f8fafc;
            border-radius: 1rem;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
        .horarios-table th {
            background: linear-gradient(135deg, #FFD700, #FFED4E);
            color: #1a3a5c;
            padding: 1rem;
            font-weight: 700;
            text-align: center;
            font-size: 1rem;
        }
        .horarios-table td {
            padding: 0.8rem;
            text-align: center;
            border-bottom: 1px solid #e2e8f0;
            color: #4a5568;
        }
        .horarios-table tr:last-child td {
            border-bottom: none;
        }
        .horarios-table tr:hover {
            background: #fef9e6;
        }
        .dia-semana {
            font-weight: 700;
            color: #1a3a5c;
            background: #f1f5f9;
        }

        /* Botón admin */
        .admin-link {
            position: fixed;
            bottom: 20px;
            right: 20px;
            background: #FFD700;
            backdrop-filter: blur(5px);
            padding: 12px 20px;
            border-radius: 60px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            z-index: 99;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            text-decoration: none;
            color: #1a3a5c;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            transition: all 0.3s;
        }
        .admin-link:hover {
            transform: scale(1.05);
            box-shadow: 0 8px 20px rgba(0,0,0,0.15);
        }

        /* Navegación swiper personalizada */
        .swiper-button-prev, .swiper-button-next {
            background: white;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .swiper-button-prev::after, .swiper-button-next::after {
            font-size: 18px;
            color: #89CFF0;
            font-weight: bold;
        }
        .swiper-pagination-bullet {
            background: #89CFF0;
        }
        .swiper-pagination-bullet-active {
            background: #FFD700;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .container {
                padding: 0.8rem;
            }
            .main-header {
                padding: 1rem;
                flex-direction: column;
                text-align: center;
            }
            .main-header .img-logo {
                margin-bottom: 0.5rem;
            }
            .main-header .nuevo-logo {
                max-width: 70px;
                height: 70px;
            }
            .main-header h1 {
                font-size: 1.3rem;
            }
            .main-header p {
                font-size: 0.75rem;
            }
            .main-header::before {
                font-size: 60px;
            }
            .section-title {
                font-size: 1.3rem;
            }
            .section-title i {
                font-size: 1.2rem;
            }
            .novedades-section, .actividades-section {
                padding: 1rem;
            }
            .slide-img {
                height: 180px;
            }
            .info-seccion {
                padding: 1rem;
            }
            .info-seccion h2 {
                font-size: 1.2rem;
            }
            .modal-title {
                font-size: 1.3rem;
            }
            .admin-link {
                bottom: 15px;
                right: 15px;
                padding: 8px 15px;
                font-size: 11px;
            }
            .swiper-button-prev, .swiper-button-next {
                display: none;
            }
            .horarios-table th, .horarios-table td {
                padding: 0.5rem;
                font-size: 0.8rem;
            }
        }

        @media (max-width: 480px) {
            .slide-img {
                height: 150px;
            }
            .slide-title {
                font-size: 1rem;
            }
            .slide-preview {
                font-size: 0.75rem;
            }
            .modal-portada {
                max-height: 180px;
            }
            .modal-body {
                padding: 1rem;
            }
            .horarios-table th, .horarios-table td {
                padding: 0.4rem;
                font-size: 0.7rem;
            }
        }
    </style>
</head>
<body>

<div class="container">
    <header class="main-header">
        <div class="img-logo">
            <img src="/images/logonuevo.png" class="nuevo-logo" alt="Logo Jardín Infantil Nuevo Mundo">
        </div>
        <div class="texto-header">
            <h1>🌸 Jardín Infantil Nuevo Mundo 🍎</h1>
            <p>Donde los pequeños gigantes crecen felices</p>
        </div>
    </header>

    <!-- SECCIÓN DE NOVEDADES -->
    <div class="novedades-section">
        <div class="section-title">
            <i class="fas fa-newspaper"></i>
            Novedades
        </div>
        <div class="swiper novedadesSwiper">
            <div class="swiper-wrapper">
                <?php if ($result_novedades && $result_novedades->num_rows > 0): ?>
                    <?php while($post = $result_novedades->fetch_assoc()): ?>
                        <?php $esNuevo = (time() - strtotime($post['created_at'])) <= (10 * 86400); ?>
                        <div class="swiper-slide" onclick='abrirModal(<?php echo json_encode($post); ?>)'>
                            <?php if ($esNuevo): ?>
                                <div class="new-badge"><i class="fas fa-star"></i> NUEVO</div>
                            <?php endif; ?>
                            <img class="slide-img" src="uploads/<?php echo $post['imagen']; ?>" onerror="this.src='https://picsum.photos/id/13/400/250'">
                            <div class="slide-content">
                                <div class="slide-title"><?php echo htmlspecialchars($post['titulo']); ?></div>
                                <div class="slide-date"><i class="far fa-calendar-alt"></i> <?php echo date('d/m/Y', strtotime($post['fecha'])); ?></div>
                                <div class="slide-preview"><?php echo htmlspecialchars(substr($post['preview_texto'], 0, 80)) . (strlen($post['preview_texto']) > 80 ? '…' : ''); ?></div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div class="swiper-slide loading" style="text-align: center; padding: 2rem;">
                        ✨ No hay novedades aún ✨
                    </div>
                <?php endif; ?>
            </div>
            <div class="swiper-button-prev"></div>
            <div class="swiper-button-next"></div>
            <div class="swiper-pagination"></div>
        </div>
    </div>

    <!-- SECCIÓN DE ACTIVIDADES -->
    <div class="actividades-section">
        <div class="section-title">
            <i class="fas fa-calendar-alt"></i>
            Próximas Actividades
        </div>
        <div class="swiper actividadesSwiper">
            <div class="swiper-wrapper">
                <?php if ($result_actividades && $result_actividades->num_rows > 0): ?>
                    <?php while($post = $result_actividades->fetch_assoc()): ?>
                        <?php $esNuevo = (time() - strtotime($post['created_at'])) <= (10 * 86400); ?>
                        <div class="swiper-slide" onclick='abrirModal(<?php echo json_encode($post); ?>)'>
                            <?php if ($esNuevo): ?>
                                <div class="new-badge"><i class="fas fa-star"></i> NUEVO</div>
                            <?php endif; ?>
                            <img class="slide-img" src="uploads/<?php echo $post['imagen']; ?>" onerror="this.src='https://picsum.photos/id/13/400/250'">
                            <div class="slide-content">
                                <div class="slide-title"><?php echo htmlspecialchars($post['titulo']); ?></div>
                                <div class="slide-date"><i class="far fa-calendar-alt"></i> <?php echo date('d/m/Y', strtotime($post['fecha'])); ?></div>
                                <div class="slide-preview"><?php echo htmlspecialchars(substr($post['preview_texto'], 0, 80)) . (strlen($post['preview_texto']) > 80 ? '…' : ''); ?></div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div class="swiper-slide loading" style="text-align: center; padding: 2rem;">
                        ✨ No hay actividades programadas ✨
                    </div>
                <?php endif; ?>
            </div>
            <div class="swiper-button-prev"></div>
            <div class="swiper-button-next"></div>
            <div class="swiper-pagination"></div>
        </div>
    </div>

    <!-- Información del jardín -->
    <div class="info-seccion">
        <h2><i class="fas fa-building"></i> Nuestras Instalaciones</h2>
        <p>Nuestro jardín infantil cuenta con instalaciones modernas, seguras y especialmente diseñadas para el desarrollo integral de los niños. Contamos con salones luminosos, áreas verdes, biblioteca infantil, comedor saludable, zonas de juego y un equipo de profesionales altamente calificados.</p>
        <br>
        <p>🏫 <strong>Dirección:</strong> Calle 123 #45-67, Barrio Los Pinos</p>
        <p>📞 <strong>Teléfono:</strong> (601) 123-4567</p>
        <p>📧 <strong>Email:</strong> contacto@jardinnuevomundo.com</p>
    </div>

    <div class="info-seccion">
        <h2><i class="fas fa-chalkboard-user"></i> Nuestro Equipo</h2>
        <p>Contamos con profesionales apasionados por la educación infantil, con amplia experiencia y formación continua en pedagogía infantil, psicología y desarrollo temprano. Cada uno de nuestros docentes está comprometido con el bienestar y crecimiento de los niños.</p>
    </div>

    <div class="info-seccion">
        <h2><i class="fas fa-clock"></i> Horario de Atención</h2>
        <table class="horarios-table">
            <thead>
                <tr>
                    <th>Día</th>
                    <th>Hora de Entrada</th>
                    <th>Hora de Salida</th>
                </tr>
            </thead>
            <tbody>
                <tr><td class="dia-semana">Lunes</td><td>7:00 AM</td><td>4:00 PM</td></tr>
                <tr><td class="dia-semana">Martes</td><td>7:00 AM</td><td>4:00 PM</td></tr>
                <tr><td class="dia-semana">Miércoles</td><td>7:00 AM</td><td>4:00 PM</td></tr>
                <tr><td class="dia-semana">Jueves</td><td>7:00 AM</td><td>4:00 PM</td></tr>
                <tr><td class="dia-semana">Viernes</td><td>7:00 AM</td><td>4:00 PM</td></tr>
            </tbody>
        </table>
        <p style="margin-top: 1rem; font-size: 0.85rem; color: #89CFF0;">
            <i class="fas fa-info-circle"></i> El horario de atención es de Lunes a Viernes. Sábados y Domingos cerrado.
        </p>
    </div>

    <a href="admin.php" class="admin-link">
        <i class="fas fa-user-shield"></i> Administrador
    </a>
</div>

<div id="modalOverlay" class="modal-overlay">
    <div class="modal-container">
        <img id="modalPortada" class="modal-portada" src="">
        <div class="modal-body">
            <h2 id="modalTitle" class="modal-title"></h2>
            <div id="modalDate" class="modal-date"><i class="far fa-calendar-alt"></i> <span></span></div>
            <div id="modalTextoCompleto" class="modal-texto-completo"></div>
            <button class="close-modal" id="closeModalBtn"><i class="fas fa-times-circle"></i> Cerrar</button>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
<script>
    function abrirModal(post) {
        document.getElementById('modalPortada').src = 'uploads/' + post.imagen;
        document.getElementById('modalTitle').innerText = post.titulo;
        document.getElementById('modalDate').innerHTML = '<i class="far fa-calendar-alt"></i> ' + post.fecha;
        document.getElementById('modalTextoCompleto').innerHTML = post.texto_completo.replace(/\n/g, '<br>');
        document.getElementById('modalOverlay').classList.add('active');
    }

    document.getElementById('closeModalBtn').onclick = () => {
        document.getElementById('modalOverlay').classList.remove('active');
    };
    
    document.getElementById('modalOverlay').onclick = (e) => {
        if (e.target === document.getElementById('modalOverlay')) {
            document.getElementById('modalOverlay').classList.remove('active');
        }
    };

    // Swiper para Novedades
    new Swiper('.novedadesSwiper', {
        slidesPerView: 1,
        spaceBetween: 20,
        pagination: { el: '.swiper-pagination', clickable: true },
        navigation: { nextEl: '.swiper-button-next', prevEl: '.swiper-button-prev' },
        breakpoints: { 
            640: { slidesPerView: 2 },
            1024: { slidesPerView: 3 } 
        }
    });

    // Swiper para Actividades
    new Swiper('.actividadesSwiper', {
        slidesPerView: 1,
        spaceBetween: 20,
        pagination: { el: '.swiper-pagination', clickable: true },
        navigation: { nextEl: '.swiper-button-next', prevEl: '.swiper-button-prev' },
        breakpoints: { 
            640: { slidesPerView: 2 },
            1024: { slidesPerView: 3 } 
        }
    });
</script>
</body>
</html>

<?php $conn->close(); ?>