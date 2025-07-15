DROP TABLE IF EXISTS chat_log;
DROP TABLE IF EXISTS transactions;
DROP TABLE IF EXISTS users;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL
);

CREATE TABLE transactions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    description VARCHAR(255) NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    type ENUM('income','expense') NOT NULL,
    category VARCHAR(50),
    date DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE chat_log (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    message TEXT NOT NULL,
    timestamp DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

INSERT INTO users (username) VALUES ('john');


-- USERS table (optional now, required if you enable login later)
CREATE TABLE IF NOT EXISTS users (
id INT AUTO_INCREMENT PRIMARY KEY,
username VARCHAR(50) UNIQUE NOT NULL,
password_hash VARCHAR(255) NOT NULL
);
-- TRANSACTIONS table
CREATE TABLE IF NOT EXISTS transactions (
id INT AUTO_INCREMENT PRIMARY KEY,
user_id INT NOT NULL,
description VARCHAR(255) NOT NULL,
amount DECIMAL(10,2) NOT NULL,
type ENUM('income', 'expense') NOT NULL,
category VARCHAR(50),
date DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);
-- Optional seed user for testing
INSERT IGNORE INTO users (id, username, password_hash)
VALUES (1, 'demo', '$2y$10$dummyhashdummyhashdummyhashdummyhashdummyhashdummyhashdummyhash');
-- Optional: Seed transactions for user 1
INSERT INTO transactions (user_id, description, amount, type, category, date) VALUES
(1, 'Salary', 5000.00, 'income', NULL, NOW()),
(1, 'Rent', 1800.00, 'expense', 'Utilities', NOW()),
(1, 'Groceries', 400.00, 'expense', 'Food', NOW()),
(1, 'Entertainment', 300.00, 'expense', 'Other', NOW()),
(1, 'Gas', 150.00, 'expense', 'Transport', NOW());
