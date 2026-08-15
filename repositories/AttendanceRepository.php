<?php
class AttendanceRepository extends BaseRepository {
    
    public function checkIn($registrationId, $checkedInBy, $note = '') {
        $stmt = $this->db->prepare("INSERT INTO attendance (registration_id, checked_in_by, note) VALUES (:registration_id, :checked_in_by, :note)");
        $stmt->execute([
            'registration_id' => $registrationId,
            'checked_in_by'   => $checkedInBy,
            'note'            => $note
        ]);
        
        // Cập nhật trạng thái registration thành attended
        $stmt2 = $this->db->prepare("UPDATE registrations SET status = 'attended' WHERE id = :id");
        $stmt2->execute(['id' => $registrationId]);
        
        return $this->db->lastInsertId();
    }

    public function getAttendanceList($eventId) {
        $stmt = $this->db->prepare("
            SELECT a.*, r.user_id as attendee_id, u.full_name as attendee_name, c.full_name as checker_name, e.title as event_title
            FROM attendance a
            JOIN registrations r ON r.id = a.registration_id
            JOIN users u ON u.id = r.user_id
            JOIN users c ON c.id = a.checked_in_by
            JOIN events e ON e.id = r.event_id
            WHERE r.event_id = :event_id
            ORDER BY a.checked_in_at DESC
        ");
        $stmt->execute(['event_id' => $eventId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getAllAttendanceList() {
        $stmt = $this->db->prepare("
            SELECT a.*, r.user_id as attendee_id, u.full_name as attendee_name, c.full_name as checker_name, e.title as event_title
            FROM attendance a
            JOIN registrations r ON r.id = a.registration_id
            JOIN users u ON u.id = r.user_id
            JOIN users c ON c.id = a.checked_in_by
            JOIN events e ON e.id = r.event_id
            ORDER BY a.checked_in_at DESC
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
        $sql = "UPDATE attendance SET " . implode(', ', $fields) . " WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->rowCount();
    }

    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM attendance WHERE id = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->rowCount();
    }
}
