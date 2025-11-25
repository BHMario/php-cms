<?php
/**
 * Crear nuevas tablas y columas.
 * Run: php migrate.php
 */

$config = require __DIR__ . '/../config/config.php';
$dsn = "mysql:host={$config['host']};dbname={$config['dbname']};charset={$config['charset']}";

try {
    $pdo = new PDO($dsn, $config['user'], $config['password'], [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    ]);
    
    echo "Conectado a la base de datos.\n";
    
    // Crear tabla tags
    $pdo->exec("CREATE TABLE IF NOT EXISTS tags (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL UNIQUE
    )");
    echo "Tabla 'tags' creada/verificada.\n";
    
    // Crear tabla post_tags
    $pdo->exec("CREATE TABLE IF NOT EXISTS post_tags (
        post_id INT NOT NULL,
        tag_id INT NOT NULL,
        PRIMARY KEY (post_id, tag_id),
        FOREIGN KEY (post_id) REFERENCES posts(id) ON DELETE CASCADE,
        FOREIGN KEY (tag_id) REFERENCES tags(id) ON DELETE CASCADE
    )");
    echo "Tabla 'post_tags' creada/verificada.\n";
    
    // Crear tabla followers
    $pdo->exec("CREATE TABLE IF NOT EXISTS followers (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        target_user_id INT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
        FOREIGN KEY (target_user_id) REFERENCES users(id) ON DELETE CASCADE,
        UNIQUE KEY ux_follow (user_id, target_user_id)
    )");
    echo "Tabla 'followers' creada/verificada.\n";
    
    // Añadir columna bio a users si no existe
    $result = $pdo->query("SHOW COLUMNS FROM users LIKE 'bio'");
    if ($result->rowCount() === 0) {
        $pdo->exec("ALTER TABLE users ADD COLUMN bio TEXT NULL AFTER profile_image");
        echo "Columna 'bio' añadida a 'users'.\n";
    } else {
        echo "Columna 'bio' ya existe en 'users'.\n";
    }
    
    // Crear tabla notifications (incluir tipos like/comment)
    $pdo->exec("CREATE TABLE IF NOT EXISTS notifications (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        actor_id INT NOT NULL,
        type ENUM('follow','post','like','comment') DEFAULT 'follow',
        post_id INT DEFAULT NULL,
        is_read BOOLEAN DEFAULT FALSE,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
        FOREIGN KEY (actor_id) REFERENCES users(id) ON DELETE CASCADE,
        FOREIGN KEY (post_id) REFERENCES posts(id) ON DELETE CASCADE
    )");
    echo "Tabla 'notifications' creada/verificada.\n";

    // Asegurar que la columna type de notifications incluya los nuevos valores enum
    try {
        $pdo->exec("ALTER TABLE notifications MODIFY COLUMN type ENUM('follow','post','like','comment') DEFAULT 'follow'");
        echo "Columna 'type' de notifications actualizada con nuevos tipos.\n";
    } catch (Exception $e) {
        echo "No se pudo actualizar la columna 'type' de notifications: " . $e->getMessage() . "\n";
    }
    
    echo "\nMigración completada exitosamente.\n";
    
} catch (PDOException $e) {
    echo "Error de migración: " . $e->getMessage() . "\n";
    exit(1);
}
