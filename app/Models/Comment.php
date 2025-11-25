<?php
require_once __DIR__ . '/Database.php';
require_once __DIR__ . '/Notification.php';

class Comment
{
    private $db;

    public function __construct()
    {
        $this->db = new Database();
    }

    public function create($post_id, $user_id, $content)
    {
        $this->db->query(
            "INSERT INTO comments (post_id, user_id, content) VALUES (?, ?, ?)",
            [$post_id, $user_id, $content]
        );
        
        // Crear notificación de comentario para el autor del post (si no es el mismo usuario)
        try {
            $post = $this->db->fetch("SELECT user_id FROM posts WHERE id = ?", [$post_id]);
            if ($post && isset($post['user_id']) && $post['user_id'] != $user_id) {
                $notification = new Notification($this->db->getConnection());
                $notification->create($post['user_id'], $user_id, 'comment', $post_id);
            }
        } catch (Exception $e) {
            
        }

        return $this->db->lastInsertId();
    }

    public function getByPost($post_id)
    {
        return $this->db->fetchAll(
            "SELECT c.*, u.username, u.profile_image FROM comments c JOIN users u ON c.user_id = u.id WHERE c.post_id = ? ORDER BY c.created_at ASC",
            [$post_id]
        );
    }

    public function countForPost($post_id)
    {
        $row = $this->db->fetch("SELECT COUNT(*) as cnt FROM comments WHERE post_id = ?", [$post_id]);
        return $row ? (int)$row['cnt'] : 0;
    }
}
