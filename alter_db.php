<?php
require 'config/Database.php';
try {
    $db = Database::getInstance()->getConnection();
    // Use try catch for each because IF NOT EXISTS for column is supported in MariaDB but might throw error in older MySQL if not supported
    try {
        $db->exec('ALTER TABLE clubs ADD COLUMN image VARCHAR(255) NULL AFTER description;');
        echo "Added image to clubs\n";
    } catch (Exception $e) { echo "Clubs image: " . $e->getMessage() . "\n"; }
    
    try {
        $db->exec('ALTER TABLE events ADD COLUMN image VARCHAR(255) NULL AFTER description;');
        echo "Added image to events\n";
    } catch (Exception $e) { echo "Events image: " . $e->getMessage() . "\n"; }
    
} catch (Exception $e) {
    echo "DB Error: " . $e->getMessage();
}
