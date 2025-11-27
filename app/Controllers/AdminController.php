<?php

require_once __DIR__ . '/../Models/User.php';

class AdminController
{
    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Acceso restringido a administradores en todas las subclases
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
    }

    public function index()
    {
        // Cargar datos básicos para el dashboard (puede ampliarse)
        require __DIR__ . '/../Views/admin/dashboard.php';
    }
}
