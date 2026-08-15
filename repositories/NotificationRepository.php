<?php
class NotificationRepository extends BaseRepository {
    
    public function create($data) {
        $stmt = $this->db->prepare("
            INSERT INTO notifications (club_id, event_id, title, content, created_by)
            VALUES (:club_id, :event_id, :title, :content, :created_by)
        ");
        $stmt->execute([
            'club_id'    => $data['club_id']  ?? null,
            'event_id'   => $data['event_id'] ?? null,
            'title'      => $data['title'],
            'content'    => $data['content'],
            'created_by' => $data['created_by']
        ]);
        return $this->db->lastInsertId();
    }

    public function getList($filters = [], $limit = 50) {
        $where = [];
        $params = [];
        
        if (!empty($filters['club_id'])) {
            $where[] = "(club_id = :club_id OR club_id IS NULL)";
            $params['club_id'] = $filters['club_id'];
        }
        if (!empty($filters['event_id'])) {
            $where[] = "event_id = :event_id";
            $params['event_id'] = $filters['event_id'];
        }

        $sql = "SELECT n.*, u.full_name as author_name 
                FROM notifications n 
                LEFT JOIN users u ON u.id = n.created_by ";
        
        if (count($where) > 0) {
            $sql .= " WHERE " . implode(' AND ', $where);
        }
        $sql .= " ORDER BY n.created_at DESC LIMIT " . (int)$limit;
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
