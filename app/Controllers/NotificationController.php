<?php

require_once __DIR__ . '/../Models/Notification.php';
require_once __DIR__ . '/../Models/Database.php';

class NotificationController
{
    private $notificationModel;

    public function __construct()
    {
        $db = new Database();
        $this->notificationModel = new Notification($db->getConnection());
    }

    public function inbox()
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }

        $notifications = $this->notificationModel->getByUserId($_SESSION['user_id'], 50);
        $unreadCount = $this->notificationModel->countUnread($_SESSION['user_id']);
        
        // Marcar todas como leídas
        if ($unreadCount > 0) {
            $this->notificationModel->markAllAsRead($_SESSION['user_id']);
        }

        require __DIR__ . '/../Views/notification/inbox.php';
    }

    public function delete($id)
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }

        $this->notificationModel->delete($id);
        header('Location: /notifications');
        exit;
    }

    public function getUnreadCount()
    {
        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['count' => 0]);
            exit;
        }

        $count = $this->notificationModel->countUnread($_SESSION['user_id']);
        header('Content-Type: application/json');
        echo json_encode(['count' => $count]);
        exit;
    }
}
?>
