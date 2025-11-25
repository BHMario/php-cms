<?php
require_once __DIR__ . '/Database.php';
require_once __DIR__ . '/Notification.php';

class Follower
{
    private $db;
    private $notification;

    public function __construct()
    {
        $this->db = new Database();
        $this->notification = new Notification($this->db->getConnection());
    }

    public function follow($user_id, $target_user_id)
    {
        try {
            $exists = $this->db->fetch("SELECT id FROM followers WHERE user_id = ? AND target_user_id = ?", [$user_id, $target_user_id]);
            if ($exists) return;
            $this->db->query("INSERT INTO followers (user_id, target_user_id) VALUES (?, ?)", [$user_id, $target_user_id]);
            
            // Crear notificación de seguimiento
            $this->notification->create($target_user_id, $user_id, 'follow');
        } catch (PDOException $e) {}
    }

    public function unfollow($user_id, $target_user_id)
    {
        try {
            $this->db->query("DELETE FROM followers WHERE user_id = ? AND target_user_id = ?", [$user_id, $target_user_id]);
        } catch (PDOException $e) {}
    }

    public function isFollowing($user_id, $target_user_id)
    {
        try {
            $row = $this->db->fetch("SELECT id FROM followers WHERE user_id = ? AND target_user_id = ?", [$user_id, $target_user_id]);
            return (bool)$row;
        } catch (PDOException $e) { return false; }
    }

    public function countFollowers($target_user_id)
    {
        try {
            $row = $this->db->fetch("SELECT COUNT(*) as cnt FROM followers WHERE target_user_id = ?", [$target_user_id]);
            return $row ? (int)$row['cnt'] : 0;
        } catch (PDOException $e) { return 0; }
    }

    public function countFollowing($user_id)
    {
        try {
            $row = $this->db->fetch("SELECT COUNT(*) as cnt FROM followers WHERE user_id = ?", [$user_id]);
            return $row ? (int)$row['cnt'] : 0;
        } catch (PDOException $e) { return 0; }
    }
}
