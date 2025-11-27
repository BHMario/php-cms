<?php

require_once __DIR__ . '/../Models/User.php';

class AdminController
{
    public function index()
    {
        // Acceso restringido a administradores
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }

        $userModel = new User();
        $user = $userModel->getById($_SESSION['user_id']);
        if (!$user || ($user['role'] ?? '') !== 'admin') {
            http_response_code(403);
            echo 'Acceso denegado';
            exit;
        }

        // Cargar datos básicos para el dashboard (puede ampliarse)
        require __DIR__ . '/../Views/admin/dashboard.php';
    }
}
