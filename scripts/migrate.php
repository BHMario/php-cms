<?php
/**
 * Migration script to create new tables and columns for the blog CMS.
 * Run: php migrate.php
 */

$config = require __DIR__ . '/../config/config.php';
$dsn = "mysql:host={$config['host']};dbname={$config['dbname']};charset={$config['charset']}";

try {
    $pdo = new PDO($dsn, $config['user'], $config['password'], [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    ]);
    
    echo "Conectado a la base de datos.\n";
    
    // Create tags table
    $pdo->exec("CREATE TABLE IF NOT EXISTS tags (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL UNIQUE
    )");
    echo "✓ Tabla 'tags' creada/verificada.\n";
    
    // Create post_tags table
    $pdo->exec("CREATE TABLE IF NOT EXISTS post_tags (
        post_id INT NOT NULL,
        tag_id INT NOT NULL,
        PRIMARY KEY (post_id, tag_id),
        FOREIGN KEY (post_id) REFERENCES posts(id) ON DELETE CASCADE,
        FOREIGN KEY (tag_id) REFERENCES tags(id) ON DELETE CASCADE
    )");
    echo "✓ Tabla 'post_tags' creada/verificada.\n";
    
    // Create followers table
    $pdo->exec("CREATE TABLE IF NOT EXISTS followers (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        target_user_id INT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
        FOREIGN KEY (target_user_id) REFERENCES users(id) ON DELETE CASCADE,
        UNIQUE KEY ux_follow (user_id, target_user_id)
    )");
    echo "✓ Tabla 'followers' creada/verificada.\n";
    
    // Add bio column to users if not exists
    $result = $pdo->query("SHOW COLUMNS FROM users LIKE 'bio'");
    if ($result->rowCount() === 0) {
        $pdo->exec("ALTER TABLE users ADD COLUMN bio TEXT NULL AFTER profile_image");
        echo "✓ Columna 'bio' añadida a 'users'.\n";
    } else {
        echo "✓ Columna 'bio' ya existe en 'users'.\n";
    }
    
    echo "\n✅ Migración completada exitosamente.\n";
    
} catch (PDOException $e) {
    echo "❌ Error de migración: " . $e->getMessage() . "\n";
    exit(1);
}
