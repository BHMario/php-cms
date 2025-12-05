<?php
/**
 * database/init.php - Inicializacion centralizada de BD
 * 
 * Este archivo maneja TODAS las migraciones y configuraciones de BD.
 * Se puede ejecutar varias veces
**/

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../app/Models/Database.php';
require_once __DIR__ . '/../app/Models/Post.php';

class DatabaseInitializer
{
    private $db;
    private $changes = [];
    
    public function __construct()
    {
        try {
            $this->db = new Database();
        } catch (Exception $e) {
            echo "Error: No se pudo conectar a la BD\n";
            echo "   Asegura que MySQL este corriendo\n";
            echo "   y que config/config.php tenga los datos correctos\n";
            exit(1);
        }
    }
    
    /**
     * Ejecutar todas las migraciones
     */
    public function run()
    {
        echo "Inicializando BD...\n\n";
        
        $this->addColumnBio();
        $this->addColumnSlug();
        $this->ensureIndexSlug();
        $this->generateSlugsForExistingPosts();
        
        $this->printSummary();
    }
    
    /**
     * Agregar columna bio a usuarios si no existe
     */
    private function addColumnBio()
    {
        try {
            $exists = $this->columnExists('users', 'bio');
            
            if (!$exists) {
                echo "Agregando columna 'bio' a usuarios...\n";
                $this->db->query("ALTER TABLE users ADD COLUMN bio TEXT NULL AFTER profile_image");
                $this->addChange('Columna bio agregada a usuarios');
                echo "   OK: Columna bio agregada\n";
            } else {
                echo "OK: Columna bio ya existe en usuarios\n";
            }
        } catch (Exception $e) {
            echo "Aviso sobre bio: " . $e->getMessage() . "\n";
        }
    }
    
    /**
     * Agregar columna slug a posts si no existe
     */
    private function addColumnSlug()
    {
        try {
            $exists = $this->columnExists('posts', 'slug');
            
            if (!$exists) {
                echo "Agregando columna 'slug' a posts...\n";
                $this->db->query("ALTER TABLE posts ADD COLUMN slug VARCHAR(255) UNIQUE AFTER title");
                $this->addChange('Columna slug agregada a posts');
                echo "   OK: Columna slug agregada\n";
            } else {
                echo "OK: Columna slug ya existe en posts\n";
            }
        } catch (Exception $e) {
            echo "Error al agregar slug: " . $e->getMessage() . "\n";
            throw $e;
        }
    }
    
    /**
     * Crear índice para slug si no existe
     */
    private function ensureIndexSlug()
    {
        try {
            $indexExists = $this->indexExists('posts', 'idx_slug');
            
            if (!$indexExists) {
                echo "Agregando indice 'idx_slug' a posts...\n";
                $this->db->query("ALTER TABLE posts ADD INDEX idx_slug (slug)");
                $this->addChange('Indice idx_slug agregado para busquedas rapidas');
                echo "   OK: Indice agregado\n";
            } else {
                echo "OK: Indice idx_slug ya existe\n";
            }
        } catch (Exception $e) {
            echo "Aviso sobre indice: " . $e->getMessage() . "\n";
        }
    }
    
    /**
     * Generar slugs para posts existentes sin slug
     */
    private function generateSlugsForExistingPosts()
    {
        try {
            echo "Buscando posts sin slug...\n";
            
            $postsWithoutSlug = $this->db->fetchAll(
                "SELECT id, title FROM posts WHERE slug IS NULL OR slug = '' ORDER BY id ASC"
            );
            
            if (empty($postsWithoutSlug)) {
                echo "   OK: Todos los posts tienen slug\n";
                return;
            }
            
            echo "   Se encontraron " . count($postsWithoutSlug) . " posts sin slug\n";
            echo "   Generando slugs...\n\n";
            
            $postModel = new Post($this->db);
            $processedCount = 0;
            
            foreach ($postsWithoutSlug as $post) {
                $generatedSlug = $postModel->generateSlug($post['title']);
                
                // Asegurar unicidad
                $counter = 1;
                $slug = $generatedSlug;
                while ($this->db->fetch("SELECT id FROM posts WHERE slug = ?", [$slug])) {
                    $slug = $generatedSlug . '-' . $counter++;
                    if ($counter > 1000) {
                        $slug = $generatedSlug . '-' . time();
                        break;
                    }
                }
                
                // Actualizar
                $this->db->query("UPDATE posts SET slug = ? WHERE id = ?", [$slug, $post['id']]);
                
                echo "      OK: Post ID {$post['id']}: '{$post['title']}' -> '$slug'\n";
                $processedCount++;
            }
            
            if ($processedCount > 0) {
                $this->addChange("$processedCount posts actualizados con slugs");
                echo "\n   Se actualizaron $processedCount posts\n";
            }
            
        } catch (Exception $e) {
            echo "Error al generar slugs: " . $e->getMessage() . "\n";
            throw $e;
        }
    }
    
    /**
     * Verificar si una columna existe en una tabla
     */
    private function columnExists($table, $column)
    {
        try {
            // Usar DESCRIBE, que es más portable
            // Si la columna no existe, retorna false (o genera error)
            $result = @$this->db->fetch("DESCRIBE $table $column");
            return $result !== null && !empty($result);
        } catch (Exception $e) {
            // Si hay error, la columna no existe
            return false;
        }
    }
    
    /**
     * Verificar si un índice existe en una tabla
     */
    private function indexExists($table, $indexName)
    {
        try {
            $result = $this->db->fetchAll("SHOW KEYS FROM $table WHERE Key_name = ?", [$indexName]);
            return !empty($result);
        } catch (Exception $e) {
            // Si hay error, el índice no existe
            return false;
        }
    }
    
    /**
     * Registrar cambio
     */
    private function addChange($message)
    {
        $this->changes[] = $message;
    }
    
    /**
     * Imprimir resumen
     */
    private function printSummary()
    {
        echo "\n========================================\n";
        echo "OK: Inicializacion completada\n";
        
        if (!empty($this->changes)) {
            echo "\nCambios realizados:\n";
            foreach ($this->changes as $i => $change) {
                echo "   " . ($i + 1) . ". $change\n";
            }
        } else {
            echo "\nBD ya estaba actualizada\n";
        }
        
        echo "========================================\n\n";
    }
}

// Si se ejecuta directamente desde terminal
if (php_sapi_name() === 'cli' && !isset($GLOBALS['_DATABASE_INITIALIZER_EXECUTED'])) {
    $GLOBALS['_DATABASE_INITIALIZER_EXECUTED'] = true;
    $initializer = new DatabaseInitializer();
    $initializer->run();
}

// Retornar instancia para uso desde otros archivos
return DatabaseInitializer::class;
