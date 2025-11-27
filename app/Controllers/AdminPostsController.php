<?php

require_once __DIR__ . '/../Models/Post.php';
require_once __DIR__ . '/../Models/Category.php';
require_once __DIR__ . '/AdminController.php';

class AdminPostsController extends AdminController
{
    public function index()
    {
        $postModel = new Post();
        $posts = $postModel->getAll();
        require __DIR__ . '/../Views/admin/posts/index.php';
    }

    public function create()
    {
        $categoryModel = new Category();
        $categories = $categoryModel->getAll();
        require __DIR__ . '/../Views/admin/posts/create.php';
    }

    public function store()
    {
        // Basic store implementation
        $title = $_POST['title'] ?? '';
        $content = $_POST['content'] ?? '';
        $category_id = !empty($_POST['category_id']) ? $_POST['category_id'] : null;
        $user_id = $_SESSION['user_id'];

        $postModel = new Post();
        $post = $postModel->create($title, $content, $user_id, $category_id);

        header('Location: /admin/posts');
        exit;
    }

    public function edit($id)
    {
        $postModel = new Post();
        $post = $postModel->getById($id);
        $categoryModel = new Category();
        $categories = $categoryModel->getAll();
        require __DIR__ . '/../Views/admin/posts/edit.php';
    }

    public function update($id)
    {
        $title = $_POST['title'] ?? '';
        $content = $_POST['content'] ?? '';
        $category_id = !empty($_POST['category_id']) ? $_POST['category_id'] : null;

        $postModel = new Post();
        $postModel->update($id, $title, $content, $category_id, null);

        header('Location: /admin/posts');
        exit;
    }

    public function delete($id)
    {
        $postModel = new Post();
        $postModel->delete($id);
        header('Location: /admin/posts');
        exit;
    }

    // Optional: show
    public function show($id)
    {
        $postModel = new Post();
        $post = $postModel->getById($id);
        require __DIR__ . '/../Views/admin/posts/show.php';
    }
}
