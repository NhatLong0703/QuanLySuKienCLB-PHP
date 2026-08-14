SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ATTENDANCE (Thêm dữ liệu điểm danh cho user 3 và 4 ở event 1)
INSERT IGNORE INTO attendance (id, registration_id, checked_in_by, checked_in_at) VALUES
(1, 1, 2, '2026-09-01 07:30:00'),
(2, 2, 2, '2026-09-01 07:45:00');

-- NOTIFICATIONS (Gửi thông báo mẫu cho user 3 và 4)
INSERT IGNORE INTO notifications (id, user_id, title, message, is_read, created_at) VALUES
(1, 3, 'Đăng ký thành công', 'Bạn đã đăng ký thành công sự kiện Hackathon mùa hè 2026.', 1, '2026-08-10 10:01:00'),
(2, 3, 'Nhắc nhở sự kiện', 'Sự kiện Hackathon mùa hè 2026 sẽ diễn ra vào ngày mai, nhớ mang theo laptop nhé!', 0, '2026-08-31 08:00:00'),
(3, 4, 'Đăng ký thành công', 'Bạn đã đăng ký thành công sự kiện Hackathon mùa hè 2026.', 0, '2026-08-10 11:01:00'),
(4, 2, 'Có người đăng ký mới', 'Sinh viên Nguyễn Thị Member vừa đăng ký tham gia Workshop.', 0, '2026-08-11 09:05:00');

-- AUDIT_LOGS (Ghi log các hoạt động quan trọng)
INSERT IGNORE INTO audit_logs (id, user_id, action, target_table, target_id, detail, created_at) VALUES
(1, 1, 'CREATE_CLUB', 'clubs', 1, '{"name":"CLB Lập Trình HNMU"}', '2026-08-01 08:00:00'),
(2, 1, 'ASSIGN_MANAGER', 'club_managers', 1, '{"user_id":2,"club_id":1}', '2026-08-01 08:10:00'),
(3, 2, 'CREATE_EVENT', 'events', 1, '{"title":"Hackathon mùa hè 2026"}', '2026-08-05 09:00:00'),
(4, 3, 'REGISTER_EVENT', 'events', 1, '{"registration_id":1}', '2026-08-10 10:00:00'),
(5, 2, 'CHECKIN', 'attendance', 1, '{"registration_id":1}', '2026-09-01 07:30:00');

SET FOREIGN_KEY_CHECKS = 1;
