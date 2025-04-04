<?php
session_start();

require_once 'controllers/LoginController.php';
require_once 'controllers/AdminDashboardController.php';
require_once 'controllers/ClienteDashboardController.php';

$action = $_GET['action'] ?? 'index';
$controller = $_GET['controller'] ?? 'Login';

// Verificar si el usuario está logueado para acceder a los dashboards
if (!isset($_SESSION['id_usuario']) && $controller !== 'Login') {
    header("Location: views/login.php");
    exit();
}

$controllerClass = $controller . 'Controller';

if (class_exists($controllerClass)) {
    $controllerInstance = new $controllerClass();

    if (method_exists($controllerInstance, $action)) {
        $controllerInstance->$action();
    } else {
        header("HTTP/1.0 404 Not Found");
        include 'views/errors/404.php';
    }
} else {
    header("HTTP/1.0 404 Not Found");
    include 'views/errors/404.php';
}