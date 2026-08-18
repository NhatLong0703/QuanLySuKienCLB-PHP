<?php
class EventRepository extends BaseRepository {
    public function getAll($filters = []) {
        $where = "WHERE 1=1";
        $params = [];
        
        if (!empty($filters['status']))     { $where .= " AND e.status=:status";               $params['status'] = $filters['status']; }
        if (!empty($filters['keyword']))    { $where .= " AND e.title LIKE :kw";                $params['kw'] = '%'.$filters['keyword'].'%'; }
        if (!empty($filters['start_date'])) { $where .= " AND DATE(e.start_time)>=:start_date"; $params['start_date'] = $filters['start_date']; }
        if (!empty($filters['end_date']))   { $where .= " AND DATE(e.start_time)<=:end_date";   $params['end_date'] = $filters['end_date']; }
        if (!empty($filters['club_id']))    { $where .= " AND e.club_id=:club_id";              $params['club_id'] = $filters['club_id']; }
        
        $sort = in_array($filters['sort_by'] ?? '', ['start_time','title','capacity','registered_count']) ? $filters['sort_by'] : 'start_time';
        
        $countSql = "SELECT COUNT(*) FROM events e LEFT JOIN clubs c ON c.id = e.club_id $where";
        $countStmt = $this->db->prepare($countSql);
        $countStmt->execute($params);
        $total = $countStmt->fetchColumn();

        $sql = "SELECT e.*, c.name as club_name, (e.capacity - e.registered_count) as slots_left
                FROM events e LEFT JOIN clubs c ON c.id = e.club_id $where ORDER BY e.$sort DESC";
        
        $page = (int)($filters['page'] ?? 1);
        $limit = (int)($filters['limit'] ?? 6); // default 6 for pagination
        $offset = ($page - 1) * $limit;
        
        // If limit is explicitly set to -1, fetch all (for no pagination cases)
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

    public function findById($id) {
        $stmt = $this->db->prepare("SELECT e.*,c.name as club_name,(e.capacity-e.registered_count) as slots_left FROM events e LEFT JOIN clubs c ON c.id=e.club_id WHERE e.id=:id LIMIT 1");
        $stmt->execute(['id'=>$id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function create($data) {
        $cols = implode(',', array_keys($data));
        $vals = implode(',', array_map(fn($k) => ":$k", array_keys($data)));
        $stmt = $this->db->prepare("INSERT INTO events ($cols) VALUES ($vals)");
        $stmt->execute($data);
        return $this->db->lastInsertId();
    }

    public function update($id, $data) {
        $sets = implode(',', array_map(fn($k) => "$k=:$k", array_keys($data)));
        $data['id'] = $id;
        $stmt = $this->db->prepare("UPDATE events SET $sets WHERE id=:id");
        $stmt->execute($data);
        return $stmt->rowCount();
    }

    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM events WHERE id=:id");
        $stmt->execute(['id'=>$id]);
        return $stmt->rowCount();
    }

    public function incrementRegistered($id) {
        $this->db->prepare("UPDATE events SET registered_count=registered_count+1 WHERE id=:id")->execute(['id'=>$id]);
    }

    public function decrementRegistered($id) {
        $this->db->prepare("UPDATE events SET registered_count=GREATEST(registered_count-1,0) WHERE id=:id")->execute(['id'=>$id]);
    }
}
