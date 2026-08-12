<?php
class Event extends BaseModel {
    protected $table = 'events';

    // Lấy danh sách sự kiện kèm bộ lọc
    public function getAll($filters = []) {
        $sql = "SELECT e.*, c.name AS club_name, (e.capacity - e.registered_count) AS slots_left 
                FROM {$this->table} e 
                JOIN clubs c ON c.id = e.club_id 
                WHERE 1=1";
        $params = [];

        if (!empty($filters['club_id'])) {
            $sql .= " AND e.club_id = :club_id";
            $params['club_id'] = $filters['club_id'];
        }
        if (!empty($filters['status'])) {
            $sql .= " AND e.status = :status";
            $params['status'] = $filters['status'];
        }

        $sql .= " ORDER BY e.start_time ASC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    // Lấy chi tiết
    public function getById($id) {
        $sql = "SELECT e.*, c.name AS club_name 
                FROM {$this->table} e 
                JOIN clubs c ON c.id = e.club_id 
                WHERE e.id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch();
    }
}
