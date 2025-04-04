<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once "../models/LoginModel.php";
require_once "../models/Conexion.php";

class LoginController
{
    private $email;
    private $password;

    public function __construct($email, $password) {
        $this->email = filter_var($email, FILTER_SANITIZE_EMAIL);
        $this->password = trim($password);
    }

    public function login()
    {
        try {
            $model = new LoginModel();
            $usuario = $model->validarCredenciales($this->email, $this->password);

            if ($usuario) {
                // Establecer sesión
                $_SESSION['id_usuario'] = $usuario['id_usuario'];
                $_SESSION['email'] = $usuario['email'];
                $_SESSION['rol'] = $usuario['rol'];  // Importante para el dashboard
                $_SESSION['nombre'] = $usuario['nombre']; // Para mostrar en el dashboard

                // Redirección según rol
                $redirect = ($usuario['rol'] === 'admin') ? 'admin/dashboard.php' : 'cliente/dashboard.php';

                header("Location: /IdealEventsx/views/$redirect?msg=" . urlencode("Bienvenido " . $usuario['nombre']) . "&status=success");
                exit();
            } else {
                throw new Exception("Credenciales incorrectas");
            }
        } catch (Exception $e) {
            error_log("Error en login: " . $e->getMessage());
            header("Location: ../views/login.php?msg=" . urlencode($e->getMessage()) . "&status=error");
            exit();
        }
    }
}

// Procesamiento del formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['email']) && !empty($_POST['password'])) {
    $loginController = new LoginController($_POST['email'], $_POST['password']);
    $loginController->login();
} else {
    header("Location: ../views/login.php?msg=" . urlencode("Por favor complete todos los campos") . "&status=error");
    exit();
}