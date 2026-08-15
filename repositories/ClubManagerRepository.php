<?php
class ClubManagerRepository extends BaseRepository {
    
    public function assign($clubId, $userId) {
        $stmt = $this->db->prepare("INSERT IGNORE INTO club_managers (club_id, user_id) VALUES (:club_id, :user_id)");
        $stmt->execute(['club_id' => $clubId, 'user_id' => $userId]);
        return $this->db->lastInsertId();
    }

    public function revoke($clubId, $userId) {
        $stmt = $this->db->prepare("DELETE FROM club_managers WHERE club_id = :club_id AND user_id = :user_id");
        $stmt->execute(['club_id' => $clubId, 'user_id' => $userId]);
        return $stmt->rowCount();
    }

    public function findByClub($clubId) {
        $stmt = $this->db->prepare("
            SELECT cm.*, u.full_name, u.email 
            FROM club_managers cm 
            JOIN users u ON u.id = cm.user_id 
            WHERE cm.club_id = :club_id
        ");
        $stmt->execute(['club_id' => $clubId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function findByUser($userId) {
        $stmt = $this->db->prepare("
            SELECT cm.*, c.name as club_name 
            FROM club_managers cm 
            JOIN clubs c ON c.id = cm.club_id 
            WHERE cm.user_id = :user_id
        ");
        $stmt->execute(['user_id' => $userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
