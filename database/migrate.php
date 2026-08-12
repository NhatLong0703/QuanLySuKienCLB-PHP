<?php
require_once __DIR__ . '/../config/Database.php';

try {
    $db = Database::getInstance()->getConnection();
    echo "Kết nối CSDL thành công.\n";

    // 1. Chạy file schema.sql
    $schemaFile = __DIR__ . '/migrations/01_schema.sql';
    if (file_exists($schemaFile)) {
        $sql = file_get_contents($schemaFile);
        $db->exec($sql);
        echo "Đã chạy thành công 01_schema.sql (Tạo bảng).\n";
    } else {
        echo "Không tìm thấy file 01_schema.sql\n";
    }

    // 2. Chạy file triggers.sql (Tách bằng '----')
    $triggerFile = __DIR__ . '/migrations/02_triggers.sql';
    if (file_exists($triggerFile)) {
        $sql = file_get_contents($triggerFile);
        $statements = explode('----', $sql);
        foreach ($statements as $stmt) {
            $stmt = trim($stmt);
            if (!empty($stmt)) {
                $db->exec($stmt);
            }
        }
        echo "Đã chạy thành công 02_triggers.sql (Tạo Trigger).\n";
    } else {
        echo "Không tìm thấy file 02_triggers.sql\n";
    }

    // 3. Chạy file seed_data.sql (Chèn dữ liệu mẫu)
    $seedFile = __DIR__ . '/migrations/03_seed_data.sql';
    if (file_exists($seedFile)) {
        $sql = file_get_contents($seedFile);
        $db->exec($sql);
        echo "Đã chạy thành công 03_seed_data.sql (Chèn dữ liệu mẫu).\n";
    } else {
        echo "Không tìm thấy file 03_seed_data.sql\n";
    }

    echo "Hoàn tất Migrations!\n";

} catch (PDOException $e) {
    echo "Lỗi CSDL: " . $e->getMessage() . "\n";
} catch (Exception $e) {
    echo "Lỗi: " . $e->getMessage() . "\n";
}
