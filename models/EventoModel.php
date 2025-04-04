<?php
include_once "Conexion.php";

class EventoModel 
{
    public static function mdlListarEventos() {
        try {
            $conexion = Conexion::conectar();
            $stmt = $conexion->prepare("SELECT * FROM evento");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            die("Error al listar eventos: " . $e->getMessage());
        }
    }
    
    public static function mdlObtenerEventoPorId($id) {
        try {
            $conexion = Conexion::conectar();
            $stmt = $conexion->prepare("SELECT * FROM evento WHERE id_evento = :id");
            $stmt->bindParam(":id", $id, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            die("Error al obtener evento: " . $e->getMessage());
        }
    }
    
    public static function mdlAgregarEvento($titulo, $descripcion, $fecha, $hora, $ubicacion, $categoria, $precio) {
        try {
            $conexion = Conexion::conectar();
            $stmt = $conexion->prepare("INSERT INTO evento 
                (titulo, descripción, fecha, hora, ubicacion, categoria, precio) 
                VALUES (:titulo, :descripcion, :fecha, :hora, :ubicacion, :categoria, :precio)");

            $stmt->bindParam(":titulo", $titulo, PDO::PARAM_STR);
            $stmt->bindParam(":descripcion", $descripcion, PDO::PARAM_STR);
            $stmt->bindParam(":fecha", $fecha, PDO::PARAM_STR);
            $stmt->bindParam(":hora", $hora, PDO::PARAM_STR);
            $stmt->bindParam(":ubicacion", $ubicacion, PDO::PARAM_STR);
            $stmt->bindParam(":categoria", $categoria, PDO::PARAM_STR);
            $stmt->bindParam(":precio", $precio, PDO::PARAM_STR);

            return $stmt->execute();
        } catch (PDOException $e) {
            die("Error al agregar evento: " . $e->getMessage());
        }
    }
    
    public static function mdlEditarEvento($id_evento, $titulo, $descripcion, $fecha, $hora, $ubicacion, $categoria, $precio) {
        try {
            $conexion = Conexion::conectar();
            $stmt = $conexion->prepare("UPDATE evento 
                SET titulo = :titulo, 
                    descripción = :descripcion, 
                    fecha = :fecha, 
                    hora = :hora, 
                    ubicacion = :ubicacion, 
                    categoria = :categoria, 
                    precio = :precio
                WHERE id_evento = :id_evento");

            $stmt->bindParam(":id_evento", $id_evento, PDO::PARAM_INT);
            $stmt->bindParam(":titulo", $titulo, PDO::PARAM_STR);
            $stmt->bindParam(":descripcion", $descripcion, PDO::PARAM_STR);
            $stmt->bindParam(":fecha", $fecha, PDO::PARAM_STR);
            $stmt->bindParam(":hora", $hora, PDO::PARAM_STR);
            $stmt->bindParam(":ubicacion", $ubicacion, PDO::PARAM_STR);
            $stmt->bindParam(":categoria", $categoria, PDO::PARAM_STR);
            $stmt->bindParam(":precio", $precio, PDO::PARAM_STR);

            return $stmt->execute();
        } catch (PDOException $e) {
            die("Error al editar evento: " . $e->getMessage());
        }
    }
    
    public static function mdlEliminarEvento($id_evento) {
        try {
            $conexion = Conexion::conectar();
            $stmt = $conexion->prepare("DELETE FROM evento WHERE id_evento = :id_evento");
            $stmt->bindParam(":id_evento", $id_evento, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            die("Error al eliminar evento: " . $e->getMessage());
        }
    }
    
    // Método adicional para obtener eventos por organizador (si lo necesitas)
    public static function mdlObtenerEventosPorOrganizador($id_organizador) {
        try {
            $conexion = Conexion::conectar();
            $stmt = $conexion->prepare("SELECT * FROM evento WHERE id_organizador = :id_organizador");
            $stmt->bindParam(":id_organizador", $id_organizador, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            die("Error al obtener eventos por organizador: " . $e->getMessage());
        }
    }
}