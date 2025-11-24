<?php

require_once __DIR__ . '/Database.php';

class User
{
    private $db;
    public $id;
    public $username;
    public $password;
    public $role;

    public function __construct()
    {
        $this->db = new Database();
    }

    // Registrar nuevo usuario
    public function register($username, $password, $role = 'user')
    {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $this->db->query(
            "INSERT INTO users (username, password, role) VALUES (?, ?, ?)",
            [$username, $hash, $role]
        );
        $this->id = $this->db->lastInsertId();
        $this->username = $username;
        $this->role = $role;
        return $this;
    }

    // Autenticar usuario
    public function login($username, $password)
    {
        $user = $this->db->fetch("SELECT * FROM users WHERE username = ?", [$username]);
        if ($user && password_verify($password, $user['password'])) {
            $this->id = $user['id'];
            $this->username = $user['username'];
            $this->role = $user['role'];
            return true;
        }
        return false;
    }

    // Buscar usuario por username
    public function findByUsername($username)
    {
        return $this->db->fetch("SELECT * FROM users WHERE username = ?", [$username]);
    }

    // Cambiar contraseña
    public function changePassword($newPassword)
    {
        $hash = password_hash($newPassword, PASSWORD_DEFAULT);
        $this->db->query("UPDATE users SET password = ? WHERE id = ?", [$hash, $this->id]);
        $this->password = $hash;
    }
}
