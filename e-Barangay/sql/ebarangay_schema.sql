-- sql/ebarangay_schema.sql
CREATE DATABASE IF NOT EXISTS e_barangay CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE e_barangay;

CREATE TABLE IF NOT EXISTS users (
  user_id INT AUTO_INCREMENT PRIMARY KEY,
  role ENUM('admin','secretary','resident') NOT NULL DEFAULT 'resident',
  fullname VARCHAR(255) NOT NULL,
  email VARCHAR(255) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  contact_no VARCHAR(50),
  address VARCHAR(255),
  date_registered TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS residents (
  resident_id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  birthdate DATE NULL,
  gender VARCHAR(10) NULL,
  civil_status VARCHAR(50) NULL,
  nationality VARCHAR(50) NULL,
  FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Optional: tables for clearance, announcements, cases (basic)
CREATE TABLE IF NOT EXISTS clearance_requests (
  request_id INT AUTO_INCREMENT PRIMARY KEY,
  resident_id INT NOT NULL,
  purpose TEXT,
  request_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  status ENUM('pending','approved','rejected','released') DEFAULT 'pending',
  approved_by INT NULL,
  release_date DATETIME NULL,
  FOREIGN KEY (resident_id) REFERENCES residents(resident_id),
  FOREIGN KEY (approved_by) REFERENCES users(user_id)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS announcements (
  announcement_id INT AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(255) NOT NULL,
  content TEXT NOT NULL,
  posted_by INT NOT NULL,
  posted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (posted_by) REFERENCES users(user_id)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS cases (
  case_id INT AUTO_INCREMENT PRIMARY KEY,
  resident_id INT NOT NULL,
  case_type VARCHAR(255),
  case_description TEXT,
  case_date DATE,
  status ENUM('open','under investigation','closed') DEFAULT 'open',
  handled_by INT NULL,
  FOREIGN KEY (resident_id) REFERENCES residents(resident_id),
  FOREIGN KEY (handled_by) REFERENCES users(user_id)
) ENGINE=InnoDB;
