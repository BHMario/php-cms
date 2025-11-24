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

    // Alias para compatibilidad: getByUsername
    public function getByUsername($username)
    {
        return $this->db->fetch("SELECT * FROM users WHERE username = ?", [$username]);
    }

    // Alias para compatibilidad: create
    public function create($username, $password, $role = 'user')
    {
        return $this->register($username, $password, $role);
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

    // Cambiar contraseña
    public function changePassword($newPassword)
    {
        $hash = password_hash($newPassword, PASSWORD_DEFAULT);
        $this->db->query("UPDATE users SET password = ? WHERE id = ?", [$hash, $this->id]);
        $this->password = $hash;
    }

    // Cambiar contraseña por id (útil desde controladores)
    public function updatePasswordById($id, $newPassword)
    {
        $hash = password_hash($newPassword, PASSWORD_DEFAULT);
        $this->db->query("UPDATE users SET password = ? WHERE id = ?", [$hash, $id]);
    }

    // Update bio for a user, create column if missing
    public function updateBioById($id, $bio)
    {
        try {
            $this->db->query("UPDATE users SET bio = ? WHERE id = ?", [$bio, $id]);
        } catch (PDOException $e) {
            $msg = $e->getMessage();
            if (strpos($msg, 'Unknown column') !== false || strpos($msg, '1054') !== false) {
                $this->db->query("ALTER TABLE users ADD COLUMN bio TEXT NULL AFTER profile_image");
                $this->db->query("UPDATE users SET bio = ? WHERE id = ?", [$bio, $id]);
            } else {
                throw $e;
            }
        }
    }

    // Actualizar imagen de perfil
    public function updateProfileImage($id, $filename)
    {
        try {
            $this->db->query("UPDATE users SET profile_image = ? WHERE id = ?", [$filename, $id]);
        } catch (PDOException $e) {
            // Si la columna no existe (ej. en DB antigua), intentamos crearla y reintentar
            $msg = $e->getMessage();
            if (strpos($msg, 'Unknown column') !== false || strpos($msg, '1054') !== false) {
                // Añadir columna profile_image y reintentar
                $this->db->query("ALTER TABLE users ADD COLUMN profile_image VARCHAR(255) NULL AFTER password");
                $this->db->query("UPDATE users SET profile_image = ? WHERE id = ?", [$filename, $id]);
            } else {
                throw $e;
            }
        }
    }

    // Obtener todos los usuarios
    public function getAll()
    {
        return $this->db->fetchAll("SELECT id, username, role FROM users ORDER BY username ASC");
    }

    // Obtener usuario por ID
    public function getById($id)
    {
        try {
            return $this->db->fetch("SELECT id, username, role, profile_image FROM users WHERE id = ?", [$id]);
        } catch (PDOException $e) {
            // Si la columna profile_image no existe en la base de datos actual,
            // hacemos una consulta alternativa sin ese campo para evitar fallos.
            return $this->db->fetch("SELECT id, username, role FROM users WHERE id = ?", [$id]);
        }
    }

    // Eliminar usuario
    public function delete($id)
    {
        $this->db->query("DELETE FROM users WHERE id = ?", [$id]);
    }
}
