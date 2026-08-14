<?php
class ClubRepository extends BaseRepository {
    public function getAll() {
        return $this->db->query("SELECT * FROM clubs ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);
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
