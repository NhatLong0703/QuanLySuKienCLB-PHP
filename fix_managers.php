<?php
require_once __DIR__ . '/config/env.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/repositories/BaseRepository.php';

class Fixer extends BaseRepository {
    public function fix() {
        $stmt = $this->db->prepare("INSERT IGNORE INTO club_managers (club_id, user_id) SELECT id, created_by FROM clubs");
        $stmt->execute();
        echo "Updated " . $stmt->rowCount() . " clubs.\n";
    }
}
$f = new Fixer();
$f->fix();

