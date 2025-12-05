<?php

require_once __DIR__ . '/Database.php';
require_once __DIR__ . '/BaseModel.php';
require_once __DIR__ . '/Notification.php';

class Like extends BaseModel
{
    public function create(int $post_id, int $user_id): ?int
    {
        $this->validateId($post_id);
        $this->validateId($user_id);
        
        if ($this->userLiked($post_id, $user_id)) {
            return null;
        }

        $this->db->query(
            "INSERT INTO likes (post_id, user_id) VALUES (?, ?)",
            [$post_id, $user_id]
        );

        // Crear notificación de like para el autor del post (si no es el mismo usuario)
        try {
            $post = $this->db->fetch("SELECT user_id FROM posts WHERE id = ?", [$post_id]);
            if ($post && isset($post['user_id']) && $post['user_id'] != $user_id) {
                $notification = new Notification($this->db->getConnection());
                $notification->create($post['user_id'], $user_id, 'like', $post_id);
            }
        } catch (Exception $e) {
            // Silenciar errores de notificación
        }

        return (int)$this->db->lastInsertId();
    }

    public function delete(int $post_id, int $user_id): void
    {
        $this->validateId($post_id);
        $this->validateId($user_id);
        
        $this->db->query("DELETE FROM likes WHERE post_id = ? AND user_id = ?", [$post_id, $user_id]);
    }

    public function countForPost(int $post_id): int
    {
        $this->validateId($post_id);
        
        $row = $this->db->fetch("SELECT COUNT(*) as cnt FROM likes WHERE post_id = ?", [$post_id]);
        return $row ? (int)$row['cnt'] : 0;
    }

    public function userLiked(int $post_id, int $user_id): bool
    {
        $this->validateId($post_id);
        $this->validateId($user_id);
        
        $row = $this->db->fetch("SELECT id FROM likes WHERE post_id = ? AND user_id = ?", [$post_id, $user_id]);
        return (bool)$row;
    }
}
