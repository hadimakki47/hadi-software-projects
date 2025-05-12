-- volunteer_connect.sql

CREATE DATABASE IF NOT EXISTS volunteer_connect
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;
USE volunteer_connect;

CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  first_name VARCHAR(50) NOT NULL,
  last_name  VARCHAR(50) NOT NULL,
  email VARCHAR(100) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE opportunities (
  id INT AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(150) NOT NULL,
  category VARCHAR(50) NOT NULL,
  location VARCHAR(100) NOT NULL,
  description TEXT NOT NULL,
  date DATE NOT NULL,
  volunteers_needed INT NOT NULL,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE favorites (
  user_id INT NOT NULL,
  opp_id  INT NOT NULL,
  PRIMARY KEY(user_id, opp_id),
  FOREIGN KEY(user_id) REFERENCES users(id),
  FOREIGN KEY(opp_id)  REFERENCES opportunities(id)
);

CREATE TABLE contacts (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  email VARCHAR(100) NOT NULL,
  subject VARCHAR(100) NOT NULL,
  message TEXT NOT NULL,
  sent_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE subscribers (
  id INT AUTO_INCREMENT PRIMARY KEY,
  email VARCHAR(100) NOT NULL UNIQUE,
  subscribed_at DATETIME DEFAULT CURRENT_TIMESTAMP
);
