CREATE DATABASE women_safety;
USE women_safety;

CREATE TABLE users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100),
    email VARCHAR(100) UNIQUE,
    phone VARCHAR(15),
    password VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE admin (
    admin_id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50),
    password VARCHAR(255)
);

INSERT INTO admin (username, password)
VALUES ('admin', '$2y$10$Q6zKzqHjQZ2nB8c8QxZlQe6t9xvMZzZQ9m6n3zFz1M8yKxJz9b5a');

CREATE TABLE complaints (
    complaint_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    incident_type VARCHAR(100),
    description TEXT,
    location VARCHAR(255),
    evidence VARCHAR(255),
    status ENUM('Pending','In Progress','Resolved') DEFAULT 'Pending',
    reported_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE emergency_sos (
    sos_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    location VARCHAR(255),
    message TEXT,
    sent_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE non_reg_sos (
    sos_id INT AUTO_INCREMENT PRIMARY KEY,
    location VARCHAR(255),
    message TEXT,
    sent_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
