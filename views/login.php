<?php
session_start();

// Redirección si ya está logueado
if (isset($_SESSION['id_usuario'])) {
    header("Location: dashboard.php");
    exit();
}

// Manejo de mensajes de retroalimentación
$msg = $_GET['msg'] ?? '';
$status = $_GET['status'] ?? '';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Sistema de Eventos - Login</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Estilos personalizados -->
    <link rel="stylesheet" href="css/main.css">
    <!-- SweetAlert2 para mensajes bonitos -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body {
            background-color: #f8f9fa;
        }
        .login-container {
            max-width: 400px;
            margin: 50px auto;
            padding: 20px;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .form-control {
            margin-bottom: 15px;
        }
        .btn-primary {
            width: 100%;
        }
        .form-title {
            text-align: center;
            margin-bottom: 20px;
            color: #333;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <!-- Formulario de Inicio de Sesión -->
            <div class="login-container">
                <h2 class="form-title">Iniciar Sesión</h2>
                <form action="../controllers/LoginController.php" method="POST">
                    <div class="mb-3">
                        <input type="email" class="form-control" name="email" placeholder="Correo electrónico" required>
                    </div>
                    <div class="mb-3">
                        <input type="password" class="form-control" name="password" placeholder="Contraseña" required>
                    </div>
                    <button type="submit" class="btn btn-primary" name="login">Iniciar Sesión</button>
                </form>
            </div>
        </div>

        <div class="col-md-6">
            <!-- Formulario de Registro -->
            <div class="login-container">
                <h2 class="form-title">Registrarse</h2>
                <form action="controllers/RegisterController.php" method="POST">
                    <div class="mb-3">
                        <select class="form-select" name="tipo_documento" required>
                            <option value="" disabled selected>Tipo de documento</option>
                            <option value="Cédula">Cédula</option>
                            <option value="Tarjeta de Identidad">Tarjeta de Identidad</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <input type="text" class="form-control" name="documento" placeholder="Número de documento" required>
                    </div>
                    <div class="mb-3">
                        <input type="text" class="form-control" name="nombre" placeholder="Nombre(s)" required>
                    </div>
                    <div class="mb-3">
                        <input type="text" class="form-control" name="apellido" placeholder="Apellido(s)" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Fecha de nacimiento</label>
                        <input type="date" class="form-control" name="fecha_nacimiento" required>
                    </div>
                    <div class="mb-3">
                        <select class="form-select" name="genero" required>
                            <option value="" disabled selected>Género</option>
                            <option value="Masculino">Masculino</option>
                            <option value="Femenino">Femenino</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <input type="email" class="form-control" name="email" placeholder="Correo electrónico" required>
                    </div>
                    <div class="mb-3">
                        <input type="password" class="form-control" name="password" placeholder="Contraseña" required>
                    </div>
                    <button type="submit" class="btn btn-success" name="register">Registrarse</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const msg = "<?php echo htmlspecialchars($msg, ENT_QUOTES); ?>";
        const status = "<?php echo htmlspecialchars($status, ENT_QUOTES); ?>";

        if (msg && status) {
            Swal.fire({
                icon: status === 'success' ? 'success' : 'error',
                title: status === 'success' ? 'Éxito' : 'Error',
                text: msg,
                timer: 3000,
                showConfirmButton: false
            });
        }
    });
</script>

<!-- Bootstrap JS Bundle con Popper -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>