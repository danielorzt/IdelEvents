<?php session_start();
if (!isset($_SESSION['id_usuario'])) {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ideal Event's</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="css/main.css">
    <!-- Estilos específicos para arreglar el logo justo aquí -->
    <style>
        .navbar-logo {
            height: 40px !important;
            width: auto !important;
            object-fit: contain !important;
        }
    </style>
</head>
<body>
<nav class="navbar navbar-expand-md custom-navbar">
    <div class="container-fluid">
        <a class="navbar-brand" href="dashboard.php">
            <!-- Verificando la ruta de la imagen -->
            <img src="../public/logo/logo.png" alt="Logo" class="navbar-logo">
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link" href="evento.php">Eventos</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="organizador.php">Organizador</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="productos.php">Clientes</a>
                </li>
            </ul>    
            <div class="dropdown ms-auto">
                <button class="btn dropdown-toggle profile-button" type="button" data-bs-toggle="dropdown">
                    <i class="bi bi-person-circle me-1"></i> Ver perfil
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><a class="dropdown-item" href="usuarios.php">Ver datos</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item" href="../logout.php">Cerrar sesión</a></li>
                </ul>
            </div>
        </div>
    </div>
</nav>

<div class="container mt-5">
    <h1 class="text-center">Bienvenido a Ideal Event's</h1>
    <p class="text-center">Esta plataforma te permite crear y gestionar eventos de manera facil y eficiente.</p>

    <!-- Carrusel de Videos -->
    <div id="videoCarousel" class="carousel slide mb-5" data-bs-ride="carousel" data-bs-interval="10000">
        <div class="carousel-inner rounded shadow">
            <?php
            $videos = ['video1.mp4', 'video2.mp4', 'video3.mp4', 'video4.mp4'];
            foreach ($videos as $index => $video) {
                $activeClass = $index === 0 ? 'active' : '';
                echo "<div class='carousel-item $activeClass'>";
                echo "<video class='d-block w-100' autoplay loop muted>
                        <source src='../public/video/$video' type='video/mp4'>
                      </video>";
                echo "</div>";
            }
            ?>
        </div>
        <button class="carousel-control-prev" type="button" data-bs-target="#videoCarousel" data-bs-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Anterior</span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#videoCarousel" data-bs-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Siguiente</span>
        </button>
    </div>

    <!-- Sección de Acordeón -->
    <div class="accordion mt-5 shadow" id="infoAccordion">
        <div class="accordion-item">
            <h2 class="accordion-header" id="headingOne">
                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne">
                    <i class="bi bi-truck me-2"></i> Información sobre Eventos
                </button>
            </h2>
            <div id="collapseOne" class="accordion-collapse collapse show" data-bs-parent="#infoAccordion">
                <div class="accordion-body">
                    <p>Aquí puedes gestionar toda la información relacionada con eventos:</p>
                    <ul>
                        <li>Agregar nuevos eventos</li>
                        <li>Actualizar información de eventos</li>
                        <li>Visualizar historial de eventos</li>
                    </ul>
                    <a href="evento.php" class="btn btn-sm btn-primary">Ver Eventos</a>
                </div>
            </div>
        </div>
        /// Información sobre Categorías 
        <div class="accordion-item">
            <h2 class="accordion-header" id="headingTwo">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo">
                    <i class="bi bi-tags me-2"></i> Información sobre Categorías
                </button>
            </h2>
            <div id="collapseTwo" class="accordion-collapse collapse" data-bs-parent="#infoAccordion">
                <div class="accordion-body">
                    <p>Organiza los productos en categorías para una mejor gestión:</p>
                    <ul>
                        <li>Crear nuevas categorías</li>
                        <li>Organizar productos por categoría</li>
                        <li>Generar reportes por categoría</li>
                    </ul>
                    <a href="categorias.php" class="btn btn-sm btn-primary">Ver categorías</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Galería de Imágenes -->
    <h3 class="mt-5 mb-4 text-center">Galería </h3>
    <div class="row mt-3 text-center g-4">
        <?php
        $imagenes = ['event1.png', 'img2.png', 'img3.png', 'img4.png'];
        foreach ($imagenes as $imagen) {
            echo "<div class='col-md-3 col-sm-6 mb-4'>
                    <div class='card hover-shadow'>
                        <img src='../public/img/$imagen' class='card-img-top' alt='Imagen'>
                        <div class='card-body'>
                            <h6 class='card-title'>Producto " . rand(100, 999) . "</h6>
                        </div>
                    </div>
                  </div>";
        }
        ?>
    </div>

    <!-- Botón para Popup -->
    <div class="text-center mt-4 mb-5">
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#infoModal">
            <i class="bi bi-info-circle me-1"></i> Más Información
        </button>
    </div>

    <!-- Modal (Popup) -->
    <div class="modal fade" id="infoModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-info-circle me-2"></i>Información Adicional</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Este es un sistema de administración que te permite promover, gestionar eventos y más.</p>
                    <p>Características del sistema:</p>
                    <ul>
                        <li>Gestión completa de Eventos</li>
                        <li>Control de Eventos</li>
                        <li>Organizadores</li>
                        <li>Estadísticas y reportes</li>
                    </ul>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Entendido</button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Footer inspirado en daisyUI -->
<footer class="footer mt-5">
    <div class="container">
        <div class="row">
            <div class="col-md-6 d-flex align-items-center mb-4 mb-md-0">
                <div class="d-flex align-items-center">
                    <div class="footer-logo bg-secondary d-flex align-items-center justify-content-center">
                        <i class="bi bi-database text-white"></i>
                    </div>
                    <div>
                        <h5 class="mb-0">Admin Panel</h5>
                        <p class="mb-0">Sistema de gestión personalizado</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="subscribe-form">
                    <h6>Recibe actualizaciones y noticias</h6>
                    <div class="input-group mb-2">
                        <input type="email" class="form-control" placeholder="email@sitio.com">
                        <button class="btn btn-dark" type="button">Suscribirse</button>
                    </div>
                    <p class="privacy-text">
                        <i class="bi bi-check-circle-fill text-success"></i>
                        No compartimos tu correo electrónico con nadie
                    </p>
                </div>
            </div>
        </div>

        <hr class="my-4">

        <div class="row">
            <div class="col-md-6">
                <div class="social-icons">
                    <a href="#"><i class="bi bi-github"></i></a>
                    <a href="#"><i class="bi bi-discord"></i></a>
                    <a href="#"><i class="bi bi-npm"></i></a>
                    <a href="#"><i class="bi bi-box-seam"></i></a>
                </div>
            </div>
            <div class="col-md-6 text-md-end">
                <p class="mb-0">Desarrollado por <strong>Nicol Alarcón</strong> &copy; 2025</p>
            </div>
        </div>
    </div>
</footer>

<!-- Eliminar jQuery y Bootstrap 4, mantener solo Bootstrap 5 -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>