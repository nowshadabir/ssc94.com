-- ============================================================================
-- SSC BATCH '94 — MASTER DATABASE SCHEMA
-- Updated: 2026-02-21
-- Run this file ONCE on a fresh database.
-- Safe to re-run: uses IF NOT EXISTS / IF EXISTS guards throughout.
-- ============================================================================

CREATE DATABASE IF NOT EXISTS ssc94_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE ssc94_db;

SET FOREIGN_KEY_CHECKS = 0;

-- ============================================================================
-- 1. USERS
-- ============================================================================
CREATE TABLE IF NOT EXISTS users (
    user_id        INT PRIMARY KEY AUTO_INCREMENT,
    user_code      VARCHAR(6)  UNIQUE DEFAULT NULL,          -- 6-digit member ID
    balance        DECIMAL(10,2)      DEFAULT 0.00,          -- wallet balance
    full_name      VARCHAR(255)       NOT NULL,
    mobile         VARCHAR(11)  UNIQUE NOT NULL,
    email          VARCHAR(255) UNIQUE NOT NULL,
    password_hash  VARCHAR(255)       NOT NULL,
    profile_photo  VARCHAR(500),
    status         ENUM('active','inactive','pending') DEFAULT 'pending',
    email_verified  BOOLEAN DEFAULT FALSE,
    mobile_verified BOOLEAN DEFAULT FALSE,
    referral_code  VARCHAR(50)  DEFAULT NULL,
    referred_by    INT          DEFAULT NULL,
    created_at     TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at     TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    last_login     TIMESTAMP NULL,
    INDEX idx_mobile        (mobile),
    INDEX idx_email         (email),
    INDEX idx_status        (status),
    INDEX idx_full_name     (full_name),
    INDEX idx_referral_code (referral_code),
    INDEX idx_referred_by   (referred_by)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- 2. USER PERSONAL INFO
-- ============================================================================
CREATE TABLE IF NOT EXISTS user_personal_info (
    info_id           INT PRIMARY KEY AUTO_INCREMENT,
    user_id           INT NOT NULL,
    father_name       VARCHAR(255),
    mother_name       VARCHAR(255),
    blood_group       ENUM('A+','A-','B+','B-','O+','O-','AB+','AB-') NOT NULL,
    permanent_address TEXT NOT NULL,
    date_of_birth     DATE,
    gender            ENUM('Male','Female','Other'),
    willing_to_donate TINYINT(1) DEFAULT 1,
    last_donation     DATE NULL,
    created_at        TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at        TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    INDEX idx_blood_group (blood_group)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- 3. USER PRESENT INFO
-- ============================================================================
CREATE TABLE IF NOT EXISTS user_present_info (
    present_id                 INT PRIMARY KEY AUTO_INCREMENT,
    user_id                    INT NOT NULL,
    job_business               VARCHAR(255) NOT NULL,
    institute_working_station  VARCHAR(255) NOT NULL,
    current_location           VARCHAR(255) NOT NULL,
    current_address            TEXT,
    linkedin_profile           VARCHAR(500),
    facebook_profile           VARCHAR(500),
    created_at                 TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at                 TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    INDEX idx_working_station (institute_working_station),
    INDEX idx_cur_location    (current_location)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- 4. USER SCHOOL INFO (SSC)
-- ============================================================================
CREATE TABLE IF NOT EXISTS user_school_info (
    school_id      INT PRIMARY KEY AUTO_INCREMENT,
    user_id        INT NOT NULL,
    school_name    VARCHAR(255) NOT NULL,
    zilla          VARCHAR(100) NOT NULL,
    union_upozilla VARCHAR(100) NOT NULL,
    batch_year     INT NOT NULL DEFAULT 1994,
    roll_number    VARCHAR(50),
    board          VARCHAR(100),
    gpa            DECIMAL(3,2),
    created_at     TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at     TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    INDEX idx_school   (school_name),
    INDEX idx_zilla    (zilla),
    INDEX idx_upozilla (union_upozilla),
    INDEX idx_batch    (batch_year)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- 5. PASSWORD RESET TOKENS
-- ============================================================================
CREATE TABLE IF NOT EXISTS password_reset_tokens (
    token_id   INT PRIMARY KEY AUTO_INCREMENT,
    user_id    INT NOT NULL,
    token      VARCHAR(255) NOT NULL UNIQUE,
    expires_at TIMESTAMP NOT NULL,
    used       BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    INDEX idx_token   (token),
    INDEX idx_expires (expires_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- 6. EMAIL / MOBILE VERIFICATION TOKENS
-- ============================================================================
CREATE TABLE IF NOT EXISTS verification_tokens (
    verification_id INT PRIMARY KEY AUTO_INCREMENT,
    user_id         INT NOT NULL,
    token           VARCHAR(255) NOT NULL UNIQUE,
    type            ENUM('email','mobile') NOT NULL,
    expires_at      TIMESTAMP NOT NULL,
    verified        BOOLEAN DEFAULT FALSE,
    created_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    INDEX idx_token (token),
    INDEX idx_type  (type)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- 7. LOGIN HISTORY / AUDIT LOG
-- ============================================================================
CREATE TABLE IF NOT EXISTS login_history (
    history_id      INT PRIMARY KEY AUTO_INCREMENT,
    user_id         INT NOT NULL,
    login_time      TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    ip_address      VARCHAR(45),
    user_agent      TEXT,
    status          ENUM('success','failed') NOT NULL,
    failure_reason  VARCHAR(255),
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    INDEX idx_user_time (user_id, login_time),
    INDEX idx_status    (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- 8. USER SESSIONS (remember-me)
-- ============================================================================
CREATE TABLE IF NOT EXISTS user_sessions (
    session_id VARCHAR(255) PRIMARY KEY,
    user_id    INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    expires_at TIMESTAMP NULL,
    ip_address VARCHAR(45),
    user_agent TEXT,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    INDEX idx_user    (user_id),
    INDEX idx_expires (expires_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- 9. PAYMENTS
-- ============================================================================
CREATE TABLE IF NOT EXISTS payments (
    payment_id      INT PRIMARY KEY AUTO_INCREMENT,
    user_id         INT NOT NULL,
    payment_type    ENUM('registration','donation','event','reunion','scholarship') DEFAULT 'registration',
    amount          DECIMAL(10,2) NOT NULL,
    currency        VARCHAR(3)    DEFAULT 'BDT',
    payment_method  VARCHAR(50)   DEFAULT 'bkash',
    transaction_id  VARCHAR(100)  UNIQUE,
    payment_status  ENUM('pending','processing','completed','failed','refunded') DEFAULT 'pending',
    bkash_payment_id VARCHAR(100),
    bkash_trx_id    VARCHAR(100),
    payment_data    JSON,
    payment_date    TIMESTAMP NULL,
    created_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    INDEX idx_user        (user_id),
    INDEX idx_transaction (transaction_id),
    INDEX idx_status      (payment_status),
    INDEX idx_trx         (bkash_trx_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- 10. PAYMENT GATEWAY SETTINGS
-- ============================================================================
CREATE TABLE IF NOT EXISTS payment_gateway_settings (
    setting_id    INT PRIMARY KEY AUTO_INCREMENT,
    gateway_name  VARCHAR(50) NOT NULL UNIQUE,
    is_active     BOOLEAN DEFAULT FALSE,
    api_key       VARCHAR(500),
    api_secret    VARCHAR(500),
    merchant_number VARCHAR(50),
    webhook_url   VARCHAR(500),
    success_url   VARCHAR(500),
    cancel_url    VARCHAR(500),
    settings_data JSON,
    created_at    TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at    TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_gateway_name (gateway_name),
    INDEX idx_active       (is_active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- 11. BALANCE TRANSACTIONS (wallet ledger)
-- ============================================================================
CREATE TABLE IF NOT EXISTS balance_transactions (
    transaction_id  INT PRIMARY KEY AUTO_INCREMENT,
    user_id         INT NOT NULL,
    transaction_type ENUM('credit','debit') NOT NULL,
    amount          DECIMAL(10,2) NOT NULL,
    balance_before  DECIMAL(10,2) NOT NULL,
    balance_after   DECIMAL(10,2) NOT NULL,
    description     VARCHAR(255),
    reference_type  VARCHAR(50),   -- 'referral', 'payment', 'withdrawal', etc.
    reference_id    INT,
    created_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    INDEX idx_user_id         (user_id),
    INDEX idx_transaction_type (transaction_type),
    INDEX idx_created_at      (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- 12. EVENTS
-- ============================================================================
CREATE TABLE IF NOT EXISTS events (
    event_id          INT PRIMARY KEY AUTO_INCREMENT,
    event_name        VARCHAR(255) NOT NULL,
    event_description LONGTEXT,
    event_date        DATE NOT NULL,
    event_time        TIME,
    venue             VARCHAR(255) NOT NULL,
    venue_address     TEXT,
    venue_latitude    DECIMAL(10,8),
    venue_longitude   DECIMAL(11,8),
    event_type        ENUM('reunion','donation_drive','picnic','seminar','social','other') DEFAULT 'other',
    status            ENUM('upcoming','ongoing','completed','cancelled') DEFAULT 'upcoming',
    registration_fee  DECIMAL(10,2) DEFAULT 0.00,
    max_attendees     INT,
    event_poster      VARCHAR(500),
    event_banner      VARCHAR(500),
    organizer_id      INT NOT NULL,
    created_at        TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at        TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (organizer_id) REFERENCES users(user_id) ON DELETE CASCADE,
    INDEX idx_event_date     (event_date),
    INDEX idx_event_status   (status),
    INDEX idx_event_organizer (organizer_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- 13. EVENT ATTENDEES
-- ============================================================================
CREATE TABLE IF NOT EXISTS event_attendees (
    attendee_id           INT PRIMARY KEY AUTO_INCREMENT,
    event_id              INT NOT NULL,
    user_id               INT NOT NULL,
    registration_status   ENUM('registered','attended','no_show','cancelled') DEFAULT 'registered',
    t_shirt_size          ENUM('XS','S','M','L','XL','XXL'),
    guest_name            VARCHAR(255),
    guest_count           INT DEFAULT 1,
    special_requirements  TEXT,
    registered_at         TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    attended_at           TIMESTAMP NULL,
    UNIQUE KEY unique_user_event (event_id, user_id),
    FOREIGN KEY (event_id) REFERENCES events(event_id) ON DELETE CASCADE,
    FOREIGN KEY (user_id)  REFERENCES users(user_id)  ON DELETE CASCADE,
    INDEX idx_attendee_event  (event_id),
    INDEX idx_attendee_user   (user_id),
    INDEX idx_attendee_status (registration_status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- 14. EVENT GALLERY
-- ============================================================================
CREATE TABLE IF NOT EXISTS event_gallery (
    gallery_id      INT PRIMARY KEY AUTO_INCREMENT,
    event_id        INT NOT NULL,
    image_filename  VARCHAR(500) NOT NULL,
    image_caption   VARCHAR(255),
    uploaded_by     INT,
    uploaded_at     TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (event_id)    REFERENCES events(event_id) ON DELETE CASCADE,
    FOREIGN KEY (uploaded_by) REFERENCES users(user_id)  ON DELETE SET NULL,
    INDEX idx_gallery_event (event_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- 15. EVENT UPDATES / ANNOUNCEMENTS
-- ============================================================================
CREATE TABLE IF NOT EXISTS event_updates (
    update_id      INT PRIMARY KEY AUTO_INCREMENT,
    event_id       INT NOT NULL,
    update_title   VARCHAR(255) NOT NULL,
    update_content LONGTEXT NOT NULL,
    update_type    ENUM('announcement','schedule_change','reminder','itinerary') DEFAULT 'announcement',
    posted_by      INT NOT NULL,
    posted_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (event_id)  REFERENCES events(event_id) ON DELETE CASCADE,
    FOREIGN KEY (posted_by) REFERENCES users(user_id)  ON DELETE CASCADE,
    INDEX idx_updates_event (event_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- 16. EVENT TICKETS (paid events)
-- ============================================================================
CREATE TABLE IF NOT EXISTS event_tickets (
    ticket_id         INT PRIMARY KEY AUTO_INCREMENT,
    event_id          INT NOT NULL,
    user_id           INT NOT NULL,
    ticket_number     VARCHAR(50) UNIQUE NOT NULL,
    qr_code           LONGTEXT,
    ticket_status     ENUM('valid','used','cancelled','refunded') DEFAULT 'valid',
    amount_paid       DECIMAL(10,2),
    payment_method    ENUM('bkash','nagad','bank_transfer','cash','free') DEFAULT 'free',
    payment_reference VARCHAR(100),
    issued_at         TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    used_at           TIMESTAMP NULL,
    FOREIGN KEY (event_id) REFERENCES events(event_id) ON DELETE CASCADE,
    FOREIGN KEY (user_id)  REFERENCES users(user_id)  ON DELETE CASCADE,
    INDEX idx_ticket_event  (event_id),
    INDEX idx_ticket_user   (user_id),
    INDEX idx_ticket_status (ticket_status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- 17. REUNIONS (active reunion metadata)
-- ============================================================================
CREATE TABLE IF NOT EXISTS reunions (
    reunion_id             INT PRIMARY KEY AUTO_INCREMENT,
    title                  VARCHAR(255) NOT NULL,
    reunion_date           DATE NOT NULL,
    reunion_time           VARCHAR(10) DEFAULT '09:00',
    venue                  VARCHAR(255) NOT NULL,
    venue_details          VARCHAR(255),
    food_menu              TEXT,
    activities             TEXT,
    cost_alumnus           DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    cost_guest             DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    registration_deadline  DATE,
    status                 ENUM('active','inactive','completed') DEFAULT 'active',
    created_at             TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at             TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_reunion_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- 18. REUNION REGISTRATIONS
--     guest_count  = number of additional guests beyond the member
--     gender       = member's gender (from the registration form)
--     guests_data  = JSON array [{name, gender, tshirt}, ...] per extra guest
-- ============================================================================
CREATE TABLE IF NOT EXISTS reunion_registrations (
    registration_id INT PRIMARY KEY AUTO_INCREMENT,
    user_id         INT NOT NULL,
    reunion_id      INT NOT NULL,
    ticket_number   VARCHAR(20) UNIQUE NOT NULL,
    full_name       VARCHAR(255) NOT NULL,
    mobile          VARCHAR(20)  NOT NULL,
    tshirt_size     ENUM('S','M','L','XL','XXL') NOT NULL DEFAULT 'XL',
    gender          ENUM('male','female','other') DEFAULT 'male',
    guest_count     INT DEFAULT 0,
    guests_data     JSON,
    total_amount    DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    payment_status  ENUM('pending','completed','failed') DEFAULT 'pending',
    qr_code_data    TEXT,
    transaction_id  VARCHAR(100),
    created_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id)    REFERENCES users(user_id)    ON DELETE CASCADE,
    FOREIGN KEY (reunion_id) REFERENCES reunions(reunion_id) ON DELETE CASCADE,
    UNIQUE KEY unique_user_reunion (user_id, reunion_id),
    INDEX idx_rr_payment_status (payment_status),
    INDEX idx_rr_ticket         (ticket_number)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS = 1;

-- ============================================================================
-- DEFAULT GATEWAY CONFIGURATION
-- Update URLs before going live (replace localhost with your domain).
-- ============================================================================
INSERT INTO payment_gateway_settings (gateway_name, is_active, api_key, success_url, cancel_url, webhook_url)
VALUES
    ('rupantorpay', TRUE,  'g8wxKwv4ts2ToZO7siWuQsAAHcfafRnRRPAjMeOrcpbTuX8vys',
     'http://localhost/SSC-94/ssc94.com/api/payment/success.php',
     'http://localhost/SSC-94/ssc94.com/api/payment/cancel.php',
     'http://localhost/SSC-94/ssc94.com/api/payment/webhook.php'),
    ('bkash', FALSE, '', '', '', '')
ON DUPLICATE KEY UPDATE
    api_key     = VALUES(api_key),
    is_active   = VALUES(is_active),
    success_url = VALUES(success_url),
    cancel_url  = VALUES(cancel_url),
    webhook_url = VALUES(webhook_url);

-- ============================================================================
-- VERIFICATION
-- ============================================================================
SELECT 'Schema loaded successfully.' AS status;
SHOW TABLES;
