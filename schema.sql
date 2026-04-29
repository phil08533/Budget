CREATE DATABASE IF NOT EXISTS futureworth;
USE futureworth;

CREATE TABLE IF NOT EXISTS users (
  user_id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(80) NOT NULL,
  email VARCHAR(255) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL
);

CREATE TABLE IF NOT EXISTS income (
  income_id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  source_name VARCHAR(120) NOT NULL,
  amount DECIMAL(10,2) NOT NULL,
  frequency VARCHAR(20) NOT NULL DEFAULT 'monthly',
  FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS expenses (
  expense_id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  category VARCHAR(120) NOT NULL,
  amount DECIMAL(10,2) NOT NULL,
  date DATE NOT NULL,
  FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS scenarios (
  scenario_id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  type ENUM('save', 'invest', 'hybrid') NOT NULL DEFAULT 'save',
  monthly_amount DECIMAL(10,2) NOT NULL,
  duration_months INT NOT NULL,
  expected_return_rate DECIMAL(5,2) NOT NULL DEFAULT 7.00,
  FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS budget_scenarios (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  scenario_id INT NOT NULL,
  saved_name VARCHAR(120) NOT NULL,
  FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
  FOREIGN KEY (scenario_id) REFERENCES scenarios(scenario_id) ON DELETE CASCADE
);

INSERT INTO users (username, email, password_hash)
VALUES ('demo', 'demo@example.com', '$2y$10$ABCDEFGHIJKLMNOPQRSTU')
ON DUPLICATE KEY UPDATE username = VALUES(username);
