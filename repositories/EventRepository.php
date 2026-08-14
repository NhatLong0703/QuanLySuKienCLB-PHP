<?php
class EventRepository extends BaseRepository {
    public function getAll($filters = []) {
        $sql = "SELECT e.*, c.name as club_name, (e.capacity - e.registered_count) as slots_left
                FROM events e LEFT JOIN clubs c ON c.id = e.club_id WHERE 1=1";
        $params = [];
        if (!empty($filters['status']))     { $sql .= " AND e.status=:status";               $params['status'] = $filters['status']; }
        if (!empty($filters['keyword']))    { $sql .= " AND e.title LIKE :kw";                $params['kw'] = '%'.$filters['keyword'].'%'; }
        if (!empty($filters['start_date'])) { $sql .= " AND DATE(e.start_time)>=:start_date"; $params['start_date'] = $filters['start_date']; }
        if (!empty($filters['end_date']))   { $sql .= " AND DATE(e.start_time)<=:end_date";   $params['end_date'] = $filters['end_date']; }
        if (!empty($filters['club_id']))    { $sql .= " AND e.club_id=:club_id";              $params['club_id'] = $filters['club_id']; }
        $sort = in_array($filters['sort_by'] ?? '', ['start_time','title','capacity','registered_count']) ? $filters['sort_by'] : 'start_time';
        $sql .= " ORDER BY e.$sort DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
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
