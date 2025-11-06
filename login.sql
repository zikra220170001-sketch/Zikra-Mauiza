CREATE DATABASE IF NOT EXISTS login;
USE login;

-- Tabel users
CREATE TABLE IF NOT EXISTS users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(50) UNIQUE NOT NULL,
  password VARCHAR(255) NOT NULL,
  email VARCHAR(100) NOT NULL,
  login_attempts INT DEFAULT 0,
  blocked_until DATETIME NULL,
  last_attempt DATETIME NULL
);

-- Tabel log aktivitas login
CREATE TABLE IF NOT EXISTS login_logs (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(50),
  status ENUM('berhasil','gagal') NOT NULL,
  waktu DATETIME DEFAULT CURRENT_TIMESTAMP,
  ip_address VARCHAR(50)
);
