<?php
require 'config/env.php';
require 'config/database.php';
try {
    $db = Database::getInstance()->getConnection();
    $stmt = $db->prepare("INSERT INTO registrations (event_id,user_id,status) VALUES (7,1,'registered')");
    $stmt->execute();
    echo "Success!";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
