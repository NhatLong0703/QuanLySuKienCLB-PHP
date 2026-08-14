<?php
class ClubManagerRepository extends BaseRepository {
    public function assignManager($clubId, $userId) {
        $stmt = $this->db->prepare("INSERT IGNORE INTO club_managers (club_id, user_id) VALUES (:club_id, :user_id)");
        $stmt->execute(['club_id' => $clubId, 'user_id' => $userId]);
        return $this->db->lastInsertId();
    }

    public function removeManager($clubId, $userId) {
        $stmt = $this->db->prepare("DELETE FROM club_managers WHERE club_id = :club_id AND user_id = :user_id");
        $stmt->execute(['club_id' => $clubId, 'user_id' => $userId]);
        return $stmt->rowCount();
    }

    public function getManagersByClub($clubId) {
        $stmt = $this->db->prepare("SELECT cm.*, u.full_name, u.email FROM club_managers cm JOIN users u ON cm.user_id = u.id WHERE cm.club_id = :club_id ORDER BY cm.assigned_at DESC");
        $stmt->execute(['club_id' => $clubId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getClubsByUser($userId) {
        $stmt = $this->db->prepare("SELECT cm.*, c.name as club_name FROM club_managers cm JOIN clubs c ON cm.club_id = c.id WHERE cm.user_id = :user_id ORDER BY cm.assigned_at DESC");
        $stmt->execute(['user_id' => $userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
