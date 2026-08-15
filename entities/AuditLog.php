<?php
class AuditLog implements JsonSerializable {
    private $id, $userId, $action, $targetTable, $targetId, $detail, $createdAt;

    public function __construct(array $data = []) {
        $this->id          = $data['id']           ?? null;
        $this->userId      = $data['user_id']      ?? null;
        $this->action      = $data['action']       ?? null;
        $this->targetTable = $data['target_table'] ?? null;
        $this->targetId    = $data['target_id']    ?? null;
        $this->detail      = $data['detail']       ?? null;
        $this->createdAt   = $data['created_at']   ?? null;
    }

    public function jsonSerialize(): mixed {
        return [
            'id'           => $this->id,
            'user_id'      => $this->userId,
            'action'       => $this->action,
            'target_table' => $this->targetTable,
            'target_id'    => $this->targetId,
            'detail'       => $this->detail,
            'created_at'   => $this->createdAt
        ];
    }
}
