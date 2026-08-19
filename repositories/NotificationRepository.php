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

    public function getList($filters = []) {
        $where = [];
        $params = [];
        
        if (!empty($filters['club_id'])) {
            $where[] = "(n.club_id = :club_id OR n.club_id IS NULL)";
            $params['club_id'] = $filters['club_id'];
        }
        if (!empty($filters['event_id'])) {
            $where[] = "n.event_id = :event_id";
            $params['event_id'] = $filters['event_id'];
        }

        $whereClause = count($where) > 0 ? "WHERE " . implode(' AND ', $where) : "";

        // Count total
        $countSql = "SELECT COUNT(*) FROM notifications n $whereClause";
        $countStmt = $this->db->prepare($countSql);
        $countStmt->execute($params);
        $total = $countStmt->fetchColumn();

        $page = (int)($filters['page'] ?? 1);
        $limit = (int)($filters['limit'] ?? 5); // Default limit to 5 per page
        $offset = ($page - 1) * $limit;

        $sql = "SELECT n.*, u.full_name as author_name, c.name as club_name, e.title as event_title 
                FROM notifications n 
                LEFT JOIN users u ON u.id = n.created_by 
                LEFT JOIN clubs c ON c.id = n.club_id
                LEFT JOIN events e ON e.id = n.event_id 
                $whereClause
                ORDER BY n.created_at DESC";
                
        if ($limit > 0) {
            $sql .= " LIMIT :limit OFFSET :offset";
        }
        
        $stmt = $this->db->prepare($sql);
        foreach ($params as $k => $v) $stmt->bindValue(":$k", $v);
        
        if ($limit > 0) {
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        }
        
        $stmt->execute();
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return [
            'data' => $data,
            'total' => $total,
            'page' => $page,
            'limit' => $limit > 0 ? $limit : $total,
            'total_pages' => $limit > 0 ? ceil($total / $limit) : 1
        ];
    }
    
    public function update($id, $data) {
        $fields = [];
        $params = ['id' => $id];
        foreach ($data as $k => $v) {
            $fields[] = "$k = :$k";
            $params[$k] = $v;
        }
        if (empty($fields)) return 0;
        $sql = "UPDATE notifications SET " . implode(', ', $fields) . " WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->rowCount();
    }

    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM notifications WHERE id = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->rowCount();
    }
}
