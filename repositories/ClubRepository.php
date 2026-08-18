<?php
class ClubRepository extends BaseRepository {
    public function getAll($page = 1, $limit = 6, $status = null) {
        $offset = ($page - 1) * $limit;
        
        $where = "";
        $params = [];
        if ($status) {
            $where = "WHERE status = :status";
            $params['status'] = $status;
        }

        $countStmt = $this->db->prepare("SELECT COUNT(*) FROM clubs $where");
        $countStmt->execute($params);
        $total = $countStmt->fetchColumn();

        $sql = "SELECT * FROM clubs $where ORDER BY name";
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
        $stmt = $this->db->prepare("SELECT * FROM clubs WHERE id=:id LIMIT 1");
        $stmt->execute(['id'=>$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public function create($data) {
        $cols = implode(',', array_keys($data));
        $vals = implode(',', array_map(fn($k) => ":$k", array_keys($data)));
        $stmt = $this->db->prepare("INSERT INTO clubs ($cols) VALUES ($vals)");
        $stmt->execute($data);
        return $this->db->lastInsertId();
    }

    public function update($id, $data) {
        $sets = implode(',', array_map(fn($k) => "$k=:$k", array_keys($data)));
        $data['id'] = $id;
        $stmt = $this->db->prepare("UPDATE clubs SET $sets WHERE id=:id");
        $stmt->execute($data);
        return $stmt->rowCount();
    }

    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM clubs WHERE id=:id");
        $stmt->execute(['id'=>$id]);
        return $stmt->rowCount();
    }
}
