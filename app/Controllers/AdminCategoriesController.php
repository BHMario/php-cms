<?php

require_once __DIR__ . '/../Models/Category.php';
require_once __DIR__ . '/AdminController.php';

class AdminCategoriesController extends AdminController
{
    public function index()
    {
        $cat = new Category();
        $categories = $cat->getAll();
        require __DIR__ . '/../Views/admin/categories/index.php';
    }

    public function create()
    {
        require __DIR__ . '/../Views/admin/categories/create.php';
    }

    public function store()
    {
        $name = $_POST['name'] ?? '';
        $cat = new Category();
        $cat->create($name);
        header('Location: /admin/categories');
        exit;
    }

    public function edit($id)
    {
        $cat = new Category();
        $category = $cat->getById($id);
        require __DIR__ . '/../Views/admin/categories/edit.php';
    }

    public function update($id)
    {
        $name = $_POST['name'] ?? '';
        $db = new \Database();
        $db->query('UPDATE categories SET name = ? WHERE id = ?', [$name, $id]);
        header('Location: /admin/categories');
        exit;
    }

    public function delete($id)
    {
        $db = new \Database();
        $db->query('DELETE FROM categories WHERE id = ?', [$id]);
        header('Location: /admin/categories');
        exit;
    }
}
