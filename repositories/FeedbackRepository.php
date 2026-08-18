<?php
class FeedbackRepository extends BaseRepository {
    
    public function create($eventId, $userId, $rating, $comment) {
        $stmt = $this->db->prepare("
            INSERT INTO event_feedbacks (event_id, user_id, rating, comment)
            VALUES (:event_id, :user_id, :rating, :comment)
        ");
        $stmt->execute([
            'event_id' => $eventId,
            'user_id'  => $userId,
            'rating'   => $rating,
            'comment'  => $comment
        ]);
        return $this->db->lastInsertId();
    }

    public function getByEvent($eventId) {
        $stmt = $this->db->prepare("
            SELECT f.*, u.full_name as user_name 
            FROM event_feedbacks f
            JOIN users u ON u.id = f.user_id
            WHERE f.event_id = :event_id
            ORDER BY f.created_at DESC
        ");
        $stmt->execute(['event_id' => $eventId]);
        return $stmt->fetchAll();
    }

    public function findByUserAndEvent($userId, $eventId) {
        $stmt = $this->db->prepare("
            SELECT * FROM event_feedbacks 
            WHERE user_id = :user_id AND event_id = :event_id
        ");
        $stmt->execute(['user_id' => $userId, 'event_id' => $eventId]);
        return $stmt->fetch();
    }
    
    public function getEventStats($eventId) {
        $stmt = $this->db->prepare("
            SELECT COUNT(id) as total_feedbacks, AVG(rating) as avg_rating 
            FROM event_feedbacks 
            WHERE event_id = :event_id
        ");
        $stmt->execute(['event_id' => $eventId]);
        return $stmt->fetch();
    }
}
