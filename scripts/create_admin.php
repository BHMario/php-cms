<?php
// Usage: php scripts/create_admin.php admin_username admin_password
require_once __DIR__ . '/../app/Models/User.php';

if ($argc < 3) {
    echo "Usage: php create_admin.php username password\n";
    exit(1);
}

$username = $argv[1];
$password = $argv[2];

$userModel = new User();
$exists = $userModel->getByUsername($username);
if ($exists) {
    echo "User '$username' already exists.\n";
    exit(0);
}

$userModel->create($username, $password, 'admin');
echo "Admin user '$username' created.\n";
