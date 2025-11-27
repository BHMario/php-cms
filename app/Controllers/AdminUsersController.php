<?php

require_once __DIR__ . '/../Models/User.php';
require_once __DIR__ . '/AdminController.php';

class AdminUsersController extends AdminController
{
    public function index()
    {
        $user = new User();
        $users = $user->getAll();
        require __DIR__ . '/../Views/admin/users/index.php';
    }

    public function create()
    {
        require __DIR__ . '/../Views/admin/users/create.php';
    }

    public function store()
    {
        $username = $_POST['username'] ?? '';
        $password = $_POST['password'] ?? '';
        $role = $_POST['role'] ?? 'user';
        $user = new User();
        $user->register($username, $password, $role);
        header('Location: /admin/users');
        exit;
    }

    public function edit($id)
    {
        $user = new User();
        $u = $user->getById($id);
        require __DIR__ . '/../Views/admin/users/edit.php';
    }

    public function update($id)
    {
        // Only change role or password
        $role = $_POST['role'] ?? null;
        $password = $_POST['password'] ?? null;
        $user = new User();
        if ($password) {
            $user->updatePasswordById($id, $password);
        }
        if ($role) {
            $db = new \Database();
            $db->query('UPDATE users SET role = ? WHERE id = ?', [$role, $id]);
        }
        header('Location: /admin/users');
        exit;
    }

    public function delete($id)
    {
        $user = new User();
        $user->delete($id);
        header('Location: /admin/users');
        exit;
    }
}
