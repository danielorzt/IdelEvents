<?php
require_once 'models/EventoModel.php';

class EventoController {
    // Método para listar todos los eventos
    public function listar() {
        $eventos = EventoModel::mdlListarEventos();
        
        // Puedes cargar una vista aquí o devolver los datos como JSON
        // Ejemplo con vista:
        require_once 'views/evento/listar.php';
    }
    
    // Método para mostrar un evento específico
    public function ver($id) {
        $evento = EventoModel::mdlObtenerEventoPorId($id);
        
        if (!$evento) {
            // Manejar el caso cuando el evento no existe
            header("Location: " . BASE_URL . "error/notFound");
            exit();
        }
        
        // Cargar vista de detalle
        require_once 'views/evento/ver.php';
    }
    
    // Método para mostrar el formulario de creación
    public function crear() {
        // Verificar permisos si es necesario
        // if (!Auth::check() || !Auth::isAdmin()) { ... }
        
        require_once 'views/evento/crear.php';
    }
    
    // Método para guardar un nuevo evento
    public function guardar() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Validar datos del formulario
            $titulo = isset($_POST['titulo']) ? trim($_POST['titulo']) : '';
            $descripcion = isset($_POST['descripcion']) ? trim($_POST['descripcion']) : '';
            $fecha = isset($_POST['fecha']) ? trim($_POST['fecha']) : '';
            $hora = isset($_POST['hora']) ? trim($_POST['hora']) : '';
            $ubicacion = isset($_POST['ubicacion']) ? trim($_POST['ubicacion']) : '';
            $categoria = isset($_POST['categoria']) ? trim($_POST['categoria']) : '';
            $precio = isset($_POST['precio']) ? floatval($_POST['precio']) : 0.00;
            
            // Validaciones básicas
            if (empty($titulo) || empty($descripcion) || empty($fecha)) {
                $_SESSION['error'] = "Los campos título, descripción y fecha son obligatorios";
                header("Location: " . BASE_URL . "evento/crear");
                exit();
            }
            
            // Guardar en la base de datos
            $resultado = EventoModel::mdlAgregarEvento($titulo, $descripcion, $fecha, $hora, $ubicacion, $categoria, $precio);
            
            if ($resultado) {
                $_SESSION['success'] = "Evento creado correctamente";
                header("Location: " . BASE_URL . "evento/listar");
            } else {
                $_SESSION['error'] = "Error al crear el evento";
                header("Location: " . BASE_URL . "evento/crear");
            }
            exit();
        }
        
        // Si no es POST, redirigir
        header("Location: " . BASE_URL . "evento/listar");
        exit();
    }
    
    // Método para mostrar el formulario de edición
    public function editar($id) {
        $evento = EventoModel::mdlObtenerEventoPorId($id);
        
        if (!$evento) {
            $_SESSION['error'] = "Evento no encontrado";
            header("Location: " . BASE_URL . "evento/listar");
            exit();
        }
        
        require_once 'views/evento/editar.php';
    }
    
    // Método para actualizar un evento
    public function actualizar($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $titulo = isset($_POST['titulo']) ? trim($_POST['titulo']) : '';
            $descripcion = isset($_POST['descripcion']) ? trim($_POST['descripcion']) : '';
            $fecha = isset($_POST['fecha']) ? trim($_POST['fecha']) : '';
            $hora = isset($_POST['hora']) ? trim($_POST['hora']) : '';
            $ubicacion = isset($_POST['ubicacion']) ? trim($_POST['ubicacion']) : '';
            $categoria = isset($_POST['categoria']) ? trim($_POST['categoria']) : '';
            $precio = isset($_POST['precio']) ? floatval($_POST['precio']) : 0.00;
            
            // Validaciones
            if (empty($titulo) || empty($descripcion) || empty($fecha)) {
                $_SESSION['error'] = "Los campos título, descripción y fecha son obligatorios";
                header("Location: " . BASE_URL . "evento/editar/$id");
                exit();
            }
            
            $resultado = EventoModel::mdlEditarEvento($id, $titulo, $descripcion, $fecha, $hora, $ubicacion, $categoria, $precio);
            
            if ($resultado) {
                $_SESSION['success'] = "Evento actualizado correctamente";
            } else {
                $_SESSION['error'] = "Error al actualizar el evento";
            }
            
            header("Location: " . BASE_URL . "evento/ver/$id");
            exit();
        }
        
        // Si no es POST, redirigir
        header("Location: " . BASE_URL . "evento/listar");
        exit();
    }
    
    // Método para eliminar un evento
    public function eliminar($id) {
        // Verificar permisos si es necesario
        // if (!Auth::check() || !Auth::isAdmin()) { ... }
        
        $resultado = EventoModel::mdlEliminarEvento($id);
        
        if ($resultado) {
            $_SESSION['success'] = "Evento eliminado correctamente";
        } else {
            $_SESSION['error'] = "Error al eliminar el evento";
        }
        
        header("Location: " . BASE_URL . "evento/listar");
        exit();
    }
    
    // Método para listar eventos por organizador
    public function porOrganizador($id_organizador) {
        $eventos = EventoModel::mdlObtenerEventosPorOrganizador($id_organizador);
        
        // Cargar vista específica o devolver JSON
        require_once 'views/evento/organizador.php';
    }
}