<?php
session_start();
if (!isset($_SESSION['id_usuario'])) {
    header("Location: login.php");
    exit();
}

include_once "../models/LoginModel.php";

$usuarioId = $_SESSION['id_usuario'];
$datosUsuario = LoginModel::mdlObtenerUsuarioPorId($usuarioId);
$rolesUsuario = LoginModel::mdlObtenerRolesUsuario($usuarioId);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil de Usuario</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="css/main.css">
    <!-- Estilos específicos para arreglar el logo -->
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
       
            <div class="dropdown ms-auto">
                <button class="btn dropdown-toggle profile-button" type="button" data-bs-toggle="dropdown">
                    <i class="bi bi-person-circle me-1"></i> Ver perfil
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><a class="dropdown-item active" href="usuarios.php">Ver datos</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item" href="../logout.php">Cerrar sesión</a></li>
                </ul>
            </div>
        </div>
    </div>
</nav>

<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Perfil de Usuario</h1>
        <a href="dashboard.php" class="btn btn-secondary">Volver al Panel</a>
    </div>

    <?php if (isset($_GET['success'])) : ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= htmlspecialchars($_GET['success']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <?php if (isset($_GET['error'])) : ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?= htmlspecialchars($_GET['error']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="row">
        <div class="col-md-8 mx-auto">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0"><i class="bi bi-person-circle me-2"></i>Datos personales</h4>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="fw-bold">Tipo de documento:</label>
                        <p><?= htmlspecialchars($datosUsuario['tipo_documento']) ?></p>
                    </div>
                    <div class="mb-3">
                        <label class="fw-bold">Número de documento:</label>
                        <p><?= htmlspecialchars($datosUsuario['documento']) ?></p>
                    </div>
                    <div class="mb-3">
                        <label class="fw-bold">Nombre:</label>
                        <p><?= htmlspecialchars($datosUsuario['nombre']) ?></p>
                    </div>
                    <div class="mb-3">
                        <label class="fw-bold">Apellido:</label>
                        <p><?= htmlspecialchars($datosUsuario['apellido']) ?></p>
                    </div>
                    <div class="mb-3">
                        <label class="fw-bold">Fecha de nacimiento:</label>
                        <p><?= htmlspecialchars($datosUsuario['fecha_nacimiento']) ?></p>
                    </div>
                    <div class="mb-3">
                        <label class="fw-bold">Género:</label>
                        <p><?= htmlspecialchars($datosUsuario['genero']) ?></p>
                    </div>
                    <div class="mb-3">
                        <label class="fw-bold">Email:</label>
                        <p><?= htmlspecialchars($datosUsuario['email']) ?></p>
                    </div>
                    <div class="mb-3">
                        <label class="fw-bold">Roles:</label>
                        <ul>
                            <?php foreach ($rolesUsuario as $rol): ?>
                                <li><?= htmlspecialchars($rol['nombre']) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
                <div class="card-footer">
                    <button class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#modalEditarUsuario">
                        <i class="bi bi-pencil-square me-1"></i> Editar perfil
                    </button>
                    <button class="btn btn-danger ms-2" data-bs-toggle="modal" data-bs-target="#modalCambiarPassword">
                        <i class="bi bi-key me-1"></i> Cambiar contraseña
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal para Editar Usuario -->
<div class="modal fade" id="modalEditarUsuario">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-pencil-square me-2"></i>Editar perfil</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form action="../controllers/LoginController.php?action=actualizar" method="POST">
                    <input type="hidden" name="id_usuario" value="<?= $usuarioId ?>">
                    <div class="mb-3">
                        <label for="tipo_documento" class="form-label">Tipo de documento</label>
                        <select class="form-control" id="tipo_documento" name="tipo_documento" required>
                            <option value="Cédula" <?= $datosUsuario['tipo_documento'] == 'Cédula' ? 'selected' : '' ?>>Cédula</option>
                            <option value="Tarjeta de Identidad" <?= $datosUsuario['tipo_documento'] == 'Tarjeta de Identidad' ? 'selected' : '' ?>>Tarjeta de Identidad</option>
                            <option value="Pasaporte" <?= $datosUsuario['tipo_documento'] == 'Pasaporte' ? 'selected' : '' ?>>Pasaporte</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="documento" class="form-label">Número de documento</label>
                        <input type="text" class="form-control" id="documento" name="documento" value="<?= htmlspecialchars($datosUsuario['documento']) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="nombre" class="form-label">Nombre</label>
                        <input type="text" class="form-control" id="nombre" name="nombre" value="<?= htmlspecialchars($datosUsuario['nombre']) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="apellido" class="form-label">Apellido</label>
                        <input type="text" class="form-control" id="apellido" name="apellido" value="<?= htmlspecialchars($datosUsuario['apellido']) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="fecha_nacimiento" class="form-label">Fecha de nacimiento</label>
                        <input type="date" class="form-control" id="fecha_nacimiento" name="fecha_nacimiento" value="<?= htmlspecialchars($datosUsuario['fecha_nacimiento']) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="genero" class="form-label">Género</label>
                        <select class="form-control" id="genero" name="genero" required>
                            <option value="Masculino" <?= $datosUsuario['genero'] == 'Masculino' ? 'selected' : '' ?>>Masculino</option>
                            <option value="Femenino" <?= $datosUsuario['genero'] == 'Femenino' ? 'selected' : '' ?>>Femenino</option>
                            <option value="Otro" <?= $datosUsuario['genero'] == 'Otro' ? 'selected' : '' ?>>Otro</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" value="<?= htmlspecialchars($datosUsuario['email']) ?>" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Guardar cambios</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal para Cambiar Contraseña -->
<div class="modal fade" id="modalCambiarPassword">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-key me-2"></i>Cambiar contraseña</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form action="../controllers/LoginController.php?action=cambiarPassword" method="POST">
                    <input type="hidden" name="id_usuario" value="<?= $usuarioId ?>">
                    <div class="mb-3">
                        <label for="password_actual" class="form-label">Contraseña actual</label>
                        <input type="password" class="form-control" id="password_actual" name="password_actual" required>
                    </div>
                    <div class="mb-3">
                        <label for="password_nueva" class="form-label">Nueva contraseña</label>
                        <input type="password" class="form-control" id="password_nueva" name="password_nueva" required>
                    </div>
                    <div class="mb-3">
                        <label for="password_confirmacion" class="form-label">Confirmar nueva contraseña</label>
                        <input type="password" class="form-control" id="password_confirmacion" name="password_confirmacion" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Cambiar contraseña</button>
                </form>
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
                <p class="mb-0">Desarrollado por <strong>Tu Empresa</strong> &copy; 2025</p>
            </div>
        </div>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>