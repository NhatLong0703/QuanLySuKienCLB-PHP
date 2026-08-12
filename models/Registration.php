<?php
class Registration extends BaseModel {
    protected $table = 'registrations';

    // Xử lý đăng ký tham gia
    public function register($eventId, $userId) {
        // Kiểm tra xem đã từng đăng ký và huỷ chưa
        $sqlCheck = "SELECT id, status FROM {$this->table} WHERE event_id = :event_id AND user_id = :user_id";
        $stmtCheck = $this->db->prepare($sqlCheck);
        $stmtCheck->execute(['event_id' => $eventId, 'user_id' => $userId]);
        $existing = $stmtCheck->fetch();

        if ($existing) {
            if ($existing['status'] === 'registered') {
                throw new Exception("Bạn đã đăng ký sự kiện này rồi.");
            }
            // Nếu đã huỷ, chuyển status lại thành registered
            $sql = "UPDATE {$this->table} SET status = 'registered', cancelled_at = NULL WHERE id = :id";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute(['id' => $existing['id']]);
        }

        // Thêm mới
        $sql = "INSERT INTO {$this->table} (event_id, user_id, status) VALUES (:event_id, :user_id, 'registered')";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'event_id' => $eventId,
            'user_id' => $userId
        ]);
    }

    // Xử lý huỷ đăng ký
    public function cancel($eventId, $userId) {
        // Cấp ứng dụng kiểm tra hạn chót
        $sqlEvent = "SELECT registration_deadline FROM events WHERE id = :id";
        $stmtE = $this->db->prepare($sqlEvent);
        $stmtE->execute(['id' => $eventId]);
        $event = $stmtE->fetch();

        if (!$event) {
             throw new Exception("Không tìm thấy sự kiện.");
        }
        if (strtotime($event['registration_deadline']) < time()) {
             throw new Exception("Đã qua hạn huỷ đăng ký.");
        }

        $sql = "UPDATE {$this->table} SET status = 'cancelled', cancelled_at = NOW() 
                WHERE event_id = :event_id AND user_id = :user_id AND status = 'registered'";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'event_id' => $eventId,
            'user_id' => $userId
        ]);
        
        if ($stmt->rowCount() === 0) {
             throw new Exception("Bạn chưa đăng ký hoặc không thể huỷ.");
        }
        return true;
    }
}
