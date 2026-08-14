SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- 1. USERS
CREATE TABLE IF NOT EXISTS users (
    id              BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    full_name       VARCHAR(150)        NOT NULL,
    email           VARCHAR(191)        NOT NULL,
    password_hash   VARCHAR(255)        NOT NULL,
    phone           VARCHAR(20)         NULL,
    role            ENUM("member","organizer","admin") NOT NULL DEFAULT "member",
    status          ENUM("active","locked")            NOT NULL DEFAULT "active",
    created_at      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uq_users_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 2. CLUBS
CREATE TABLE IF NOT EXISTS clubs (
    id              BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name            VARCHAR(150) NOT NULL,
    description     TEXT         NULL,
    image           VARCHAR(255) NULL,
    status          ENUM("active","inactive") NOT NULL DEFAULT "active",
    created_by      BIGINT UNSIGNED NOT NULL,
    created_at      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uq_clubs_name (name),
    CONSTRAINT fk_clubs_created_by FOREIGN KEY (created_by) REFERENCES users(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 3. CLUB_MANAGERS
CREATE TABLE IF NOT EXISTS club_managers (
    id          BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    club_id     BIGINT UNSIGNED NOT NULL,
    user_id     BIGINT UNSIGNED NOT NULL,
    assigned_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uq_club_manager (club_id, user_id),
    CONSTRAINT fk_cm_club FOREIGN KEY (club_id) REFERENCES clubs(id) ON DELETE CASCADE,
    CONSTRAINT fk_cm_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 4. EVENTS
CREATE TABLE IF NOT EXISTS events (
    id                    BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    club_id               BIGINT UNSIGNED NOT NULL,
    title                 VARCHAR(200)    NOT NULL,
    description           TEXT            NULL,
    image                 VARCHAR(255)    NULL,
    location              VARCHAR(255)    NULL,
    start_time            DATETIME        NOT NULL,
    end_time              DATETIME        NOT NULL,
    registration_deadline DATETIME        NOT NULL,
    capacity              INT UNSIGNED    NOT NULL,
    registered_count      INT UNSIGNED    NOT NULL DEFAULT 0,
    status                ENUM("draft","open","closed","cancelled") NOT NULL DEFAULT "draft",
    created_by            BIGINT UNSIGNED NOT NULL,
    created_at            DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at            DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_events_club       FOREIGN KEY (club_id)    REFERENCES clubs(id) ON DELETE CASCADE,
    CONSTRAINT fk_events_created_by FOREIGN KEY (created_by) REFERENCES users(id),
    CONSTRAINT chk_events_time     CHECK (end_time > start_time),
    CONSTRAINT chk_events_deadline CHECK (registration_deadline <= start_time),
    CONSTRAINT chk_events_capacity CHECK (capacity > 0),
    INDEX idx_events_club      (club_id),
    INDEX idx_events_status    (status),
    INDEX idx_events_start_time(start_time)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 5. REGISTRATIONS
CREATE TABLE IF NOT EXISTS registrations (
    id            BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    event_id      BIGINT UNSIGNED NOT NULL,
    user_id       BIGINT UNSIGNED NOT NULL,
    status        ENUM("registered","cancelled") NOT NULL DEFAULT "registered",
    registered_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    cancelled_at  DATETIME NULL,
    UNIQUE KEY uq_registration (event_id, user_id),
    CONSTRAINT fk_reg_event FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE,
    CONSTRAINT fk_reg_user  FOREIGN KEY (user_id)  REFERENCES users(id)  ON DELETE CASCADE,
    INDEX idx_reg_event_status (event_id, status),
    INDEX idx_reg_user         (user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 6. ATTENDANCE
CREATE TABLE IF NOT EXISTS attendance (
    id              BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    registration_id BIGINT UNSIGNED NOT NULL,
    checked_in_by   BIGINT UNSIGNED NOT NULL,
    checked_in_at   DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    note            VARCHAR(255) NULL,
    UNIQUE KEY uq_attendance_registration (registration_id),
    CONSTRAINT fk_att_registration FOREIGN KEY (registration_id) REFERENCES registrations(id) ON DELETE CASCADE,
    CONSTRAINT fk_att_checked_in_by FOREIGN KEY (checked_in_by)  REFERENCES users(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 7. NOTIFICATIONS
CREATE TABLE IF NOT EXISTS notifications (
    id         BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    club_id    BIGINT UNSIGNED NULL,
    event_id   BIGINT UNSIGNED NULL,
    title      VARCHAR(200)    NOT NULL,
    content    TEXT            NOT NULL,
    created_by BIGINT UNSIGNED NOT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_noti_club       FOREIGN KEY (club_id)    REFERENCES clubs(id)  ON DELETE CASCADE,
    CONSTRAINT fk_noti_event      FOREIGN KEY (event_id)   REFERENCES events(id) ON DELETE CASCADE,
    CONSTRAINT fk_noti_created_by FOREIGN KEY (created_by) REFERENCES users(id),
    CONSTRAINT chk_noti_target CHECK (club_id IS NOT NULL OR event_id IS NOT NULL)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 8. AUDIT_LOGS
CREATE TABLE IF NOT EXISTS audit_logs (
    id           BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id      BIGINT UNSIGNED  NULL,
    action       VARCHAR(100)     NOT NULL,
    target_table VARCHAR(50)      NOT NULL,
    target_id    BIGINT UNSIGNED  NOT NULL,
    detail       JSON             NULL,
    created_at   DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_audit_user FOREIGN KEY (user_id) REFERENCES users(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

SET FOREIGN_KEY_CHECKS = 1;
