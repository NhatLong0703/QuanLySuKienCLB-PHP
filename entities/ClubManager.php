<?php
class ClubManager {
    private $id;
    private $clubId;
    private $userId;
    private $assignedAt;

    public function __construct($data = []) {
        if (!empty($data)) {
            $this->id = $data['id'] ?? null;
            $this->clubId = $data['club_id'] ?? null;
            $this->userId = $data['user_id'] ?? null;
            $this->assignedAt = $data['assigned_at'] ?? null;
        }
    }

    public function getId() { return $this->id; }
    public function getClubId() { return $this->clubId; }
    public function getUserId() { return $this->userId; }
    public function getAssignedAt() { return $this->assignedAt; }
}
