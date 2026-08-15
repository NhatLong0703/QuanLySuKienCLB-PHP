<?php
require 'config/Database.php';
$db = Database::getInstance()->getConnection();
$db->exec("ALTER TABLE registrations MODIFY COLUMN status ENUM('registered','cancelled','attended') NOT NULL DEFAULT 'registered'");
echo "Table altered successfully\n";
