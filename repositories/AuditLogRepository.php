<?php
class AuditLogRepository extends BaseRepository {
    public function log($userId, $action, $targetTable, $targetId, $detail = null) {
        $stmt = $this->db->prepare("INSERT INTO audit_logs (user_id, action, target_table, target_id, detail) VALUES (:user_id, :action, :target_table, :target_id, :detail)");
        $stmt->execute([
            'user_id' => $userId,
            'action' => $action,
            'target_table' => $targetTable,
            'target_id' => $targetId,
            'detail' => $detail ? json_encode($detail, JSON_UNESCAPED_UNICODE) : null
        ]);
        return $this->db->lastInsertId();
    }

    public function getLogs($limit = 100) {
        $stmt = $this->db->prepare("SELECT a.*, u.full_name as user_name FROM audit_logs a LEFT JOIN users u ON a.user_id = u.id ORDER BY a.created_at DESC LIMIT :limit");
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
