-- TIER 3: DATABASE INITIALIZATION SCRIPT

-- Users table for Admin Authentication (No IP restrictions)
CREATE TABLE IF NOT EXISTS users (
    id INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Enhanced Student table with new fields
CREATE TABLE IF NOT EXISTS student (
    ID INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    admin_id INT(11) NOT NULL,
    Name VARCHAR(255) NOT NULL,
    Email VARCHAR(255) NOT NULL,
    Age INT(3) NOT NULL,
    profile_photo VARCHAR(255),
    phone VARCHAR(20),
    address TEXT,
    enrollment_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    status ENUM('active', 'inactive', 'graduated') DEFAULT 'active',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (admin_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Performance indexes for Name and Email
CREATE INDEX idx_student_name ON student(Name);
CREATE INDEX idx_student_email ON student(Email);
CREATE INDEX idx_student_status ON student(status);
CREATE INDEX idx_student_admin ON student(admin_id);

-- Sample students will be created by individual admins
-- No default students inserted
