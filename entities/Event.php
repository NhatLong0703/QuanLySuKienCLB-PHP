<?php
class Event implements JsonSerializable {
    private $id, $clubId, $title, $description, $location, $startTime, $endTime, $registrationDeadline, $capacity, $registeredCount, $status, $createdBy, $createdAt, $updatedAt;

    public function __construct(array $data = []) {
        $this->id                   = $data['id']                    ?? null;
        $this->clubId               = $data['club_id']               ?? null;
        $this->title                = $data['title']                 ?? null;
        $this->description          = $data['description']           ?? null;
        $this->location             = $data['location']              ?? null;
        $this->startTime            = $data['start_time']            ?? null;
        $this->endTime              = $data['end_time']              ?? null;
        $this->registrationDeadline = $data['registration_deadline'] ?? null;
        $this->capacity             = $data['capacity']              ?? 0;
        $this->registeredCount      = $data['registered_count']      ?? 0;
        $this->status               = $data['status']                ?? 'draft';
        $this->createdBy            = $data['created_by']            ?? null;
        $this->createdAt            = $data['created_at']            ?? null;
        $this->updatedAt            = $data['updated_at']            ?? null;
    }

    public function getId()       { return $this->id; }
    public function getTitle()    { return $this->title; }
    public function getStatus()   { return $this->status; }
    public function getCapacity() { return $this->capacity; }
    public function getRegisteredCount() { return $this->registeredCount; }

    public function jsonSerialize(): mixed {
        return ['id'=>$this->id,'club_id'=>$this->clubId,'title'=>$this->title,'description'=>$this->description,'location'=>$this->location,'start_time'=>$this->startTime,'end_time'=>$this->endTime,'registration_deadline'=>$this->registrationDeadline,'capacity'=>$this->capacity,'registered_count'=>$this->registeredCount,'slots_left'=>max(0,$this->capacity-$this->registeredCount),'status'=>$this->status,'created_by'=>$this->createdBy,'created_at'=>$this->createdAt];
    }
}
