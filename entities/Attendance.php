<?php
class Attendance implements JsonSerializable {
    private $id, $registrationId, $checkedInBy, $checkedInAt, $note;

    public function __construct(array $data = []) {
        $this->id = $data['id'] ?? null;
        $this->registrationId = $data['registration_id'] ?? null;
        $this->checkedInBy = $data['checked_in_by'] ?? null;
        $this->checkedInAt = $data['checked_in_at'] ?? null;
        $this->note = $data['note'] ?? null;
    }

    public function getId() { return $this->id; }

    public function jsonSerialize(): mixed {
        return ['id'=>$this->id,'registration_id'=>$this->registrationId,'checked_in_by'=>$this->checkedInBy,'checked_in_at'=>$this->checkedInAt,'note'=>$this->note];
    }
}
