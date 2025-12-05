<?php

require_once __DIR__ . '/Database.php';
require_once __DIR__ . '/BaseModel.php';
require_once __DIR__ . '/Notification.php';

class Follower extends BaseModel
{
    private Notification $notification;

    public function __construct(?Database $db = null)
    {
        parent::__construct($db);
        $this->notification = new Notification($this->db->getConnection());
    }

    public function follow(int $user_id, int $target_user_id): void
    {
        $this->validateId($user_id);
        $this->validateId($target_user_id);
        
        if ($user_id === $target_user_id) {
            throw new InvalidArgumentException('Un usuario no puede seguirse a sí mismo');
        }

        try {
            $exists = $this->db->fetch("SELECT id FROM followers WHERE user_id = ? AND target_user_id = ?", [$user_id, $target_user_id]);
            if ($exists) return;
            
            $this->db->query("INSERT INTO followers (user_id, target_user_id) VALUES (?, ?)", [$user_id, $target_user_id]);
            
            // Crear notificación de seguimiento
            $this->notification->create($target_user_id, $user_id, 'follow');
        } catch (PDOException $e) {
            throw new RuntimeException('Error al seguir usuario: ' . $e->getMessage());
        }
    }

    public function unfollow(int $user_id, int $target_user_id): void
    {
        $this->validateId($user_id);
        $this->validateId($target_user_id);
        
        try {
            $this->db->query("DELETE FROM followers WHERE user_id = ? AND target_user_id = ?", [$user_id, $target_user_id]);
        } catch (PDOException $e) {
            throw new RuntimeException('Error al dejar de seguir: ' . $e->getMessage());
        }
    }

    public function isFollowing(int $user_id, int $target_user_id): bool
    {
        $this->validateId($user_id);
        $this->validateId($target_user_id);
        
        try {
            $row = $this->db->fetch("SELECT id FROM followers WHERE user_id = ? AND target_user_id = ?", [$user_id, $target_user_id]);
            return (bool)$row;
        } catch (PDOException $e) {
            return false;
        }
    }

    public function countFollowers(int $target_user_id): int
    {
        $this->validateId($target_user_id);
        
        try {
            $row = $this->db->fetch("SELECT COUNT(*) as cnt FROM followers WHERE target_user_id = ?", [$target_user_id]);
            return $row ? (int)$row['cnt'] : 0;
        } catch (PDOException $e) {
            return 0;
        }
    }

    public function countFollowing(int $user_id): int
    {
        $this->validateId($user_id);
        
        try {
            $row = $this->db->fetch("SELECT COUNT(*) as cnt FROM followers WHERE user_id = ?", [$user_id]);
            return $row ? (int)$row['cnt'] : 0;
        } catch (PDOException $e) {
            return 0;
        }
    }
}
