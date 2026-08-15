<?php
class AuditLogRepository extends BaseRepository {
    
    public function log($userId, $action, $targetTable, $targetId, $detail = '') {
        $stmt = $this->db->prepare("
            INSERT INTO audit_logs (user_id, action, target_table, target_id, detail)
            VALUES (:user_id, :action, :target_table, :target_id, :detail)
        ");
        $stmt->execute([
            'user_id'      => $userId,
            'action'       => $action,
            'target_table' => $targetTable,
            'target_id'    => $targetId,
            'detail'       => $detail
        ]);
        return $this->db->lastInsertId();
    }

    public function getLogs($page = 1, $limit = 50) {
        $offset = ($page - 1) * $limit;
        $stmt = $this->db->prepare("
            SELECT a.*, u.full_name as user_name, u.email as user_email
            FROM audit_logs a
            LEFT JOIN users u ON u.id = a.user_id
            ORDER BY a.created_at DESC
            LIMIT :limit OFFSET :offset
        ");
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
