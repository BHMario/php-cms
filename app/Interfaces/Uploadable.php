<?php

/**
 * Uploadable - Interfaz para clases que manejan subida de archivos
 */
interface Uploadable
{
    /**
     * Almacenar un archivo subido
     * 
     * @param array $file Array de $_FILES
     * @param string $subdir Subdirectorio relativo a uploads
     * @return string Ruta pública del archivo almacenado
     * @throws RuntimeException si la subida falla
     */
    public function store(array $file, string $subdir = 'files'): string;
}
