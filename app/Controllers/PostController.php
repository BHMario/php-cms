<?php

require_once __DIR__ . '/../Models/Post.php';
require_once __DIR__ . '/../Models/User.php';
require_once __DIR__ . '/../Models/Notification.php';
require_once __DIR__ . '/../Models/Database.php';

class PostController
{
    private $postModel;
    private $userModel;
    private $notificationModel;

    public function __construct()
    {
        $this->postModel = new Post();
        $this->userModel = new User();
        $db = new Database();
        $this->notificationModel = new Notification($db->getConnection());
    }

    public function index()
    {
        // Mostrar solo los posts del usuario autenticado
        if (!isset($_SESSION['user_id'])) {
            // Redirigir a login si no está autenticado
            header('Location: /login');
            exit;
        }

        $posts = $this->postModel->getByUser($_SESSION['user_id']);
        // Contenedores
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
        require __DIR__ . '/../Views/post/index.php';
    }

    public function show($id)
    {
        $post = $this->postModel->getById($id);
        $comments = [];
        $likeCount = 0;
        $userLiked = false;

        require_once __DIR__ . '/../Models/Comment.php';
        require_once __DIR__ . '/../Models/Like.php';
        $commentModel = new Comment();
        $likeModel = new Like();

        if ($post) {
            $comments = $commentModel->getByPost($id);
            $likeCount = $likeModel->countForPost($id);
            if (isset($_SESSION['user_id'])) {
                $userLiked = $likeModel->userLiked($id, $_SESSION['user_id']);
            }
        }

        require __DIR__ . '/../Views/post/show.php';
    }

    public function create()
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }
        require __DIR__ . '/../Views/post/create.php';
    }

    public function store()
    {
        $title = trim($_POST['title']);
        $content = trim($_POST['content']);

        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }

        // Procesar imagen (opcional)
        $imagePath = null;
        if (!empty($_FILES['image']) && $_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE) {
            $file = $_FILES['image'];
            $allowed = ['image/jpeg','image/png','image/gif'];
            if ($file['error'] === UPLOAD_ERR_OK && in_array(mime_content_type($file['tmp_name']), $allowed)) {
                $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
                $newName = 'post_' . $_SESSION['user_id'] . '_' . time() . '.' . $ext;
                $targetDir = __DIR__ . '/../../public/uploads/posts';
                if (!is_dir($targetDir)) mkdir($targetDir, 0755, true);
                $target = $targetDir . '/' . $newName;
                if (move_uploaded_file($file['tmp_name'], $target)) {
                    $imagePath = 'uploads/posts/' . $newName;
                }
            }
        }

        if ($title && $content) {
            $categoryId = !empty($_POST['category_id']) ? $_POST['category_id'] : null;
            $post = $this->postModel->create($title, $content, $_SESSION['user_id'], $categoryId, $imagePath);
            
            // Tags (separados por comas)
            if (!empty($_POST['tags'])) {
                require_once __DIR__ . '/../Models/Tag.php';
                $tagModel = new Tag();
                $raw = explode(',', $_POST['tags']);
                $ids = [];
                foreach ($raw as $r) {
                    $n = trim($r);
                    if ($n === '') continue;
                        $ids[] = $tagModel->getOrCreate($n);
                    }
                    $postId = method_exists($post, 'getId') ? $post->getId() : ($post['id'] ?? null);
                    if ($postId !== null) {
                        $this->postModel->setTags((int)$postId, $ids);
                    }
            }
            
                // Notificar a los seguidores sobre el nuevo post
                $postIdForNotify = method_exists($post, 'getId') ? $post->getId() : ($post['id'] ?? null);
                if ($postIdForNotify !== null) {
                    $this->notifyFollowers($_SESSION['user_id'], (int)$postIdForNotify);
                }
        }

        header("Location: /posts");
        exit;
    }

    // Notificar a todos los followers de un usuario cuando publica un post
    private function notifyFollowers($userId, $postId)
    {
        try {
            $db = new Database();
            // Obtener todos los followers del usuario
            $followers = $db->fetchAll(
                "SELECT user_id FROM followers WHERE target_user_id = ?",
                [$userId]
            );
            
            foreach ($followers as $follower) {
                $this->notificationModel->create(
                    $follower['user_id'],  // quien recibe la notificación
                    $userId,               // quien publicó
                    'post',                // tipo de notificación
                    $postId                // id del post
                );
            }
        } catch (\Exception $e) {
            error_log("Error notifying followers: " . $e->getMessage());
        }
    }

    public function edit($id)
    {
        $post = $this->postModel->getById($id);
        if (!$post) {
            header('Location: /posts');
            exit;
        }

        // Solo el propietario o admin puede editar
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }
        $currentUser = $this->userModel->getById($_SESSION['user_id']);
        if ($post['user_id'] != $_SESSION['user_id'] && $currentUser['role'] !== 'admin') {
            header('Location: /posts');
            exit;
        }

        require __DIR__ . '/../Views/post/edit.php';
    }

    public function update($id)
    {
        $title = trim($_POST['title']);
        $content = trim($_POST['content']);

        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }

        // Asegurar que el post existe y el usuario está autorizado
        $post = $this->postModel->getById($id);
        if ($post && ($post['user_id'] == $_SESSION['user_id'] || $this->userModel->getById($_SESSION['user_id'])['role'] === 'admin')) {
            // Procesar imagen (opcional)
            $imagePath = null;
            if (!empty($_FILES['image']) && $_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE) {
                $file = $_FILES['image'];
                $allowed = ['image/jpeg','image/png','image/gif'];
                if ($file['error'] === UPLOAD_ERR_OK && in_array(mime_content_type($file['tmp_name']), $allowed)) {
                    $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
                    $newName = 'post_' . $_SESSION['user_id'] . '_' . time() . '.' . $ext;
                    $targetDir = __DIR__ . '/../../public/uploads/posts';
                    if (!is_dir($targetDir)) mkdir($targetDir, 0755, true);
                    $target = $targetDir . '/' . $newName;
                    if (move_uploaded_file($file['tmp_name'], $target)) {
                        $imagePath = 'uploads/posts/' . $newName;
                    }
                }
            }

            if ($title && $content) {
                // Si no se sube una nueva imagen, mantener la imagen existente
                if ($imagePath === null) {
                    $imagePath = $post['image'] ?? null;
                }
                $categoryId = !empty($_POST['category_id']) ? $_POST['category_id'] : null;
                $this->postModel->update($id, $title, $content, $categoryId, $imagePath);
                // Etiquetas (separadas por comas)
                if (isset($_POST['tags'])) {
                    require_once __DIR__ . '/../Models/Tag.php';
                    $tagModel = new Tag();
                    $raw = explode(',', $_POST['tags']);
                    $ids = [];
                    foreach ($raw as $r) {
                        $n = trim($r);
                        if ($n === '') continue;
                        $ids[] = $tagModel->getOrCreate($n);
                    }
                    $this->postModel->setTags($id, $ids);
                }
            }
        }

        header("Location: /posts");
        exit;
    }

    public function delete($id)
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }

        $post = $this->postModel->getById($id);
        if ($post && ($post['user_id'] == $_SESSION['user_id'] || $this->userModel->getById($_SESSION['user_id'])['role'] === 'admin')) {
            $this->postModel->delete($id);
        }

        header("Location: /posts");
        exit;
    }

    // Enviar comentario
    public function comment($postId)
    {
        require_once __DIR__ . '/../Models/Comment.php';
        $commentModel = new Comment();

        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }

        $content = trim($_POST['content'] ?? '');
        if ($content) {
            $commentModel->create($postId, $_SESSION['user_id'], $content);
        }

        header("Location: /posts/{$postId}");
        exit;
    }

    // Dar/quitar like
    public function like($postId)
    {
        require_once __DIR__ . '/../Models/Like.php';
        $likeModel = new Like();

        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }

        if ($likeModel->userLiked($postId, $_SESSION['user_id'])) {
            $likeModel->delete($postId, $_SESSION['user_id']);
        } else {
            $likeModel->create($postId, $_SESSION['user_id']);
        }

        header("Location: /posts/{$postId}");
        exit;
    }
}
