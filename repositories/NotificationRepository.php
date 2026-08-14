<?php
class NotificationRepository extends BaseRepository {
    public function create($data) {
        $stmt = $this->db->prepare("INSERT INTO notifications (club_id, event_id, title, content, created_by) VALUES (:club_id, :event_id, :title, :content, :created_by)");
        $stmt->execute($data);
        return $this->db->lastInsertId();
    }

    public function getForUser($userId) {
        $stmt = $this->db->prepare("SELECT DISTINCT n.* FROM notifications n 
            LEFT JOIN registrations r ON n.event_id = r.event_id AND r.user_id = :user_id 
            LEFT JOIN club_managers cm ON n.club_id = cm.club_id AND cm.user_id = :user_id
            WHERE r.user_id IS NOT NULL OR cm.user_id IS NOT NULL OR (n.club_id IS NULL AND n.event_id IS NULL)
            ORDER BY n.created_at DESC LIMIT 50");
        $stmt->execute(['user_id' => $userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getAll($limit=50) {
        $stmt = $this->db->prepare("SELECT * FROM notifications ORDER BY created_at DESC LIMIT :limit");
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
