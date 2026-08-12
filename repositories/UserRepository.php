<?php
class UserRepository extends BaseRepository {
    public function findByEmail($email) {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE email = :email LIMIT 1");
        $stmt->execute(['email' => $email]);
        $row = $stmt->fetch();
        return $row ? new User($row) : null;
    }

    public function findById($id) {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE id = :id LIMIT 1");
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();
        return $row ? new User($row) : null;
    }

    public function create($data) {
        $stmt = $this->db->prepare("INSERT INTO users (full_name,email,password_hash,phone,role) VALUES (:full_name,:email,:password_hash,:phone,:role)");
        $stmt->execute([
            'full_name'     => $data['full_name'],
            'email'         => $data['email'],
            'password_hash' => password_hash($data['password'], PASSWORD_BCRYPT),
            'phone'         => $data['phone'] ?? null,
            'role'          => $data['role']  ?? 'member',
        ]);
        return $this->db->lastInsertId();
    }

    public function getAll($page = 1, $limit = 20) {
        $offset = ($page - 1) * $limit;
        $stmt = $this->db->prepare("SELECT * FROM users ORDER BY created_at DESC LIMIT :limit OFFSET :offset");
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return array_map(fn($r) => new User($r), $stmt->fetchAll());
    }

    public function updateStatus($id, $status) {
        $stmt = $this->db->prepare("UPDATE users SET status=:status WHERE id=:id");
        $stmt->execute(['status'=>$status,'id'=>$id]);
        return $stmt->rowCount();
    }
}
