DROP TRIGGER IF EXISTS trg_registrations_before_insert;
CREATE TRIGGER trg_registrations_before_insert
BEFORE INSERT ON registrations
FOR EACH ROW
BEGIN
    DECLARE v_status VARCHAR(20);
    DECLARE v_deadline DATETIME;
    DECLARE v_capacity INT;
    DECLARE v_count INT;

    IF NEW.status = 'registered' THEN
        SELECT status, registration_deadline, capacity, registered_count
          INTO v_status, v_deadline, v_capacity, v_count
          FROM events WHERE id = NEW.event_id FOR UPDATE;

        IF v_status <> 'open' THEN
            SIGNAL SQLSTATE '45000'
                SET MESSAGE_TEXT = 'Sự kiện không mở đăng ký (đã đóng/huỷ/nháp).';
        ELSEIF NOW() > v_deadline THEN
            SIGNAL SQLSTATE '45000'
                SET MESSAGE_TEXT = 'Đã quá hạn đăng ký sự kiện.';
        ELSEIF v_count >= v_capacity THEN
            SIGNAL SQLSTATE '45000'
                SET MESSAGE_TEXT = 'Sự kiện đã đủ số lượng người tham gia.';
        END IF;
    END IF;
END;
----
DROP TRIGGER IF EXISTS trg_registrations_after_insert;
CREATE TRIGGER trg_registrations_after_insert
AFTER INSERT ON registrations
FOR EACH ROW
BEGIN
    IF NEW.status = 'registered' THEN
        UPDATE events SET registered_count = registered_count + 1
         WHERE id = NEW.event_id;
    END IF;
END;
----
DROP TRIGGER IF EXISTS trg_registrations_after_update;
CREATE TRIGGER trg_registrations_after_update
AFTER UPDATE ON registrations
FOR EACH ROW
BEGIN
    IF OLD.status = 'registered' AND NEW.status = 'cancelled' THEN
        UPDATE events SET registered_count = registered_count - 1
         WHERE id = NEW.event_id;
    ELSEIF OLD.status = 'cancelled' AND NEW.status = 'registered' THEN
        UPDATE events SET registered_count = registered_count + 1
         WHERE id = NEW.event_id;
    END IF;
END;
----
DROP TRIGGER IF EXISTS trg_attendance_before_insert;
CREATE TRIGGER trg_attendance_before_insert
BEFORE INSERT ON attendance
FOR EACH ROW
BEGIN
    DECLARE v_reg_status VARCHAR(20);
    SELECT status INTO v_reg_status FROM registrations
     WHERE id = NEW.registration_id;

    IF v_reg_status IS NULL OR v_reg_status <> 'registered' THEN
        SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'Chỉ đăng ký hợp lệ mới được điểm danh.';
    END IF;
END;
