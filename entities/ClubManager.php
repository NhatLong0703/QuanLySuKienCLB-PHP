<?php
class ClubManager implements JsonSerializable {
    private $id, $clubId, $userId, $assignedAt;

    public function __construct(array $data = []) {
        $this->id         = $data['id']          ?? null;
        $this->clubId     = $data['club_id']     ?? null;
        $this->userId     = $data['user_id']     ?? null;
        $this->assignedAt = $data['assigned_at'] ?? null;
    }

    public function jsonSerialize(): mixed {
        return [
            'id'          => $this->id,
            'club_id'     => $this->clubId,
            'user_id'     => $this->userId,
            'assigned_at' => $this->assignedAt
        ];
    }
}
