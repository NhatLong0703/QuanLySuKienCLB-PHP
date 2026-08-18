<?php
require_once __DIR__ . '/config/database.php';
$db = Database::getInstance()->getConnection();
$stmt = $db->query("SHOW CREATE TABLE events");
print_r($stmt->fetch());
$stmt = $db->query("SHOW CREATE TABLE users");
print_r($stmt->fetch());
