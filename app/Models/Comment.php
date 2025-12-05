<?php

require_once __DIR__ . '/Database.php';
require_once __DIR__ . '/BaseModel.php';
require_once __DIR__ . '/Notification.php';

class Comment extends BaseModel
{
    public function create(int $post_id, int $user_id, string $content): int
    {
        $this->validateId($post_id);
        $this->validateId($user_id);
        $this->validateNotEmpty($content, 'content');

        $this->db->query(
            "INSERT INTO comments (post_id, user_id, content) VALUES (?, ?, ?)",
            [$post_id, $user_id, $content]
        );
        
        // Notificar al autor del post
        try {
            $post = $this->db->fetch("SELECT user_id FROM posts WHERE id = ?", [$post_id]);
            if ($post && isset($post['user_id']) && $post['user_id'] != $user_id) {
                $notification = new Notification($this->db->getConnection());
                $notification->create($post['user_id'], $user_id, 'comment', $post_id);
            }
        } catch (Exception $e) {
            // Silenciar errores de notificación
        }

        return (int)$this->db->lastInsertId();
    }

    public function getByPost(int $post_id): array
    {
        $this->validateId($post_id);
        
        return $this->db->fetchAll(
            "SELECT c.*, u.username, u.profile_image FROM comments c JOIN users u ON c.user_id = u.id WHERE c.post_id = ? ORDER BY c.created_at ASC",
            [$post_id]
        ) ?? [];
    }

    public function countForPost(int $post_id): int
    {
        $this->validateId($post_id);
        
        $row = $this->db->fetch("SELECT COUNT(*) as cnt FROM comments WHERE post_id = ?", [$post_id]);
        return $row ? (int)$row['cnt'] : 0;
    }

    public function delete(int $id): void
    {
        $this->validateId($id);
        $this->db->query("DELETE FROM comments WHERE id = ?", [$id]);
    }
}
