<?php
class Registration implements JsonSerializable {
    private $id, $eventId, $userId, $status, $registeredAt, $cancelledAt;

    public function __construct(array $data = []) {
        $this->id           = $data['id']            ?? null;
        $this->eventId      = $data['event_id']      ?? null;
        $this->userId       = $data['user_id']       ?? null;
        $this->status       = $data['status']        ?? 'registered';
        $this->registeredAt = $data['registered_at'] ?? null;
        $this->cancelledAt  = $data['cancelled_at']  ?? null;
    }

    public function getId()     { return $this->id; }
    public function getStatus() { return $this->status; }

    public function jsonSerialize(): mixed {
        return ['id'=>$this->id,'event_id'=>$this->eventId,'user_id'=>$this->userId,'status'=>$this->status,'registered_at'=>$this->registeredAt,'cancelled_at'=>$this->cancelledAt];
    }
}
