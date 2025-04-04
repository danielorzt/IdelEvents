<?php
require_once 'Conexion.php';

class UsuarioModel {
    private $conn;

    public function __construct() {
        $conexion = new Conexion();
        $this->conn = $conexion->Conexion();
    }

    public function getUsuarios() {
        $sql = "SELECT u.*, GROUP_CONCAT(r.nombre) as roles 
                FROM Usuario u 
                LEFT JOIN usuario_rol ur ON u.id_usuario = ur.id_usuario 
                LEFT JOIN rol_usuario r ON ur.id_rol = r.id_rol 
                GROUP BY u.id_usuario";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getUsuarioById($id) {
        $sql = "SELECT * FROM Usuario WHERE id_usuario = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getRolesUsuario($id_usuario) {
        $sql = "SELECT r.id_rol, r.nombre FROM rol_usuario r 
                INNER JOIN usuario_rol ur ON r.id_rol = ur.id_rol 
                WHERE ur.id_usuario = :id_usuario";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(":id_usuario", $id_usuario, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAllRoles() {
        $sql = "SELECT * FROM rol_usuario";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function updateUsuario($id, $datos) {
        try {
            $this->conn->beginTransaction();

            $sql = "UPDATE Usuario SET 
                    tipo_documento = :tipo_documento,
                    documento = :documento,
                    nombre = :nombre,
                    apellido = :apellido,
                    fecha_nacimiento = :fecha_nacimiento,
                    genero = :genero,
                    email = :email";

            // Solo actualizar la contraseña si se proporciona una nueva
            if (!empty($datos['password'])) {
                $sql .= ", password = :password";
            }

            $sql .= " WHERE id_usuario = :id_usuario";

            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(":tipo_documento", $datos['tipo_documento']);
            $stmt->bindParam(":documento", $datos['documento']);
            $stmt->bindParam(":nombre", $datos['nombre']);
            $stmt->bindParam(":apellido", $datos['apellido']);
            $stmt->bindParam(":fecha_nacimiento", $datos['fecha_nacimiento']);
            $stmt->bindParam(":genero", $datos['genero']);
            $stmt->bindParam(":email", $datos['email']);

            if (!empty($datos['password'])) {
                $password_hash = password_hash($datos['password'], PASSWORD_DEFAULT);
                $stmt->bindParam(":password", $password_hash);
            }

            $stmt->bindParam(":id_usuario", $id, PDO::PARAM_INT);
            $stmt->execute();

            // Actualizar roles si se proporcionan
            if (isset($datos['roles'])) {
                // Eliminar roles actuales
                $sql = "DELETE FROM usuario_rol WHERE id_usuario = :id_usuario";
                $stmt = $this->conn->prepare($sql);
                $stmt->bindParam(":id_usuario", $id, PDO::PARAM_INT);
                $stmt->execute();

                // Insertar nuevos roles
                foreach ($datos['roles'] as $rol_id) {
                    $sql = "INSERT INTO usuario_rol (id_usuario, id_rol) VALUES (:id_usuario, :id_rol)";
                    $stmt = $this->conn->prepare($sql);
                    $stmt->bindParam(":id_usuario", $id, PDO::PARAM_INT);
                    $stmt->bindParam(":id_rol", $rol_id, PDO::PARAM_INT);
                    $stmt->execute();
                }
            }

            $this->conn->commit();
            return true;
        } catch (PDOException $e) {
            $this->conn->rollBack();
            error_log("Error al actualizar usuario: " . $e->getMessage());
            return false;
        }
    }

    public function deleteUsuario($id) {
        try {
            $this->conn->beginTransaction();

            // Primero eliminar las relaciones en usuario_rol
            $sql = "DELETE FROM usuario_rol WHERE id_usuario = :id_usuario";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(":id_usuario", $id, PDO::PARAM_INT);
            $stmt->execute();

            // Luego eliminar el usuario
            $sql = "DELETE FROM Usuario WHERE id_usuario = :id_usuario";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(":id_usuario", $id, PDO::PARAM_INT);
            $stmt->execute();

            $this->conn->commit();
            return true;
        } catch (PDOException $e) {
            $this->conn->rollBack();
            error_log("Error al eliminar usuario: " . $e->getMessage());
            return false;
        }
    }
}
?>