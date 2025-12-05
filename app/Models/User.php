<?php

require_once __DIR__ . '/Database.php';
require_once __DIR__ . '/BaseModel.php';

class User extends BaseModel
{
    private ?int $id = null;
    private ?string $username = null;
    private ?string $password = null;
    private ?string $role = null;
    private ?string $profile_image = null;
    private ?string $bio = null;


    // Alias para compatibilidad: getByUsername
    public function getByUsername(string $username): ?array
    {
        $this->validateNotEmpty($username, 'username');
        return $this->db->fetch("SELECT * FROM users WHERE username = ?", [$username]);
    }

    // Alias para compatibilidad: create
    public function create(string $username, string $password, string $role = 'user'): self
    {
        return $this->register($username, $password, $role);
    }

    // Registrar nuevo usuario
    public function register(string $username, string $password, string $role = 'user'): self
    {
        $this->validateNotEmpty($username, 'username');
        $this->validateNotEmpty($password, 'password');
        
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $this->db->query(
            "INSERT INTO users (username, password, role) VALUES (?, ?, ?)",
            [$username, $hash, $role]
        );
        $this->id = (int)$this->db->lastInsertId();
        $this->username = $username;
        $this->role = $role;
        return $this;
    }

    // Autenticar usuario
    public function login(string $username, string $password): bool
    {
        $this->validateNotEmpty($username, 'username');
        $this->validateNotEmpty($password, 'password');
        
        $user = $this->db->fetch("SELECT * FROM users WHERE username = ?", [$username]);
        if ($user && password_verify($password, $user['password'])) {
            $this->id = (int)$user['id'];
            $this->username = $user['username'];
            $this->role = $user['role'];
            return true;
        }
        return false;
    }

    // Cambiar contraseña
    public function changePassword(string $newPassword): void
    {
        $this->validateNotEmpty($newPassword, 'newPassword');
        if ($this->id === null) {
            throw new RuntimeException('No hay usuario cargado');
        }
        $hash = password_hash($newPassword, PASSWORD_DEFAULT);
        $this->db->query("UPDATE users SET password = ? WHERE id = ?", [$hash, $this->id]);
        $this->password = $hash;
    }

    // Cambiar contraseña por id (útil desde controladores)
    public function updatePasswordById(int $id, string $newPassword): void
    {
        $this->validateId($id);
        $this->validateNotEmpty($newPassword, 'newPassword');
        
        $hash = password_hash($newPassword, PASSWORD_DEFAULT);
        $this->db->query("UPDATE users SET password = ? WHERE id = ?", [$hash, $id]);
    }

    // Update bio for a user, create column if missing
    public function updateBioById(int $id, string $bio): void
    {
        $this->validateId($id);
        
        try {
            $this->db->query("UPDATE users SET bio = ? WHERE id = ?", [$bio, $id]);
        } catch (PDOException $e) {
            if ($this->isMissingColumnError($e)) {
                $this->db->query("ALTER TABLE users ADD COLUMN bio TEXT NULL AFTER profile_image");
                $this->db->query("UPDATE users SET bio = ? WHERE id = ?", [$bio, $id]);
            } else {
                throw $e;
            }
        }
    }

    // Actualizar imagen de perfil
    public function updateProfileImage(int $id, string $filename): void
    {
        $this->validateId($id);
        $this->validateNotEmpty($filename, 'filename');
        
        try {
            $this->db->query("UPDATE users SET profile_image = ? WHERE id = ?", [$filename, $id]);
            // Mantener estado del objeto si corresponde
            if ($this->id == $id) {
                $this->profile_image = $filename;
            }
        } catch (PDOException $e) {
            if ($this->isMissingColumnError($e)) {
                $this->db->query("ALTER TABLE users ADD COLUMN profile_image VARCHAR(255) NULL AFTER password");
                $this->db->query("UPDATE users SET profile_image = ? WHERE id = ?", [$filename, $id]);
                if ($this->id == $id) {
                    $this->profile_image = $filename;
                }
            } else {
                throw $e;
            }
        }
    }

    // Obtener todos los usuarios
    public function getAll(): array
    {
        return $this->db->fetchAll("SELECT id, username, role FROM users ORDER BY username ASC") ?? [];
    }

    // Obtener usuario por ID
    public function getById(int $id): ?array
    {
        $this->validateId($id);
        
        try {
            return $this->db->fetch("SELECT id, username, role, profile_image FROM users WHERE id = ?", [$id]);
        } catch (PDOException $e) {
            if ($this->isMissingColumnError($e)) {
                return $this->db->fetch("SELECT id, username, role FROM users WHERE id = ?", [$id]);
            }
            throw $e;
        }
    }

    // Eliminar usuario
    public function delete(int $id): void
    {
        $this->validateId($id);
        $this->db->query("DELETE FROM users WHERE id = ?", [$id]);
    }

    // ============ GETTERS ============
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function getRole(): ?string
    {
        return $this->role;
    }

    public function getProfileImage(): ?string
    {
        return $this->profile_image;
    }

    public function getBio(): ?string
    {
        return $this->bio;
    }

    // ============ SETTERS ============
    public function setUsername(string $username): self
    {
        $this->validateNotEmpty($username, 'username');
        $this->username = $username;
        return $this;
    }

    public function setRole(string $role): self
    {
        $this->validateNotEmpty($role, 'role');
        $this->role = $role;
        return $this;
    }

    public function setBio(string $bio): self
    {
        $this->bio = $bio;
        return $this;
    }

    // Convenience: return current object data as array (keeps backward compatibility with controllers that expect arrays)
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'username' => $this->username,
            'role' => $this->role,
            'profile_image' => $this->profile_image ?? null,
            'bio' => $this->bio ?? null,
        ];
    }
}
