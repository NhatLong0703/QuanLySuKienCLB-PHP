<?php
class User implements JsonSerializable {
    private $id, $fullName, $email, $passwordHash, $phone, $role, $status, $createdAt, $updatedAt;

    public function __construct(array $data = []) {
        $this->id           = $data['id']            ?? null;
        $this->fullName     = $data['full_name']     ?? null;
        $this->email        = $data['email']         ?? null;
        $this->passwordHash = $data['password_hash'] ?? null;
        $this->phone        = $data['phone']         ?? null;
        $this->role         = $data['role']          ?? 'member';
        $this->status       = $data['status']        ?? 'active';
        $this->createdAt    = $data['created_at']    ?? null;
        $this->updatedAt    = $data['updated_at']    ?? null;
    }

    public function getId()           { return $this->id; }
    public function getFullName()     { return $this->fullName; }
    public function getEmail()        { return $this->email; }
    public function getPasswordHash() { return $this->passwordHash; }
    public function getPhone()        { return $this->phone; }
    public function getRole()         { return $this->role; }
    public function getStatus()       { return $this->status; }

    public function jsonSerialize(): mixed {
        return ['id'=>$this->id,'full_name'=>$this->fullName,'email'=>$this->email,'phone'=>$this->phone,'role'=>$this->role,'status'=>$this->status,'created_at'=>$this->createdAt,'updated_at'=>$this->updatedAt];
    }
}
