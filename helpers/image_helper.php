<?php
function getImagePath($imageId) {
    if (empty($imageId)) {
        return 'public/img/default-event.jpg'; // Imagen por defecto
    }

    $conexion = new Conexion();
    $db = $conexion->getConnection();

    $query = "SELECT ruta FROM imagenes WHERE id_imagen = ?";
    $stmt = $db->prepare($query);
    $stmt->execute([$imageId]);
    $image = $stmt->fetch(PDO::FETCH_ASSOC);

    return $image ? 'public/img/' . $image['ruta'] : 'public/img/default-event.jpg';
}