<?php
require 'config/Database.php';
$db = Database::getInstance()->getConnection();
$stmt = $db->query("SHOW TRIGGERS LIKE 'registrations'");
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
