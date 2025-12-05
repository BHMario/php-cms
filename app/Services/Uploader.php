<?php

require_once __DIR__ . '/../Interfaces/Uploadable.php';

/**
 * Uploader - Servicio para gestión segura de subidas de archivos
 * Implementa validación de tamaño, tipo MIME, generación de nombres aleatorios
 * y almacenamiento en directorios organizados.
 */
class Uploader implements Uploadable
{
    private string $basePath;
    private int $maxFileSize;
    private array $allowedMime;

    /**
     * Constructor con configuración de subidas
     * 
     * @param string|null $basePath Directorio base para uploads
     * @param int $maxFileSize Tamaño máximo en bytes (default 2MB)
     * @param array $allowedMime Tipos MIME permitidos
     */
    public function __construct(?string $basePath = null, int $maxFileSize = 2097152, array $allowedMime = [])
    {
        $this->basePath = $basePath ?? realpath(__DIR__ . '/../../public/uploads');
        $this->maxFileSize = $maxFileSize;
        $this->allowedMime = !empty($allowedMime) ? $allowedMime : ['image/jpeg', 'image/png', 'image/gif'];
    }

    /**
     * Almacenar archivo con validaciones y nombre aleatorio
     * 
     * @param array $file Array de $_FILES
     * @param string $subdir Subdirectorio para organizar uploads
     * @return string Ruta pública relativa a /public
     * @throws RuntimeException si hay algún error
     */
    public function store(array $file, string $subdir = 'files'): string
    {
        $this->validateUpload($file);
        $this->validateFileSize($file);
        $this->validateImageType($file);

        $filename = $this->generateSecureFilename($file);
        $targetDir = $this->ensureTargetDirectory($subdir);
        $target = $targetDir . DIRECTORY_SEPARATOR . $filename;

        if (!move_uploaded_file($file['tmp_name'], $target)) {
            throw new RuntimeException('No se pudo mover el archivo subido');
        }

        return 'uploads/' . trim($subdir, '/') . '/' . $filename;
    }

    /**
     * Validar que el archivo fue subido correctamente
     * 
     * @param array $file
     * @throws RuntimeException
     */
    private function validateUpload(array $file): void
    {
        if (!isset($file['tmp_name']) || !is_uploaded_file($file['tmp_name'])) {
            throw new RuntimeException('No se recibió un archivo válido');
        }

        if ($file['error'] !== UPLOAD_ERR_OK) {
            throw new RuntimeException('Error en la subida: código ' . $file['error']);
        }
    }

    /**
     * Validar tamaño del archivo
     * 
     * @param array $file
     * @throws RuntimeException
     */
    private function validateFileSize(array $file): void
    {
        if (!isset($file['size'])) {
            throw new RuntimeException('No se pudo determinar el tamaño del archivo');
        }

        if ($file['size'] > $this->maxFileSize) {
            $maxMb = round($this->maxFileSize / 1024 / 1024, 1);
            throw new RuntimeException("El archivo excede el tamaño máximo permitido ({$maxMb}MB)");
        }

        if ($file['size'] === 0) {
            throw new RuntimeException('El archivo está vacío');
        }
    }

    /**
     * Validar que es una imagen y obtener información
     * 
     * @param array $file
     * @throws RuntimeException
     */
    private function validateImageType(array $file): void
    {
        $info = @getimagesize($file['tmp_name']);
        if ($info === false) {
            throw new RuntimeException('El archivo no es una imagen válida');
        }

        $mime = $info['mime'] ?? '';
        if (!in_array($mime, $this->allowedMime, true)) {
            throw new RuntimeException('Tipo de archivo no permitido: ' . ($mime ?: 'desconocido'));
        }
    }

    /**
     * Generar nombre de archivo seguro con extensión
     * 
     * @param array $file
     * @return string Nombre único y seguro
     * @throws RuntimeException
     */
    private function generateSecureFilename(array $file): string
    {
        $info = @getimagesize($file['tmp_name']);
        if ($info === false) {
            throw new RuntimeException('No se pudo procesar la imagen');
        }

        $ext = @image_type_to_extension($info[2], false);
        if ($ext === false) {
            throw new RuntimeException('Tipo de imagen no reconocido');
        }

        return bin2hex(random_bytes(16)) . '.' . $ext;
    }

    /**
     * Asegurar que el directorio de destino existe
     * 
     * @param string $subdir
     * @return string Ruta absoluta al directorio
     * @throws RuntimeException
     */
    private function ensureTargetDirectory(string $subdir): string
    {
        $targetDir = rtrim($this->basePath, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . trim($subdir, DIRECTORY_SEPARATOR);
        
        if (!is_dir($targetDir)) {
            if (!mkdir($targetDir, 0755, true)) {
                throw new RuntimeException('No se pudo crear el directorio de destino');
            }
        }

        return $targetDir;
    }

    /**
     * Obtener tamaño máximo permitido en bytes
     * 
     * @return int
     */
    public function getMaxFileSize(): int
    {
        return $this->maxFileSize;
    }

    /**
     * Establecer tipos MIME permitidos
     * 
     * @param array $mimes
     * @return self
     */
    public function setAllowedMimes(array $mimes): self
    {
        $this->allowedMime = $mimes;
        return $this;
    }
}
