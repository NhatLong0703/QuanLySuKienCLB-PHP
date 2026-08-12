<?php
class Club implements JsonSerializable {
    private $id, $name, $description, $status, $createdBy, $createdAt, $updatedAt;

    public function __construct(array $data = []) {
        $this->id          = $data['id']          ?? null;
        $this->name        = $data['name']        ?? null;
        $this->description = $data['description'] ?? null;
        $this->status      = $data['status']      ?? 'active';
        $this->createdBy   = $data['created_by']  ?? null;
        $this->createdAt   = $data['created_at']  ?? null;
        $this->updatedAt   = $data['updated_at']  ?? null;
    }

    public function getId()     { return $this->id; }
    public function getName()   { return $this->name; }
    public function getStatus() { return $this->status; }

    public function jsonSerialize(): mixed {
        return ['id'=>$this->id,'name'=>$this->name,'description'=>$this->description,'status'=>$this->status,'created_by'=>$this->createdBy,'created_at'=>$this->createdAt];
    }
}
