<?php
// Iniciar sesiones (necesario para login/registro y autorización)
if (session_status() === PHP_SESSION_NONE) {
	session_start();
}

require_once __DIR__ . '/../app/Router.php';

// Capturar la URL solicitada
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Crear el router y procesar la URL
$router = new Router();
$router->route($uri);
