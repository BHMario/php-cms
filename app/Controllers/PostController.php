<?php

require_once __DIR__ . '/../Models/Post.php';
require_once __DIR__ . '/../Models/User.php';

class PostController
{
    private $postModel;
    private $userModel;

    public function __construct()
    {
        $this->postModel = new Post();
        $this->userModel = new User();
    }

    public function index()
    {
        // Mostrar solo los posts del usuario autenticado
        if (!isset($_SESSION['user_id'])) {
            // Si no está autenticado, redirigimos a login
            header('Location: /login');
            exit;
        }

        $posts = $this->postModel->getByUser($_SESSION['user_id']);
        // Añadir contadores
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
            // Tags (comma separated)
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
                $this->postModel->setTags($post->id, $ids);
            }
        }

        header("Location: /posts");
        exit;
    }

    public function edit($id)
    {
        $post = $this->postModel->getById($id);
        if (!$post) {
            header('Location: /posts');
            exit;
        }

        // Only owner or admin can edit
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

        // Ensure post exists and user is authorized
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
                // If no new image uploaded, keep existing image
                if ($imagePath === null) {
                    $imagePath = $post['image'] ?? null;
                }
                $categoryId = !empty($_POST['category_id']) ? $_POST['category_id'] : null;
                $this->postModel->update($id, $title, $content, $categoryId, $imagePath);
                // Tags
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

    // Handle comment submission for a post
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

    // Like/unlike endpoints
    public function like($postId)
    {
        require_once __DIR__ . '/../Models/Like.php';
        $likeModel = new Like();

        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }

        // Toggle like
        if ($likeModel->userLiked($postId, $_SESSION['user_id'])) {
            $likeModel->delete($postId, $_SESSION['user_id']);
        } else {
            $likeModel->create($postId, $_SESSION['user_id']);
        }

        header("Location: /posts/{$postId}");
        exit;
    }
}
