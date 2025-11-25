<?php

class Notification
{
    private $db;

    public function __construct($database)
    {
        $this->db = $database;
    }

    /**
     * Crear una notificación
     */
    public function create($userId, $actorId, $type = 'follow', $postId = null)
    {
        try {
            $params = [$userId, $actorId, $type];
            if ($postId === null) {
                $dupSql = "SELECT COUNT(*) as count FROM notifications WHERE user_id = ? AND actor_id = ? AND type = ? AND post_id IS NULL AND created_at >= (NOW() - INTERVAL 10 SECOND)";
                $stmt = $this->db->prepare($dupSql);
                $stmt->execute($params);
            } else {
                $dupSql = "SELECT COUNT(*) as count FROM notifications WHERE user_id = ? AND actor_id = ? AND type = ? AND post_id = ? AND created_at >= (NOW() - INTERVAL 10 SECOND)";
                $params[] = $postId;
                $stmt = $this->db->prepare($dupSql);
                $stmt->execute($params);
            }

            $res = $stmt->fetch(\PDO::FETCH_ASSOC);
            if ($res && (int)$res['count'] > 0) {
                return false;
            }

            $query = "INSERT INTO notifications (user_id, actor_id, type, post_id) VALUES (?, ?, ?, ?)";
            $stmt = $this->db->prepare($query);
            $stmt->execute([$userId, $actorId, $type, $postId]);
            return true;
        } catch (\Exception $e) {
            error_log("Error creating notification: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Obtener todas las notificaciones de un usuario
     */
    public function getByUserId($userId, $limit = 20)
    {
        try {
            $limit = (int)$limit;
            $query = "SELECT 
                        n.id,
                        n.type,
                        n.is_read,
                        n.created_at,
                        u.username as actor_username,
                        u.profile_image as actor_profile_image,
                        p.title as post_title,
                        p.id as post_id
                      FROM notifications n
                      JOIN users u ON n.actor_id = u.id
                      LEFT JOIN posts p ON n.post_id = p.id
                      WHERE n.user_id = ?
                      ORDER BY n.created_at DESC
                      LIMIT " . $limit;
            $stmt = $this->db->prepare($query);
            $stmt->execute([$userId]);
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\Exception $e) {
            error_log("Error fetching notifications: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Contar notificaciones sin leer de un usuario
     */
    public function countUnread($userId)
    {
        try {
            $query = "SELECT COUNT(*) as count FROM notifications WHERE user_id = ? AND is_read = FALSE";
            $stmt = $this->db->prepare($query);
            $stmt->execute([$userId]);
            $result = $stmt->fetch(\PDO::FETCH_ASSOC);
            return $result['count'] ?? 0;
        } catch (\Exception $e) {
            return 0;
        }
    }

    /**
     * Marcar notificación como leída
     */
    public function markAsRead($notificationId)
    {
        try {
            $query = "UPDATE notifications SET is_read = TRUE WHERE id = ?";
            $stmt = $this->db->prepare($query);
            return $stmt->execute([$notificationId]);
        } catch (\Exception $e) {
            error_log("Error marking notification as read: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Marcar todas las notificaciones de un usuario como leídas
     */
    public function markAllAsRead($userId)
    {
        try {
            $query = "UPDATE notifications SET is_read = TRUE WHERE user_id = ? AND is_read = FALSE";
            $stmt = $this->db->prepare($query);
            return $stmt->execute([$userId]);
        } catch (\Exception $e) {
            error_log("Error marking all notifications as read: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Eliminar una notificación
     */
    public function delete($notificationId)
    {
        try {
            $query = "DELETE FROM notifications WHERE id = ?";
            $stmt = $this->db->prepare($query);
            return $stmt->execute([$notificationId]);
        } catch (\Exception $e) {
            error_log("Error deleting notification: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Verificar si ya existe una notificación de seguimiento
     */
    public function followNotificationExists($userId, $actorId)
    {
        try {
            $query = "SELECT COUNT(*) as count FROM notifications 
                      WHERE user_id = ? AND actor_id = ? AND type = 'follow'";
            $stmt = $this->db->prepare($query);
            $stmt->execute([$userId, $actorId]);
            $result = $stmt->fetch(\PDO::FETCH_ASSOC);
            return $result['count'] > 0;
        } catch (\Exception $e) {
            return false;
        }
    }
}
?>
