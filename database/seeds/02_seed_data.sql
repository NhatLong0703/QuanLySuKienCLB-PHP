SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- USERS (password = "123456" duoc hash bang bcrypt)
INSERT IGNORE INTO users (id, full_name, email, password_hash, phone, role, status) VALUES
(1, "Admin He Thong",   "admin@example.com", "$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi", "0900000001", "admin",     "active"),
(2, "Truong Ban To Chuc", "org1@example.com", "$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi", "0900000002", "organizer", "active"),
(3, "Nguyen Thi Member", "user1@example.com", "$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi", "0900000003", "member",    "active"),
(4, "Tran Van Thanh",    "user2@example.com", "$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi", "0900000004", "member",    "active"),
(5, "Le Thi Hoa",        "user3@example.com", "$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi", "0900000005", "member",    "active");

-- CLUBS
INSERT IGNORE INTO clubs (id, name, description, status, created_by) VALUES
(1, "CLB Lap Trinh HNMU",   "Cau lac bo lap trinh va phat trien phan mem",    "active", 1),
(2, "CLB Ngoai Ngu",         "Cau lac bo hoc ngoai ngu tieng Anh - Nhat - Han", "active", 1),
(3, "CLB The Thao",          "Cau lac bo van dong the chat va the thao",         "active", 1);

-- CLUB_MANAGERS
INSERT IGNORE INTO club_managers (club_id, user_id) VALUES
(1, 2),
(2, 2);

-- EVENTS
INSERT IGNORE INTO events (id, club_id, title, description, location, start_time, end_time, registration_deadline, capacity, registered_count, status, created_by) VALUES
(1, 1, "Hackathon mua he 2026",   "Cuoc thi lap trinh 24 gio khong ngu", "Phong A101, HNMU", "2026-09-01 08:00:00", "2026-09-02 08:00:00", "2026-08-28 23:59:59", 50, 2, "open",     2),
(2, 1, "Workshop ReactJS co ban", "Hoc ReactJS tu so den chuyen gia",    "Phong B205, HNMU", "2026-08-20 14:00:00", "2026-08-20 17:00:00", "2026-08-18 23:59:59", 30, 1, "open",     2),
(3, 2, "English Speaking Club",   "Luyen noi tieng Anh moi tuan",        "Phong C301, HNMU", "2026-08-15 18:00:00", "2026-08-15 20:00:00", "2026-08-14 23:59:59", 20, 0, "closed",   2),
(4, 3, "Giai bong da HNMU 2026",  "Giai bong da sinh vien nam 2026",     "San van dong HNMU","2026-09-10 07:00:00", "2026-09-10 17:00:00", "2026-09-05 23:59:59", 100,0, "draft",   2);

-- REGISTRATIONS
INSERT IGNORE INTO registrations (id, event_id, user_id, status, registered_at) VALUES
(1, 1, 3, "registered", "2026-08-10 10:00:00"),
(2, 1, 4, "registered", "2026-08-10 11:00:00"),
(3, 2, 3, "registered", "2026-08-11 09:00:00");

SET FOREIGN_KEY_CHECKS = 1;
