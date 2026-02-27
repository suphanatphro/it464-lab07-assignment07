-- 0. สร้างและเลือกฐานข้อมูลให้ตรงกับชื่อ DB_NAME ในไฟล์ .env
CREATE DATABASE IF NOT EXISTS suphanat_db;
USE suphanat_db;

-- 1. สร้างตาราง (เหมือนเดิม)
CREATE TABLE IF NOT EXISTS `students` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `student_id` VARCHAR(15) NOT NULL,
    `full_name` VARCHAR(100) NOT NULL,
    `username` VARCHAR(50) NOT NULL,
    `email` VARCHAR(100),
    `status` ENUM('Submitted', 'Pending', 'In Progress') DEFAULT 'Pending',
    `submitted_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 2. เพิ่มข้อมูล
INSERT INTO `students` (`student_id`, `full_name`, `username`, `email`, `status`)
VALUES 
('6601000100', 'นายสมชาย ใจดี', 'somchai-dev', 'somchai.j@mail.com', 'Submitted'),
('6601000200', 'นางสาวสมหญิง รักเรียน', 'somying-it', 'somying.r@mail.com', 'In Progress'),
-- ใส่ข้อมูลจริงของคุณที่บรรทัดนี้ --
('1660703552', 'ศุภณัฐ พรหมวงษ์', 'suphanat_web', 'suphanat.phro@bumail.net', 'Submitted');