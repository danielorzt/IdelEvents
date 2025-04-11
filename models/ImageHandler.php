<?php
class ImageHandler
{
    private $uploadDir;
    private $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
    private $maxSize = 5 * 1024 * 1024; // 5MB

    public function __construct()
    {
        // Set the path to the uploads directory - make sure it exists
        $this->uploadDir = __DIR__ . '/../uploads/';

        // Create directory if it doesn't exist
        if (!file_exists($this->uploadDir)) {
            mkdir($this->uploadDir, 0755, true);
        }

        // Also ensure the eventos subdirectory exists
        if (!file_exists($this->uploadDir . 'eventos/')) {
            mkdir($this->uploadDir . 'eventos/', 0755, true);
        }
    }

    public function uploadImage($file, $subfolder = 'eventos', $old_image = null)
    {
        // Validate file data
        if (!isset($file['error']) || is_array($file['error'])) {
            throw new Exception('Parámetros inválidos.');
        }

        // Check upload errors
        if ($file['error'] !== UPLOAD_ERR_OK) {
            $errorMessages = [
                UPLOAD_ERR_INI_SIZE => 'El archivo excede el tamaño máximo permitido por PHP.',
                UPLOAD_ERR_FORM_SIZE => 'El archivo excede el tamaño máximo permitido por el formulario.',
                UPLOAD_ERR_PARTIAL => 'El archivo fue subido parcialmente.',
                UPLOAD_ERR_NO_FILE => 'No se subió ningún archivo.',
                UPLOAD_ERR_NO_TMP_DIR => 'Falta la carpeta temporal.',
                UPLOAD_ERR_CANT_WRITE => 'Error al escribir el archivo en el disco.',
                UPLOAD_ERR_EXTENSION => 'Una extensión de PHP detuvo la subida del archivo.'
            ];

            $errorMsg = isset($errorMessages[$file['error']])
                ? $errorMessages[$file['error']]
                : 'Error desconocido al subir el archivo.';

            throw new Exception($errorMsg);
        }

        // Validate file type
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mime = $finfo->file($file['tmp_name']);

        if (!in_array($mime, $this->allowedTypes)) {
            throw new Exception('Tipo de archivo no permitido: ' . $mime . '. Solo se permiten JPG, PNG y GIF.');
        }

        // Validate file size
        if ($file['size'] > $this->maxSize) {
            throw new Exception('El archivo excede el tamaño máximo permitido de 5MB.');
        }

        // Create directory structure by year/month
        $currentYear = date('Y');
        $currentMonth = date('m');
        $targetDir = $this->uploadDir . $subfolder . '/' . $currentYear . '/' . $currentMonth . '/';

        // Create directory if it doesn't exist
        if (!file_exists($targetDir)) {
            if (!mkdir($targetDir, 0755, true)) {
                throw new Exception('No se pudo crear el directorio para guardar la imagen: ' . $targetDir);
            }
        }

        // Delete old image if it exists
        if ($old_image && file_exists(__DIR__ . '/../' . $old_image)) {
            unlink(__DIR__ . '/../' . $old_image);
        }

        // Generate unique filename
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = uniqid('img_', true) . '.' . $extension;
        $relativePath = 'uploads/' . $subfolder . '/' . $currentYear . '/' . $currentMonth . '/' . $filename;
        $fullPath = $targetDir . $filename;

        // Move uploaded file
        if (!move_uploaded_file($file['tmp_name'], $fullPath)) {
            throw new Exception('No se pudo guardar el archivo subido en: ' . $fullPath);
        }

        // Debug info - write to log for troubleshooting
        error_log("Image uploaded successfully - Full path: " . $fullPath);
        error_log("Image relative path: " . $relativePath);

        // Return image information
        return [
            'nombre_archivo' => $file['name'],
            'mime_type' => $mime,
            'tamaño' => $file['size'],
            'ruta' => $relativePath,
            'full_path' => $fullPath
        ];
    }

    public function deleteImage($imagePath)
    {
        if (empty($imagePath)) {
            return true;
        }

        $fullPath = __DIR__ . '/../' . $imagePath;

        if (file_exists($fullPath)) {
            return unlink($fullPath);
        }

        return false;
    }
}