<?php
class RegistrationRepository extends BaseRepository {
    public function findByEventAndUser($eventId, $userId) {
        $stmt = $this->db->prepare("SELECT * FROM registrations WHERE event_id=:event_id AND user_id=:user_id LIMIT 1");
        $stmt->execute(['event_id'=>$eventId,'user_id'=>$userId]);
        $row = $stmt->fetch();
        return $row ? new Registration($row) : null;
    }

    public function create($eventId, $userId) {
        $stmt = $this->db->prepare("INSERT INTO registrations (event_id,user_id,status) VALUES (:event_id,:user_id,'registered')");
        $stmt->execute(['event_id'=>$eventId,'user_id'=>$userId]);
        return $this->db->lastInsertId();
    }

    public function cancel($id) {
        $stmt = $this->db->prepare("UPDATE registrations SET status='cancelled',cancelled_at=NOW() WHERE id=:id");
        $stmt->execute(['id'=>$id]);
        return $stmt->rowCount();
    }

    public function getByEvent($eventId) {
        $stmt = $this->db->prepare("SELECT r.*,u.full_name,u.email,u.phone FROM registrations r JOIN users u ON u.id=r.user_id WHERE r.event_id=:event_id ORDER BY r.registered_at");
        $stmt->execute(['event_id'=>$eventId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getMyRegistrations($userId) {
        $stmt = $this->db->prepare("SELECT r.id as registration_id,r.status as registration_status,r.registered_at,r.cancelled_at,e.id as event_id,e.title,e.start_time,e.end_time,e.location FROM registrations r JOIN events e ON e.id=r.event_id WHERE r.user_id=:user_id ORDER BY r.registered_at DESC");
        $stmt->execute(['user_id'=>$userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getAllRegistrations() {
        $stmt = $this->db->prepare("
            SELECT r.*, u.full_name as member_name, e.title as event_title 
            FROM registrations r 
            JOIN users u ON u.id=r.user_id 
            JOIN events e ON e.id=r.event_id 
            ORDER BY r.registered_at DESC
        ");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function update($id, $data) {
        $fields = [];
        $params = ['id' => $id];
        foreach ($data as $k => $v) {
            $fields[] = "$k = :$k";
            $params[$k] = $v;
        }
        if (empty($fields)) return 0;
        $sql = "UPDATE registrations SET " . implode(', ', $fields) . " WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->rowCount();
    }

    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM registrations WHERE id = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->rowCount();
    }
}
