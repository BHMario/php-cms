<?php
require_once __DIR__ . '/Database.php';

class Like
{
    private $db;

    public function __construct()
    {
        $this->db = new Database();
    }

    public function create($post_id, $user_id)
    {
        // Avoid duplicate likes
        if ($this->userLiked($post_id, $user_id)) return null;
        $this->db->query(
            "INSERT INTO likes (post_id, user_id) VALUES (?, ?)",
            [$post_id, $user_id]
        );
        return $this->db->lastInsertId();
    }

    public function delete($post_id, $user_id)
    {
        $this->db->query("DELETE FROM likes WHERE post_id = ? AND user_id = ?", [$post_id, $user_id]);
    }

    public function countForPost($post_id)
    {
        $row = $this->db->fetch("SELECT COUNT(*) as cnt FROM likes WHERE post_id = ?", [$post_id]);
        return $row ? (int)$row['cnt'] : 0;
    }

    public function userLiked($post_id, $user_id)
    {
        $row = $this->db->fetch("SELECT id FROM likes WHERE post_id = ? AND user_id = ?", [$post_id, $user_id]);
        return (bool)$row;
    }
}
