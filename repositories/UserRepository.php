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

    public function getAll($page = 1, $limit = 20, $role = null) {
        $offset = ($page - 1) * $limit;
        
        $where = "";
        $params = [];
        if ($role) {
            $where = "WHERE role = :role";
            $params['role'] = $role;
        }

        $countStmt = $this->db->prepare("SELECT COUNT(*) FROM users $where");
        $countStmt->execute($params);
        $total = $countStmt->fetchColumn();

        $sql = "SELECT * FROM users $where ORDER BY created_at DESC";
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
        
        $data = array_map(fn($r) => new User($r), $stmt->fetchAll());

        return [
            'data' => $data,
            'total' => $total,
            'page' => $page,
            'limit' => $limit > 0 ? $limit : $total,
            'total_pages' => $limit > 0 ? ceil($total / $limit) : 1
        ];
    }

    public function updateStatus($id, $status) {
        $stmt = $this->db->prepare("UPDATE users SET status=:status WHERE id=:id");
        $stmt->execute(['status'=>$status,'id'=>$id]);
        return $stmt->rowCount();
    }

    public function update($id, $data) {
        $fields = [];
        $params = ['id' => $id];
        foreach ($data as $k => $v) {
            if ($k === 'password') {
                $fields[] = "password_hash = :password_hash";
                $params['password_hash'] = password_hash($v, PASSWORD_BCRYPT);
            } else {
                $fields[] = "$k = :$k";
                $params[$k] = $v;
            }
        }
        if (empty($fields)) return 0;
        $sql = "UPDATE users SET " . implode(', ', $fields) . " WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->rowCount();
    }

    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM users WHERE id = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->rowCount();
    }
}
