<?php
$pdo = new PDO('mysql:host=localhost;dbname=clb_event;charset=utf8mb4', 'root', '123456');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

try {
    $pdo->exec('ALTER TABLE notifications DROP CHECK chk_noti_target');
    echo "Successfully dropped chk_noti_target constraint.\n";
} catch (Exception $e) {
    echo "Error dropping constraint (maybe already dropped?): " . $e->getMessage() . "\n";
}
