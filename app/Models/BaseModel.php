<?php

/**
 * BaseModel - Clase abstracta base para todos los modelos
 * Proporciona:
 * - Inyección de dependencia de Database
 * - Métodos comunes para gestión de excepciones y responsabilidad única
 * - Type hints y visibilidad explícita
 */
abstract class BaseModel
{
    protected Database $db;

    /**
     * Constructor con inyección de dependencia de Database
     * 
     * @param Database|null $db Instancia de Database o null para crear nueva
     */
    public function __construct(?Database $db = null)
    {
        $this->db = $db instanceof Database ? $db : new Database();
    }

    /**
     * Obtener instancia de Database
     * 
     * @return Database
     */
    protected function getDatabase(): Database
    {
        return $this->db;
    }

    /**
     * Validar que un ID es válido (> 0)
     * 
     * @param int $id
     * @return void
     * @throws InvalidArgumentException
     */
    protected function validateId(int $id): void
    {
        if ($id <= 0) {
            throw new InvalidArgumentException('El ID debe ser un número positivo');
        }
    }

    /**
     * Validar que una cadena no está vacía
     * 
     * @param string $value
     * @param string $fieldName
     * @return void
     * @throws InvalidArgumentException
     */
    protected function validateNotEmpty(string $value, string $fieldName): void
    {
        if (empty(trim($value))) {
            throw new InvalidArgumentException("El campo '{$fieldName}' no puede estar vacío");
        }
    }

    /**
     * Manejar excepciones de columna faltante en BD
     * 
     * @param PDOException $e
     * @return bool true si es error de columna faltante
     */
    protected function isMissingColumnError(PDOException $e): bool
    {
        $msg = $e->getMessage();
        return strpos($msg, 'Unknown column') !== false || strpos($msg, '1054') !== false;
    }
}
