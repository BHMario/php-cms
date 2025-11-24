<?php

class Router
{
    public function route($uri)
    {
        // Limpiar la URL y separar segmentos
        $uri = trim($uri, '/');
        $segments = explode('/', $uri);

        // Ruta principal
        if ($uri === '') {
            require_once __DIR__ . '/Controllers/HomeController.php';
            $controller = new HomeController();
            $controller->index();
            return;
        }

        // Rutas de posts
        if ($segments[0] === 'post' && isset($segments[1])) {
            require_once __DIR__ . '/Controllers/PostController.php';
            $controller = new PostController();
            $controller->show($segments[1]);
            return;
        }

        // Ruta no encontrada
        http_response_code(404);
        echo "Página no encontrada";
    }
}
