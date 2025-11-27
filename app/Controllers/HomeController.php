<?php

class HomeController
{
    public function index()
    {
        // Si es administrador, redirigir al dashboard
        if (isset($_SESSION['user_id'])) {
            require_once __DIR__ . '/../Models/User.php';
            $userModel = new User();
            $user = $userModel->getById($_SESSION['user_id']);
            if ($user && isset($user['role']) && $user['role'] === 'admin') {
                header("Location: /admin");
                exit;
            }
        }
        
        // Mostrar todos los posts en la página principal
        require_once __DIR__ . '/../Models/Post.php';
        $postModel = new Post();
        
        // Obtener parámetros de filtro
        $q = trim($_GET['q'] ?? '');
        $sort = $_GET['sort'] ?? 'recent';
        $tagSearch = trim($_GET['tag_search'] ?? '');
        $categories = isset($_GET['categories']) ? (is_array($_GET['categories']) ? $_GET['categories'] : [$_GET['categories']]) : [];
        
        // Sanitizar categorías
        $categories = array_filter(array_map(function($c) { return intval($c); }, $categories));
        
        // Construir SQL con filtros y orden
        $where = [];
        $params = [];
        
        if (!empty($categories)) {
            $placeholders = implode(',', array_fill(0, count($categories), '?'));
            $where[] = "posts.category_id IN ($placeholders)";
            $params = array_merge($params, $categories);
        }
        
        if ($q !== '') {
            $where[] = "(posts.title LIKE ? OR posts.content LIKE ?)";
            $params[] = "%$q%";
            $params[] = "%$q%";
        }
        
        // Filtrar por tags si hay búsqueda de etiquetas
        if ($tagSearch !== '') {
            $where[] = "(posts.id IN (SELECT post_id FROM post_tags JOIN tags ON post_tags.tag_id = tags.id WHERE tags.name LIKE ?))";
            $params[] = "%$tagSearch%";
        }
        
        // Determinar ORDER BY basado en sort
        $orderBy = 'posts.created_at DESC';
        if ($sort === 'likes') {
            $orderBy = 'like_count DESC, posts.created_at DESC';
        }
        
        // Ejecutar consulta
        $posts = $this->getPostsWithFilters($where, $params, $orderBy);
        
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
    
    private function getPostsWithFilters($where, $params, $orderBy)
    {
        require_once __DIR__ . '/../Models/Database.php';
        $db = new Database();
        
        $query = "SELECT posts.id, posts.title, posts.content, posts.created_at, posts.image, posts.category_id,
                         users.id as user_id, users.username, users.profile_image,
                         (SELECT COUNT(*) FROM likes WHERE likes.post_id = posts.id) as like_count,
                         (SELECT COUNT(*) FROM comments WHERE comments.post_id = posts.id) as comment_count
                  FROM posts
                  JOIN users ON posts.user_id = users.id";
        
        if (!empty($where)) {
            $query .= " WHERE " . implode(" AND ", $where);
        }
        
        $query .= " ORDER BY $orderBy";
        
        try {
            $stmt = $db->getConnection()->prepare($query);
            $stmt->execute($params);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            return [];
        }
    }
}
