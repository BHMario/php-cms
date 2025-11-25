<?php

class HomeController
{
    public function index()
    {
        // Mostrar todos los posts en la página principal
        require_once __DIR__ . '/../Models/Post.php';
        $postModel = new Post();
        // Si hay búsqueda, usar search; si no, obtener todos
        $q = trim($_GET['q'] ?? '');
        if ($q !== '') {
            try {
                $posts = $postModel->search($q);
            } catch (Exception $e) {
                $posts = [];
            }
        } else {
            $posts = $postModel->getAll();
        }
        // Contenedores de likes y comentarios
        require_once __DIR__ . '/../Models/Like.php';
        require_once __DIR__ . '/../Models/Comment.php';
        $likeModel = new Like();
        $commentModel = new Comment();
        if (is_array($posts)) {
            foreach ($posts as &$p) {
                $p['like_count'] = $likeModel->countForPost($p['id']);
                $p['comment_count'] = $commentModel->countForPost($p['id']);
            }
            unset($p);
        } else {
            $posts = [];
        }
        require __DIR__ . '/../Views/home/index.php';
    }
}
