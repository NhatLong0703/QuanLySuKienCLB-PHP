<?php
require_once __DIR__ . '/../config/env.php';
require_once __DIR__ . '/../config/Database.php';

try {
    $db = Database::getInstance()->getConnection();
    echo "<h1>Khởi tạo Dữ liệu Mẫu (Seeding)</h1>";

    // 1. Tạo 3 tài khoản mẫu
    $passHash = password_hash('123456', PASSWORD_BCRYPT);
    $users = [
        ['Admin Tổng', 'admin@gmail.com', 'admin', '0901111111'],
        ['Ban Tổ Chức 1', 'organizer@gmail.com', 'organizer', '0902222222'],
        ['Sinh Viên A', 'member@gmail.com', 'member', '0903333333']
    ];

    $uIds = [];
    foreach($users as $u) {
        $stmt = $db->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$u[1]]);
        if ($stmt->rowCount() == 0) {
            $db->prepare("INSERT INTO users (full_name, email, password_hash, role, phone) VALUES (?, ?, ?, ?, ?)")
               ->execute([$u[0], $u[1], $passHash, $u[2], $u[3]]);
            $uIds[$u[2]] = $db->lastInsertId();
            echo "<p>✅ Tạo tài khoản {$u[2]} thành công (Mật khẩu chung: <b>123456</b>)</p>";
        } else {
            $uIds[$u[2]] = $stmt->fetchColumn();
            echo "<p>⚠️ Tài khoản {$u[2]} đã tồn tại.</p>";
        }
    }

    // 2. Tạo Câu lạc bộ mẫu
    $stmt = $db->prepare("SELECT id FROM clubs WHERE name = 'CLB Lập Trình (IT Club)'");
    $stmt->execute();
    if ($stmt->rowCount() == 0) {
        $db->prepare("INSERT INTO clubs (name, description, created_by) VALUES (?, ?, ?)")
           ->execute(['CLB Lập Trình (IT Club)', 'Câu lạc bộ dành cho dân code', $uIds['admin']]);
        $clubId = $db->lastInsertId();
        echo "<p>✅ Tạo CLB Lập Trình thành công.</p>";
    } else {
        $clubId = $stmt->fetchColumn();
        echo "<p>⚠️ CLB Lập Trình đã tồn tại.</p>";
    }

    // 3. Phân quyền Organizer cho CLB
    $db->prepare("INSERT IGNORE INTO club_managers (club_id, user_id) VALUES (?, ?)")->execute([$clubId, $uIds['organizer']]);
    echo "<p>✅ Đã phân quyền Ban Tổ Chức 1 quản lý CLB Lập Trình.</p>";

    // 4. Tạo Sự kiện mẫu
    $stmt = $db->prepare("SELECT id FROM events WHERE title = 'Workshop AI & ChatGPT 2026'");
    $stmt->execute();
    if ($stmt->rowCount() == 0) {
        $start = date('Y-m-d H:i:s', strtotime('+2 days 08:00:00'));
        $end = date('Y-m-d H:i:s', strtotime('+2 days 11:00:00'));
        $deadline = date('Y-m-d H:i:s', strtotime('+1 days 23:59:59'));
        
        $db->prepare("INSERT INTO events (club_id, title, description, location, start_time, end_time, registration_deadline, capacity, status, created_by) 
                      VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'open', ?)")
           ->execute([$clubId, 'Workshop AI & ChatGPT 2026', 'Hướng dẫn ứng dụng AI vào học tập và làm việc hiệu quả.', 'Hội trường 1', $start, $end, $deadline, 100, $uIds['organizer']]);
        echo "<p>✅ Tạo Sự kiện Workshop AI thành công.</p>";
    } else {
        echo "<p>⚠️ Sự kiện Workshop AI đã tồn tại.</p>";
    }

    echo "<h3>Hoàn tất! Bạn có thể về trang chủ để xem kết quả.</h3>";
    echo "<a href='/'>Về trang chủ</a>";

} catch (Exception $e) {
    echo "<h3 style='color:red'>Lỗi: " . $e->getMessage() . "</h3>";
}
