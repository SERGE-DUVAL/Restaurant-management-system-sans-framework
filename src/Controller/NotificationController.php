<?php
namespace src\Controller;

use src\Config\Database;
use src\Model\Notification;
use PDO;

class NotificationController {

    // Lister toutes les notifications
    public function index() {
        $db = Database::getConnection();
        $stmt = $db->query("SELECT id, user_id, message, lu, created_at FROM notifications ORDER BY created_at DESC");
        $notifications = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($notifications);
    }

    // Créer une notification
    public function store($data) {
        $db = Database::getConnection();
        $notification = new Notification(
            null,
            $data['user_id'],
            $data['message'],
            0, // non lu par défaut
            date('Y-m-d H:i:s')
        );

        $stmt = $db->prepare("
            INSERT INTO notifications(user_id, message, lu, created_at)
            VALUES (:user_id, :message, :lu, :created_at)
        ");
        $stmt->execute([
            'user_id' => $notification->getUserId(),
            'message' => $notification->getMessage(),
            'lu' => $notification->isLu() ? 1 : 0,
            'created_at' => $notification->getCreatedAt()
        ]);

        echo json_encode(['success' => 'Notification créée']);
    }

    // Marquer une notification comme lue
    public function markAsRead($id) {
        $db = Database::getConnection();
        $stmt = $db->prepare("UPDATE notifications SET lu = 1 WHERE id = :id");
        $stmt->execute(['id' => $id]);
        echo json_encode(['success' => 'Notification marquée comme lue']);
    }

    // Envoyer une notification (exemple pour une notification ciblée)
    public function send($data) {
        // Ici on pourrait récupérer tous les utilisateurs ciblés ou un seul user_id
        $this->store($data);
    }
}
