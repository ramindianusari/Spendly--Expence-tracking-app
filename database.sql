
-- Expense Tracker — Database Schema
-- Run this in phpMyAdmin or MySQL CLI before starting the app

CREATE DATABASE IF NOT EXISTS expense_tracker
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE expense_tracker;

-- Users table
CREATE TABLE IF NOT EXISTS users (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name        VARCHAR(100) NOT NULL,
    email       VARCHAR(150) NOT NULL UNIQUE,
    password    VARCHAR(255) NOT NULL,          -- bcrypt hash
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Transactions table
CREATE TABLE IF NOT EXISTS transactions (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id     INT UNSIGNED NOT NULL,
    type        ENUM('income', 'expense') NOT NULL,
    category    VARCHAR(100) NOT NULL,
    amount      DECIMAL(12, 2) NOT NULL,
    note        TEXT,
    txn_date    DATE NOT NULL,
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user_date (user_id, txn_date)
) ENGINE=InnoDB;


-- Demo seed data  (password = "password123" → bcrypt)

-- Demo password: password123
INSERT IGNORE INTO users (name, email, password) VALUES
(
    'Rahul',
    'rahul@example.com',
    '$2y$12$CkKFZiKgYFgxpZyUxFrNC.BMcJhdEfOfYtk9IVYXjoWjqqfeKpp2a'
);

-- Seed transactions for user id = 1
INSERT IGNORE INTO transactions (user_id, type, category, amount, note, txn_date) VALUES
(1, 'income',  'Salary',    5000.00,  'Monthly salary',      '2026-09-03'),
(1, 'expense', 'Food',       300.00,  'Lunch',               '2026-09-03'),
(1, 'income',  'Salary',   45000.00,  'September salary',    '2026-09-01'),
(1, 'income',  'Freelance',15000.00,  'Web project payment', '2026-09-05'),
(1, 'expense', 'Food',      4200.00,  'Groceries',           '2026-09-02'),
(1, 'expense', 'Transport', 3000.00,  'Cab / petrol',        '2026-09-01');
