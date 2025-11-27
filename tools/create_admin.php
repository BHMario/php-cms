<?php
// Script para crear un usuario admin si no existe.
require_once __DIR__ . '/../app/Models/User.php';

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
