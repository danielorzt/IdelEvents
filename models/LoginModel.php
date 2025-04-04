<?php
require_once "Conexion.php";

class LoginModel
{
    private $db;

    public function __construct() {
        $this->db = Conexion::obtenerConexion();  // Usar el nombre correcto
    }

    public function validarCredenciales($email, $password)
    {
        try {
            $stmt = $this->db->prepare("SELECT id_usuario, email, password, rol, nombre FROM usuario WHERE email = :email AND activo = 1");
            $stmt->bindParam(':email', $email);
            $stmt->execute();

            $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($usuario && password_verify($password, $usuario['password'])) {
                return $usuario;
            }

            return false;
        } catch (PDOException $e) {
            error_log("Error en LoginModel: " . $e->getMessage());
            return false;
        }
    }
}