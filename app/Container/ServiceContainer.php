<?php

require_once __DIR__ . '/../Models/Database.php';
require_once __DIR__ . '/../Models/User.php';
require_once __DIR__ . '/../Models/Post.php';
require_once __DIR__ . '/../Services/Uploader.php';

/**
 * ServiceContainer - Contenedor simple para inyección de dependencias
 * Centraliza la creación de servicios y modelos con sus dependencias
 */
class ServiceContainer
{
    private static ?ServiceContainer $instance = null;
    private ?Database $database = null;
    private array $services = [];

    /**
     * Obtener instancia singleton del contenedor
     * 
     * @return ServiceContainer
     */
    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Obtener instancia de Database (singleton)
     * 
     * @return Database
     */
    public function getDatabase(): Database
    {
        if ($this->database === null) {
            $this->database = new Database();
        }
        return $this->database;
    }

    /**
     * Crear instancia de User con DI
     * 
     * @return User
     */
    public function createUser(): User
    {
        return new User($this->getDatabase());
    }

    /**
     * Crear instancia de Post con DI
     * 
     * @return Post
     */
    public function createPost(): Post
    {
        return new Post($this->getDatabase());
    }

    /**
     * Obtener instancia de Uploader
     * 
     * @return Uploader
     */
    public function getUploader(): Uploader
    {
        if (!isset($this->services['uploader'])) {
            $this->services['uploader'] = new Uploader();
        }
        return $this->services['uploader'];
    }

    /**
     * Registrar un servicio personalizado
     * 
     * @param string $name
     * @param mixed $service
     * @return void
     */
    public function register(string $name, mixed $service): void
    {
        $this->services[$name] = $service;
    }

    /**
     * Obtener un servicio registrado
     * 
     * @param string $name
     * @return mixed|null
     */
    public function get(string $name): mixed
    {
        return $this->services[$name] ?? null;
    }

    /**
     * Verificar si un servicio está registrado
     * 
     * @param string $name
     * @return bool
     */
    public function has(string $name): bool
    {
        return isset($this->services[$name]);
    }

    /**
     * Limpiar todos los servicios (útil para tests)
     * 
     * @return void
     */
    public function clear(): void
    {
        $this->services = [];
        $this->database = null;
    }
}
