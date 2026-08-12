SET FOREIGN_KEY_CHECKS = 0;
TRUNCATE TABLE users;
TRUNCATE TABLE clubs;
TRUNCATE TABLE events;
TRUNCATE TABLE registrations;
TRUNCATE TABLE attendance;
SET FOREIGN_KEY_CHECKS = 1;

-- Thêm Users
INSERT INTO users (id, full_name, email, password_hash, role) VALUES 
(1, 'Admin Toàn Hệ Thống', 'admin@example.com', '$2y$10$Y1/2H...dummy', 'admin'),
(2, 'Ban Tổ Chức Nhạc', 'btc_nhac@example.com', '$2y$10$Y1/2H...dummy', 'organizer'),
(3, 'Thành Viên Nguyễn A', 'nguyena@example.com', '$2y$10$Y1/2H...dummy', 'member'),
(4, 'Thành Viên Trần B', 'tranb@example.com', '$2y$10$Y1/2H...dummy', 'member');

-- Thêm Clubs
INSERT INTO clubs (id, name, description, created_by) VALUES 
(1, 'CLB Âm Nhạc', 'Câu lạc bộ giao lưu âm nhạc sinh viên', 1),
(2, 'CLB Tin Học', 'Giao lưu lập trình và công nghệ', 1);

-- Thêm Events
-- Sự kiện 1: Đang mở đăng ký, hạn là 3 ngày tới, bắt đầu sau 5 ngày, 100 chỗ.
INSERT INTO events (id, club_id, title, description, location, start_time, end_time, registration_deadline, capacity, status, created_by) VALUES
(1, 1, 'Liveshow Chào Tân Sinh Viên 2026', 'Sự kiện âm nhạc hoành tráng nhất năm', 'Hội trường lớn', DATE_ADD(NOW(), INTERVAL 5 DAY), DATE_ADD(NOW(), INTERVAL 6 DAY), DATE_ADD(NOW(), INTERVAL 3 DAY), 100, 'open', 2);

-- Sự kiện 2: Đã quá hạn đăng ký (deadline hôm qua)
INSERT INTO events (id, club_id, title, description, location, start_time, end_time, registration_deadline, capacity, status, created_by) VALUES
(2, 2, 'Workshop Lập trình PHP MVC', 'Chia sẻ kiến thức MVC thuần', 'Phòng lab 1', DATE_ADD(NOW(), INTERVAL 2 DAY), DATE_ADD(NOW(), INTERVAL 3 DAY), DATE_SUB(NOW(), INTERVAL 1 DAY), 50, 'open', 1);

-- Sự kiện 3: Sức chứa nhỏ (2 người) để test trigger quá tải
INSERT INTO events (id, club_id, title, description, location, start_time, end_time, registration_deadline, capacity, status, created_by) VALUES
(3, 1, 'Giao lưu Band Nhạc Nhỏ', 'Test max capacity', 'Phòng nhạc', DATE_ADD(NOW(), INTERVAL 10 DAY), DATE_ADD(NOW(), INTERVAL 11 DAY), DATE_ADD(NOW(), INTERVAL 5 DAY), 2, 'open', 2);
