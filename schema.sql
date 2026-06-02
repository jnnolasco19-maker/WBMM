-- ============================================================
-- WBMM — Web-Based Market Management System
-- General Santos City Public Market
-- schema.sql
-- ============================================================

SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS audit_logs;
DROP TABLE IF EXISTS payments;
DROP TABLE IF EXISTS vendor_stalls;
DROP TABLE IF EXISTS vendors;
DROP TABLE IF EXISTS stalls;
DROP TABLE IF EXISTS rates;
DROP TABLE IF EXISTS users;

SET FOREIGN_KEY_CHECKS = 1;

-- --------------------------------------------------------
-- USERS (market staff, collectors, supervisors, admin)
-- --------------------------------------------------------
CREATE TABLE users (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    name        VARCHAR(100)  NOT NULL,
    email       VARCHAR(100)  NOT NULL UNIQUE,
    password    VARCHAR(255)  NOT NULL,
    role        ENUM('admin','supervisor','collector','staff') NOT NULL,
    status      ENUM('active','inactive') DEFAULT 'active',
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
-- RATES (versioned — never delete old rates)
-- --------------------------------------------------------
CREATE TABLE rates (
    id                    INT AUTO_INCREMENT PRIMARY KEY,
    inside_rate_per_sqm   DECIMAL(10,2) NOT NULL,
    outside_daily_rate    DECIMAL(10,2) NOT NULL,
    outside_weekly_rate   DECIMAL(10,2) NOT NULL,
    outside_monthly_rate  DECIMAL(10,2) NOT NULL,
    ambulant_daily_rate   DECIMAL(10,2) NOT NULL,
    effective_date        DATE NOT NULL,
    created_by            INT,
    created_at            TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
-- STALLS (physical spaces — inside, outside, or ambulant)
-- --------------------------------------------------------
CREATE TABLE stalls (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    stall_code  VARCHAR(50)  NOT NULL UNIQUE,
    section     VARCHAR(100) NOT NULL,
    type        ENUM('inside','outside','ambulant') NOT NULL,
    sqm         DECIMAL(6,2) DEFAULT NULL,
    floor_level VARCHAR(20)  DEFAULT NULL,
    status      ENUM('occupied','vacant','suspended') DEFAULT 'vacant',
    notes       TEXT,
    barangay_permit_no  VARCHAR(50),
    barangay_permit_issued DATE,
    barangay_permit_expiry DATE,
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
-- VENDORS (people or businesses)
-- --------------------------------------------------------
CREATE TABLE vendors (
    id                  INT AUTO_INCREMENT PRIMARY KEY,
    vendor_no           VARCHAR(30)  NOT NULL UNIQUE,
    first_name          VARCHAR(100) NOT NULL,
    last_name           VARCHAR(100) NOT NULL,
    business_name       VARCHAR(150),
    contact             VARCHAR(20),
    address             TEXT,
    id_type             VARCHAR(50),
    id_number           VARCHAR(50),
    type                ENUM('inside','outside','ambulant') NOT NULL,
    status              ENUM('active','inactive','suspended') DEFAULT 'active',
    barangay_permit_no  VARCHAR(50),
    barangay_permit_issued DATE,
    barangay_permit_expiry DATE,
    created_at          TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
-- VENDOR-STALL ASSIGNMENTS
-- --------------------------------------------------------
CREATE TABLE vendor_stalls (
    id              INT AUTO_INCREMENT PRIMARY KEY,
    vendor_id       INT NOT NULL,
    stall_id        INT NOT NULL,
    permit_no       VARCHAR(50),
    permit_issued   DATE,
    permit_expiry   DATE,
    assigned_date   DATE NOT NULL,
    status          ENUM('active','expired','terminated') DEFAULT 'active',
    terminated_date DATE DEFAULT NULL,
    notes           TEXT,
    created_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (vendor_id) REFERENCES vendors(id) ON DELETE CASCADE,
    FOREIGN KEY (stall_id)  REFERENCES stalls(id)  ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
-- PAYMENTS (arkalaba collection records)
-- --------------------------------------------------------
CREATE TABLE payments (
    id                INT AUTO_INCREMENT PRIMARY KEY,
    reference_no      VARCHAR(50)   NOT NULL UNIQUE,
    vendor_id         INT NOT NULL,
    stall_id          INT DEFAULT NULL,
    rate_id           INT NOT NULL,
    payment_type      ENUM('daily','weekly','monthly') NOT NULL,
    sqm_charged       DECIMAL(6,2)  DEFAULT NULL,
    rate_used         DECIMAL(10,2) NOT NULL,
    computed_amount   DECIMAL(10,2) NOT NULL,
    amount_paid       DECIMAL(10,2) NOT NULL,
    period_start      DATE NOT NULL,
    period_end        DATE NOT NULL,
    collected_by      INT NOT NULL,
    payment_date      TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    notes             TEXT,
    FOREIGN KEY (vendor_id)    REFERENCES vendors(id) ON DELETE RESTRICT,
    FOREIGN KEY (stall_id)     REFERENCES stalls(id)  ON DELETE SET NULL,
    FOREIGN KEY (rate_id)      REFERENCES rates(id)   ON DELETE RESTRICT,
    FOREIGN KEY (collected_by) REFERENCES users(id)   ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
-- AUDIT LOGS
-- --------------------------------------------------------
CREATE TABLE audit_logs (
    id              INT AUTO_INCREMENT PRIMARY KEY,
    user_id         INT,
    action          VARCHAR(100),
    table_affected  VARCHAR(50),
    record_id       INT,
    details         TEXT,
    created_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- SEED DATA
-- ============================================================

-- Admin user (password: Admin@1234)
INSERT INTO users (name, email, password, role) VALUES
('Market Administrator', 'admin@wbmm.com',
 '$2y$10$PG.mjl3gdwbwKpqXKbMXs.TqwLEfbtFn4.8Me8X8vL.IxmYPFxvKm', 'admin');

-- Supervisor account (password: Admin@1234)
INSERT INTO users (name, email, password, role) VALUES
('Maria Supervisor', 'supervisor@wbmm.com',
 '$2y$10$PG.mjl3gdwbwKpqXKbMXs.TqwLEfbtFn4.8Me8X8vL.IxmYPFxvKm', 'supervisor');

-- Collector accounts (password: Admin@1234)
INSERT INTO users (name, email, password, role) VALUES
('Juan Maningil',   'collector1@wbmm.com',
 '$2y$10$PG.mjl3gdwbwKpqXKbMXs.TqwLEfbtFn4.8Me8X8vL.IxmYPFxvKm', 'collector'),
('Pedro Tigsingil', 'collector2@wbmm.com',
 '$2y$10$PG.mjl3gdwbwKpqXKbMXs.TqwLEfbtFn4.8Me8X8vL.IxmYPFxvKm', 'collector');

-- Staff account (password: Admin@1234)
INSERT INTO users (name, email, password, role) VALUES
('Ana Staff', 'staff@wbmm.com',
 '$2y$10$PG.mjl3gdwbwKpqXKbMXs.TqwLEfbtFn4.8Me8X8vL.IxmYPFxvKm', 'staff');

-- Initial rates (effective today)
INSERT INTO rates (inside_rate_per_sqm, outside_daily_rate, outside_weekly_rate,
                   outside_monthly_rate, ambulant_daily_rate, effective_date, created_by)
VALUES (45.00, 25.00, 150.00, 500.00, 15.00, CURDATE(), 1);

-- Sample stalls
INSERT INTO stalls (stall_code, section, type, sqm, floor_level, status) VALUES
('A-101',         'Dry Goods',    'inside',   6.00, 'Ground Floor', 'vacant'),
('A-102',         'Dry Goods',    'inside',   4.50, 'Ground Floor', 'vacant'),
('A-201',         'Dry Goods',    'inside',   6.00, '2nd Floor',    'vacant'),
('WM-001',        'Wet Market',   'inside',   8.00, 'Ground Floor', 'vacant'),
('WM-002',        'Wet Market',   'inside',   6.00, 'Ground Floor', 'vacant'),
('LS-001',        'Livestock',    'inside',  10.00, 'Ground Floor', 'vacant'),
('COM-001',       'Commercial',   'inside',  12.00, 'Ground Floor', 'vacant'),
('EXT-ROW-A-001', 'Outside Row A','outside', NULL,  NULL,           'vacant'),
('EXT-ROW-A-002', 'Outside Row A','outside', NULL,  NULL,           'vacant'),
('EXT-ROW-A-003', 'Outside Row A','outside', NULL,  NULL,           'vacant'),
('EXT-ROW-B-001', 'Outside Row B','outside', NULL,  NULL,           'vacant'),
('EXT-ROW-B-002', 'Outside Row B','outside', NULL,  NULL,           'vacant'),
('AMBU',          'Ambulant',     'ambulant', NULL, NULL,           'vacant');

-- Sample vendors
INSERT INTO vendors (vendor_no, first_name, last_name, business_name, contact, type, status) VALUES
('VND-2026-0001', 'Rosa',    'Dela Cruz', 'Rosa Dry Goods',    '09171234567', 'inside',   'active'),
('VND-2026-0002', 'Pedro',   'Santos',    'Santos Wet Market', '09181234567', 'inside',   'active'),
('VND-2026-0003', 'Maria',   'Reyes',     NULL,                '09191234567', 'outside',  'active'),
('VND-2026-0004', 'Jose',    'Garcia',    NULL,                '09201234567', 'ambulant', 'active');

-- Sample vendor-stall assignments
INSERT INTO vendor_stalls (vendor_id, stall_id, permit_no, permit_issued, permit_expiry, assigned_date, status) VALUES
(1, 1, 'PRM-2026-001', DATE_SUB(CURDATE(), INTERVAL 6 MONTH), DATE_ADD(CURDATE(), INTERVAL 20 DAY), DATE_SUB(CURDATE(), INTERVAL 6 MONTH), 'active'),
(2, 4, 'PRM-2026-002', DATE_SUB(CURDATE(), INTERVAL 3 MONTH), DATE_ADD(CURDATE(), INTERVAL 60 DAY), DATE_SUB(CURDATE(), INTERVAL 3 MONTH), 'active'),
(3, 8, 'PRM-2026-003', DATE_SUB(CURDATE(), INTERVAL 1 MONTH), DATE_ADD(CURDATE(), INTERVAL 90 DAY), DATE_SUB(CURDATE(), INTERVAL 1 MONTH), 'active');

UPDATE stalls SET status = 'occupied' WHERE id IN (1, 4, 8);

-- Sample payments (for dashboard chart and reports)
INSERT INTO payments (
    reference_no, vendor_id, stall_id, rate_id, payment_type,
    sqm_charged, rate_used, computed_amount, amount_paid,
    period_start, period_end, collected_by, payment_date, notes
) VALUES
(CONCAT('ARK-', DATE_FORMAT(CURDATE(), '%Y%m%d'), '-0001'), 1, 1, 1, 'monthly', 6.00, 45.00, 270.00, 270.00,
 DATE_FORMAT(CURDATE(), '%Y-%m-01'), LAST_DAY(CURDATE()), 3, NOW(), 'Sample monthly payment'),
(CONCAT('ARK-', DATE_FORMAT(CURDATE(), '%Y%m%d'), '-0002'), 2, 4, 1, 'monthly', 8.00, 45.00, 360.00, 360.00,
 DATE_FORMAT(CURDATE(), '%Y-%m-01'), LAST_DAY(CURDATE()), 3, NOW(), NULL),
(CONCAT('ARK-', DATE_FORMAT(CURDATE(), '%Y%m%d'), '-0003'), 3, 8, 1, 'weekly', NULL, 150.00, 150.00, 150.00,
 DATE_SUB(CURDATE(), INTERVAL WEEKDAY(CURDATE()) DAY),
 DATE_ADD(DATE_SUB(CURDATE(), INTERVAL WEEKDAY(CURDATE()) DAY), INTERVAL 6 DAY), 4, NOW(), NULL);
