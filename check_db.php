<?php
require 'config/Database.php';
$db = Database::getInstance()->getConnection();
$stmt = $db->query('SELECT id, title, image, created_at FROM events ORDER BY id DESC LIMIT 5');
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
