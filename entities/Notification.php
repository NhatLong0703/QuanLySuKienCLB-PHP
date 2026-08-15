<?php
class Notification implements JsonSerializable {
    private $id, $clubId, $eventId, $title, $content, $createdBy, $createdAt;

    public function __construct(array $data = []) {
        $this->id        = $data['id']         ?? null;
        $this->clubId    = $data['club_id']    ?? null;
        $this->eventId   = $data['event_id']   ?? null;
        $this->title     = $data['title']      ?? null;
        $this->content   = $data['content']    ?? null;
        $this->createdBy = $data['created_by'] ?? null;
        $this->createdAt = $data['created_at'] ?? null;
    }

    public function jsonSerialize(): mixed {
        return [
            'id'         => $this->id,
            'club_id'    => $this->clubId,
            'event_id'   => $this->eventId,
            'title'      => $this->title,
            'content'    => $this->content,
            'created_by' => $this->createdBy,
            'created_at' => $this->createdAt
        ];
    }
}
