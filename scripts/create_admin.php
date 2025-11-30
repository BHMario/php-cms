
<?php
// Script para crear un usuario admin si no existe.
require_once __DIR__ . '/../app/Models/User.php';

// Permite crear admin por defecto o personalizado
if (PHP_SAPI === 'cli' && $argc >= 3) {
    $username = $argv[1];
    $password = $argv[2];
    $role = 'admin';
    $userModel = new User();
    $existing = $userModel->getByUsername($username);
    if ($existing) {
        echo "Usuario '$username' ya existe (id: " . ($existing['id'] ?? '?') . ").\n";
        exit(0);
    }
    $userModel->register($username, $password, $role);
    echo "Usuario admin creado: $username\n";
    echo "Contraseña: $password\n";
    echo "Por seguridad, cambia esta contraseña después de iniciar sesión.\n";
    exit(0);
}

// Si no se pasan argumentos, crea admin:admin123 por defecto
$username = 'admin';
$password = 'admin123';
$role = 'admin';

$userModel = new User();
$existing = $userModel->getByUsername($username);
if ($existing) {
    echo "Usuario 'admin' ya existe (id: " . ($existing['id'] ?? '?') . ").\n";
    exit(0);
}

$userModel->register($username, $password, $role);
echo "Usuario admin creado: $username\n";
echo "Contraseña: $password\n";
echo "Por seguridad, cambia esta contraseña después de iniciar sesión.\n";
