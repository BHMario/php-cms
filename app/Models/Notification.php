<?php

/**
 * Notification - Modelo para gestión de notificaciones
 * Maneja creación, lectura y eliminación de notificaciones del sistema
 */
class Notification
{
    private \PDO $db;

    /**
     * Constructor que recibe instancia PDO directa
     * 
     * @param \PDO $database
     */
    public function __construct(\PDO $database)
    {
        $this->db = $database;
    }

    /**
     * Crear una notificación, evitando duplicados en los últimos 10 segundos
     * 
     * @param int $userId ID del usuario que recibe la notificación
     * @param int $actorId ID del usuario que genera la notificación
     * @param string $type Tipo: 'follow', 'like', 'comment'
     * @param int|null $postId ID del post relacionado (opcional)
     * @return bool true si se creó, false si ya existe o error
     */
    public function create(int $userId, int $actorId, string $type = 'follow', ?int $postId = null): bool
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
     * Obtener notificaciones de un usuario con límite
     * 
     * @param int $userId
     * @param int $limit
     * @return array
     */
    public function getByUserId(int $userId, int $limit = 20): array
    {
        try {
            $limit = max(1, (int)$limit);
            $query = "SELECT 
                        n.id,
                        n.type,
                        n.is_read,
                        n.created_at,
                        u.username as actor_username,
                        u.profile_image as actor_profile_image,
                        p.title as post_title,
                        p.slug as slug,
                        p.id as post_id
                      FROM notifications n
                      JOIN users u ON n.actor_id = u.id
                      LEFT JOIN posts p ON n.post_id = p.id
                      WHERE n.user_id = ?
                      ORDER BY n.created_at DESC
                      LIMIT " . $limit;
            $stmt = $this->db->prepare($query);
            $stmt->execute([$userId]);
            return $stmt->fetchAll(\PDO::FETCH_ASSOC) ?? [];
        } catch (\Exception $e) {
            error_log("Error fetching notifications: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Contar notificaciones sin leer
     * 
     * @param int $userId
     * @return int
     */
    public function countUnread(int $userId): int
    {
        try {
            $query = "SELECT COUNT(*) as count FROM notifications WHERE user_id = ? AND is_read = FALSE";
            $stmt = $this->db->prepare($query);
            $stmt->execute([$userId]);
            $result = $stmt->fetch(\PDO::FETCH_ASSOC);
            return (int)($result['count'] ?? 0);
        } catch (\Exception $e) {
            return 0;
        }
    }

    /**
     * Marcar una notificación como leída
     * 
     * @param int $notificationId
     * @return bool
     */
    public function markAsRead(int $notificationId): bool
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
     * 
     * @param int $userId
     * @return bool
     */
    public function markAllAsRead(int $userId): bool
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
     * 
     * @param int $notificationId
     * @return bool
     */
    public function delete(int $notificationId): bool
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
     * 
     * @param int $userId
     * @param int $actorId
     * @return bool
     */
    public function followNotificationExists(int $userId, int $actorId): bool
    {
        try {
            $query = "SELECT COUNT(*) as count FROM notifications 
                      WHERE user_id = ? AND actor_id = ? AND type = 'follow'";
            $stmt = $this->db->prepare($query);
            $stmt->execute([$userId, $actorId]);
            $result = $stmt->fetch(\PDO::FETCH_ASSOC);
            return (int)($result['count'] ?? 0) > 0;
        } catch (\Exception $e) {
            return false;
        }
    }
}
