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
    Name VARCHAR(255) NOT NULL,
    Email VARCHAR(255) NOT NULL UNIQUE,
    Age INT(3) NOT NULL,
    profile_photo VARCHAR(255),
    phone VARCHAR(20),
    address TEXT,
    enrollment_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    status ENUM('active', 'inactive', 'graduated') DEFAULT 'active',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Performance indexes for Name and Email
CREATE INDEX idx_student_name ON student(Name);
CREATE INDEX idx_student_email ON student(Email);
CREATE INDEX idx_student_status ON student(status);

-- Sample students with Neo-Brutalist Pop vibes
INSERT INTO student (Name, Email, Age, phone, address, status) VALUES 
('Zara Khan', 'zara.khan@amu.ac.in', 21, '+91-9876543210', 'Flat 12, Zakir Nagar, New Delhi', 'active'),
('Aman Gupta', 'aman.gupta@vit.ac.in', 22, '+91-9876543211', 'Room 203, Boys Hostel, Vellore', 'active'),
('Sneha Joshi', 'sneha.joshi@mitwpu.edu.in', 20, '+91-9876543212', 'Karve Nagar, Pune, Maharashtra', 'graduated'),
('Karthik Reddy', 'karthik.reddy@cbit.ac.in', 19, '+91-9876543213', 'Madhapur, Hyderabad, Telangana', 'active'),
('Nisha Patel', 'nisha.patel@gtu.ac.in', 23, '+91-9876543214', 'Satellite Road, Ahmedabad, Gujarat', 'inactive');
