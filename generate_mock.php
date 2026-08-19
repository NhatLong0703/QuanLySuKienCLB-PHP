<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/Database.php';

try {
    $db = Database::getInstance()->getConnection();
    
    echo "Bat dau tao du lieu ao...\n";
    
    // Mat khau "123456" cho tat ca
    $passHash = '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi';
    
    // 1. Tao Users (50 members, 5 organizers)
    $ho = ['Nguyen', 'Tran', 'Le', 'Pham', 'Hoang', 'Vu', 'Vo', 'Dang', 'Bui', 'Do', 'Ho', 'Ngo', 'Duong', 'Ly'];
    $dem = ['Van', 'Thi', 'Minh', 'Ngoc', 'Huu', 'Duc', 'Tuan', 'Thanh', 'Quang', 'Xuan', 'Gia'];
    $ten = ['Anh', 'Binh', 'Cuong', 'Dung', 'Hoa', 'Hung', 'Linh', 'Lan', 'Nam', 'Nghia', 'Phuc', 'Quynh', 'Son', 'Thuy', 'Tuan', 'Trang', 'Tien', 'Vinh', 'Yen'];
    
    $userIds = [];
    $orgIds = [];
    
    // Tao 5 organizers
    for($i=1; $i<=5; $i++) {
        $name = $ho[array_rand($ho)] . ' ' . $dem[array_rand($dem)] . ' ' . $ten[array_rand($ten)];
        $email = "org{$i}_" . time() . "@example.com";
        $stmt = $db->prepare("INSERT INTO users (full_name, email, password_hash, role) VALUES (?, ?, ?, 'organizer')");
        $stmt->execute([$name, $email, $passHash]);
        $orgIds[] = $db->lastInsertId();
    }
    
    // Tao 50 members
    for($i=1; $i<=50; $i++) {
        $name = $ho[array_rand($ho)] . ' ' . $dem[array_rand($dem)] . ' ' . $ten[array_rand($ten)];
        $email = "member{$i}_" . time() . "@example.com";
        $stmt = $db->prepare("INSERT INTO users (full_name, email, password_hash, role) VALUES (?, ?, ?, 'member')");
        $stmt->execute([$name, $email, $passHash]);
        $userIds[] = $db->lastInsertId();
    }
    echo "Da tao 50 Users.\n";
    
    // 2. Tao Clubs (10 CLB)
    $clubNames = [
        'CLB Am nhac Giai dieu Tre', 'CLB Nhiep anh Goc Nhin', 'CLB Tinh nguyen Mua he xanh', 
        'CLB Bong ro Sinh vien', 'CLB Vo thuat co truyen', 'CLB Khoi nghiep Sang tao',
        'CLB Tieng Anh Giao tiep', 'CLB Ky nang mem', 'CLB Truyen thong da phuong tien', 'CLB Nghe thuat Duong pho'
    ];
    $clubIds = [];
    foreach($clubNames as $idx => $cname) {
        $orgId = $orgIds[array_rand($orgIds)];
        $stmt = $db->prepare("INSERT IGNORE INTO clubs (name, description, created_by) VALUES (?, ?, ?)");
        $desc = "Day la cau lac bo " . $cname . " danh cho cac ban sinh vien dam me va mong muon phat trien ban than.";
        $stmt->execute([$cname, $desc, 1]); // created by admin
        $cid = $db->lastInsertId();
        $clubIds[] = $cid;
        
        // Assign manager
        $db->query("INSERT IGNORE INTO club_managers (club_id, user_id) VALUES ($cid, $orgId)");
    }
    echo "Da tao 10 Clubs.\n";
    
    // 3. Tao Events (30 events: 10 closed, 15 open, 5 draft)
    $eventTitles = ['Giao luu am nhac', 'Hoi thao ky nang', 'Tuyen thanh vien', 'Giai dau the thao', 'Chien dich mua he', 'Workshop sang tao', 'Cuoc thi tai nang', 'Trai he sinh vien'];
    $eventIds = [];
    
    for($i=1; $i<=30; $i++) {
        $cid = $clubIds[array_rand($clubIds)];
        $title = $eventTitles[array_rand($eventTitles)] . " " . rand(2024, 2026);
        $desc = "Su kien hap dan tu cau lac bo. Hay nhanh tay dang ky tham gia de nhan nhieu phan qua va giay chung nhan.";
        $loc = "Hoi truong " . rand(1, 5) . ", Tang " . rand(1, 5);
        $cap = rand(20, 100);
        $orgId = $orgIds[array_rand($orgIds)];
        
        // Randomize dates (some in past, some in future)
        $offsetDays = rand(-30, 30);
        $start = date('Y-m-d H:i:s', strtotime("$offsetDays days 08:00:00"));
        $end = date('Y-m-d H:i:s', strtotime("$offsetDays days 11:30:00"));
        $deadline = date('Y-m-d H:i:s', strtotime("$offsetDays days -1 days 23:59:59"));
        
        $status = 'open';
        if($offsetDays < 0) $status = 'closed';
        if(rand(1, 10) > 8) $status = 'draft';
        
        $stmt = $db->prepare("INSERT IGNORE INTO events (club_id, title, description, location, start_time, end_time, registration_deadline, capacity, status, created_by) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$cid, $title, $desc, $loc, $start, $end, $deadline, $cap, $status, $orgId]);
        
        if($status !== 'draft') {
            $eventIds[] = ['id' => $db->lastInsertId(), 'cap' => $cap, 'status' => $status, 'start' => $start];
        }
    }
    echo "Da tao 30 Events.\n";
    
    // 4. Tao Registrations, Attendance & Feedbacks
    foreach($eventIds as $ev) {
        // Random number of registrations (1 to capacity, max count(userIds))
        $numReg = min(rand(5, $ev['cap']), count($userIds));
        $uList = (array) array_rand(array_flip($userIds), $numReg); // pick random users
        
        $regCount = 0;
        foreach($uList as $uid) {
            $status = 'registered';
            if($ev['status'] == 'closed') {
                $status = rand(1,10) > 2 ? 'attended' : 'cancelled'; // 80% attended
            } else {
                if(rand(1,10) > 9) $status = 'cancelled';
            }
            
            $stmt = $db->prepare("INSERT IGNORE INTO registrations (event_id, user_id, status) VALUES (?, ?, ?)");
            $stmt->execute([$ev['id'], $uid, ($status == 'attended' ? 'registered' : $status)]);
            $regId = $db->lastInsertId();
            $regCount++;
            
            // Attendance
            if($status == 'attended' && $regId) {
                $checkInTime = date('Y-m-d H:i:s', strtotime($ev['start']) + rand(0, 1800)); // Within first 30 mins
                try {
                    $db->query("INSERT IGNORE INTO attendance (registration_id, checked_in_by, checked_in_at) VALUES ($regId, 1, '$checkInTime')");
                    $db->query("UPDATE registrations SET status = 'attended' WHERE id = $regId");
                } catch(Exception $ex) {}
                
                // Feedback (50% chance)
                if(rand(1, 10) > 5) {
                    $rating = rand(3, 5);
                    $fbComments = ['Rat tuyet voi', 'To chuc tot', 'Am thanh hoi be', 'Can nhieu thoi gian hon', 'Hay va y nghia'];
                    $comment = $fbComments[array_rand($fbComments)];
                    $stmtFb = $db->prepare("INSERT IGNORE INTO event_feedbacks (event_id, user_id, rating, comment) VALUES (?, ?, ?, ?)");
                    $stmtFb->execute([$ev['id'], $uid, $rating, $comment]);
                }
            }
        }
        
        // Update event registered_count
        $db->query("UPDATE events SET registered_count = $regCount WHERE id = {$ev['id']}");
    }
    echo "Da tao hang tram Registrations, Attendances va Feedbacks.\n";
    
    echo "Hoan tat mock data.\n";
    
} catch (Exception $e) {
    echo "Loi: " . $e->getMessage() . "\n";
}
