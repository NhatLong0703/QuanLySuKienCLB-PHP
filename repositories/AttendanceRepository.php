<?php
class AttendanceRepository extends BaseRepository {
    public function checkIn($registrationId, $checkedInBy, $note = null) {
        $stmt = $this->db->prepare("INSERT IGNORE INTO attendance (registration_id, checked_in_by, note) VALUES (:reg_id, :by, :note)");
        $stmt->execute(['reg_id'=>$registrationId, 'by'=>$checkedInBy, 'note'=>$note]);
        return $this->db->lastInsertId();
    }

    public function getByEvent($eventId) {
        $stmt = $this->db->prepare("SELECT a.*, r.user_id, u.full_name, u.email FROM attendance a JOIN registrations r ON a.registration_id = r.id JOIN users u ON r.user_id = u.id WHERE r.event_id = :event_id ORDER BY a.checked_in_at DESC");
        $stmt->execute(['event_id' => $eventId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
