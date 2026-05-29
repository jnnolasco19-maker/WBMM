-- Web-Based Market Management System (WBMM) Database Schema
-- General Santos City Public Market

CREATE DATABASE IF NOT EXISTS `wbmm_db` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `wbmm_db`;

SET FOREIGN_KEY_CHECKS = 0;

-- Drop old tables
DROP TABLE IF EXISTS `audit_logs`;
DROP TABLE IF EXISTS `payments`;
DROP TABLE IF EXISTS `records`;
DROP TABLE IF EXISTS `stalls`;
DROP TABLE IF EXISTS `role_permissions`;
DROP TABLE IF EXISTS `permissions`;
DROP TABLE IF EXISTS `roles`;
DROP TABLE IF EXISTS `password_resets`;
DROP TABLE IF EXISTS `users`;
DROP TABLE IF EXISTS `vendors`;

-- 1. USERS TABLE
CREATE TABLE `users` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(100) NOT NULL,
  `email` VARCHAR(100) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL,
  `role` ENUM('admin', 'staff') NOT NULL DEFAULT 'staff',
  `status` ENUM('active', 'inactive') NOT NULL DEFAULT 'active',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 2. VENDORS TABLE
CREATE TABLE `vendors` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(100) NOT NULL,
  `stall_number` VARCHAR(50) NOT NULL UNIQUE,
  `section` ENUM('Dry Goods', 'Wet Market', 'Livestock', 'Commercial') NOT NULL,
  `contact` VARCHAR(20) DEFAULT NULL,
  `permit_expiry` DATE NOT NULL,
  `status` ENUM('active', 'inactive') NOT NULL DEFAULT 'active',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 3. PAYMENTS TABLE (ARKALABA)
CREATE TABLE `payments` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `vendor_id` INT NOT NULL,
  `amount` DECIMAL(10,2) NOT NULL,
  `payment_type` ENUM('daily', 'weekly', 'monthly') NOT NULL,
  `period_start` DATE NOT NULL,
  `period_end` DATE NOT NULL,
  `reference_no` VARCHAR(50) NOT NULL UNIQUE,
  `collected_by` INT DEFAULT NULL,
  `notes` TEXT DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`vendor_id`) REFERENCES `vendors`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`collected_by`) REFERENCES `users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 4. AUDIT LOGS TABLE
CREATE TABLE `audit_logs` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT DEFAULT NULL,
  `action` VARCHAR(255) NOT NULL,
  `table_affected` VARCHAR(50) NOT NULL,
  `record_id` INT DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

SET FOREIGN_KEY_CHECKS = 1;

-- SEED DATA
-- Default Admin: admin@wbmm.com / Admin@1234
INSERT INTO `users` (`name`, `email`, `password`, `role`, `status`) VALUES
('Administrator', 'admin@wbmm.com', '$2y$10$ZmJFwDl6tMgalCPx9btd9O988KA9CnbAtisxBN5AmBUO0cgY0Mc/6', 'admin', 'active');

-- Default Staff: staff@wbmm.com / Staff@1234
INSERT INTO `users` (`name`, `email`, `password`, `role`, `status`) VALUES
('Staff Worker', 'staff@wbmm.com', '$2y$10$3jLJx2QwjmfNBV3sxSqmLuDMBG7bA/SO0AvoALKqea8el9rzbqX4a', 'staff', 'active');

-- Seed initial mock vendors
INSERT INTO `vendors` (`name`, `stall_number`, `section`, `contact`, `permit_expiry`, `status`) VALUES
('Juan Dela Cruz', 'STALL-001', 'Wet Market', '09171234567', '2026-12-31', 'active'),
('Maria Clara', 'STALL-002', 'Dry Goods', '09187654321', '2025-06-15', 'active'),
('Pedro Penduko', 'STALL-003', 'Livestock', '2024-05-10', '2024-05-10', 'active'),
('Gabriela Silang', 'STALL-004', 'Commercial', '09201112222', '2026-08-20', 'active');

-- Seed initial mock payments
INSERT INTO `payments` (`vendor_id`, `amount`, `payment_type`, `period_start`, `period_end`, `reference_no`, `collected_by`, `notes`) VALUES
(1, 150.00, 'daily', '2026-05-29', '2026-05-29', 'ARK-20260529-8742', 2, 'Regular daily collection'),
(2, 1050.00, 'weekly', '2026-05-22', '2026-05-28', 'ARK-20260528-9124', 2, 'Weekly stall payment');
