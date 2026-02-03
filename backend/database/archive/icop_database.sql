-- ============================================
-- ICOP Website Complete Database
-- Indira College of Pharmacy
-- MySQL/MariaDB Database Schema
-- Version: 1.0.0
-- Date: 2026-01-29
-- ============================================

-- Create Database
CREATE DATABASE IF NOT EXISTS icop_website CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE icop_website;

-- ============================================
-- 1. BANNERS TABLE (Homepage Slider)
-- ============================================
CREATE TABLE IF NOT EXISTS banners (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    image_path VARCHAR(255) NOT NULL,
    link_url VARCHAR(500),
    display_order INT DEFAULT 0,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_active_order (is_active, display_order)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Sample Banner Data
INSERT INTO banners (title, description, image_path, link_url, display_order, is_active) VALUES
('Welcome to Indira College of Pharmacy', 'Shaping Future Pharmacists Since 1994', 'images/banners/banner1.jpg', 'about.html', 1, 1),
('Admissions Open 2026-27', 'Apply Now for B.Pharm, M.Pharm, D.Pharm & Pharm.D', 'images/banners/banner2.jpg', 'apply-online-enhanced.html', 2, 1),
('NAAC A Grade Accredited', 'Excellence in Pharmaceutical Education', 'images/banners/banner3.jpg', 'about.html', 3, 1);

-- ============================================
-- 2. GALLERY TABLE (Photo Gallery)
-- ============================================
CREATE TABLE IF NOT EXISTS gallery (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    image_path VARCHAR(255) NOT NULL,
    category VARCHAR(100) DEFAULT 'Other',
    display_order INT DEFAULT 0,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_category (category),
    INDEX idx_active_order (is_active, display_order)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Sample Gallery Data
INSERT INTO gallery (title, description, image_path, category, display_order, is_active) VALUES
('Campus View', 'Beautiful ICOP Campus', 'images/gallery/campus1.jpg', 'Campus', 1, 1),
('Pharmacy Lab', 'State-of-the-art Pharmacy Laboratory', 'images/gallery/lab1.jpg', 'Labs & Facilities', 2, 1),
('Annual Day 2025', 'Students performing at Annual Function', 'images/gallery/event1.jpg', 'Events', 3, 1),
('Library', 'Well-stocked Central Library', 'images/gallery/library1.jpg', 'Campus', 4, 1);

-- ============================================
-- 3. NOTICES TABLE (Announcements)
-- ============================================
CREATE TABLE IF NOT EXISTS notices (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(500) NOT NULL,
    content TEXT NOT NULL,
    category VARCHAR(100) DEFAULT 'General',
    attachment_path VARCHAR(255),
    is_important TINYINT(1) DEFAULT 0,
    is_active TINYINT(1) DEFAULT 1,
    publish_date DATE NOT NULL,
    expiry_date DATE,
    views INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_category (category),
    INDEX idx_publish_date (publish_date),
    INDEX idx_active_important (is_active, is_important)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Sample Notice Data
INSERT INTO notices (title, content, category, is_important, is_active, publish_date, expiry_date) VALUES
('Admission Notice 2026-27', 'Applications are invited for admission to B.Pharm, M.Pharm, D.Pharm and Pharm.D courses for the academic year 2026-27. Last date: March 31, 2026', 'Admission', 1, 1, '2026-01-15', '2026-03-31'),
('Semester 2 Examination Schedule', 'The semester 2 examinations will commence from February 15, 2026. Students are advised to check the detailed timetable on the notice board.', 'Examination', 1, 1, '2026-01-20', '2026-02-28'),
('Workshop on Drug Discovery', 'A 3-day workshop on Modern Drug Discovery Techniques will be conducted from Feb 10-12, 2026. Interested students may register with the department.', 'Event', 0, 1, '2026-01-25', '2026-02-09');

-- ============================================
-- 4. RESULTS TABLE (Examination Results)
-- ============================================
CREATE TABLE IF NOT EXISTS results (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(500) NOT NULL,
    program VARCHAR(100) NOT NULL,
    semester VARCHAR(50),
    exam_type VARCHAR(100) DEFAULT 'Regular',
    result_file VARCHAR(255) NOT NULL,
    publish_date DATE NOT NULL,
    is_active TINYINT(1) DEFAULT 1,
    downloads INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_program (program),
    INDEX idx_publish_date (publish_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Sample Results Data
INSERT INTO results (title, program, semester, exam_type, result_file, publish_date, is_active) VALUES
('B.Pharm Semester 1 Results - December 2025', 'B.Pharm', 'Semester 1', 'Regular', 'results/bpharm_sem1_dec2025.pdf', '2026-01-10', 1),
('D.Pharm First Year Results - December 2025', 'D.Pharm', 'First Year', 'Regular', 'results/dpharm_fy_dec2025.pdf', '2026-01-12', 1),
('M.Pharm Semester 3 Results - December 2025', 'M.Pharm', 'Semester 3', 'Regular', 'results/mpharm_sem3_dec2025.pdf', '2026-01-15', 1);

-- ============================================
-- 5. ADMISSION APPLICATIONS TABLE
-- ============================================
CREATE TABLE IF NOT EXISTS admission_applications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    application_no VARCHAR(50) UNIQUE NOT NULL,
    
    -- Personal Details
    course VARCHAR(100) NOT NULL,
    full_name VARCHAR(255) NOT NULL,
    dob DATE NOT NULL,
    gender VARCHAR(20) NOT NULL,
    nationality VARCHAR(100) DEFAULT 'Indian',
    religion VARCHAR(100),
    category VARCHAR(50) NOT NULL,
    blood_group VARCHAR(10),
    aadhaar VARCHAR(12) NOT NULL,
    abc_id VARCHAR(50),
    
    -- Contact Details
    mobile VARCHAR(10) NOT NULL,
    email VARCHAR(255) NOT NULL,
    address TEXT NOT NULL,
    city VARCHAR(100) NOT NULL,
    district VARCHAR(100) NOT NULL,
    state VARCHAR(100) NOT NULL,
    pincode VARCHAR(6) NOT NULL,
    
    -- Parent Details
    father_name VARCHAR(255) NOT NULL,
    father_occupation VARCHAR(255),
    father_income VARCHAR(50),
    father_mobile VARCHAR(10),
    father_email VARCHAR(255),
    mother_name VARCHAR(255) NOT NULL,
    mother_occupation VARCHAR(255),
    mother_mobile VARCHAR(10),
    guardian_name VARCHAR(255),
    
    -- Academic Details - SSC
    ssc_board VARCHAR(255),
    ssc_year INT,
    ssc_percentage DECIMAL(5,2),
    ssc_seat_no VARCHAR(50),
    
    -- Academic Details - HSC
    hsc_stream VARCHAR(100),
    hsc_year INT,
    hsc_percentage DECIMAL(5,2),
    pcb_marks DECIMAL(5,2),
    
    -- Entrance Exam
    entrance_exam VARCHAR(100),
    entrance_score DECIMAL(8,2),
    entrance_roll_no VARCHAR(50),
    entrance_rank INT,
    
    -- Document Paths
    photo_path VARCHAR(255),
    signature_path VARCHAR(255),
    aadhaar_doc_path VARCHAR(255),
    ssc_doc_path VARCHAR(255),
    hsc_doc_path VARCHAR(255),
    lc_doc_path VARCHAR(255),
    
    -- Status
    status VARCHAR(50) DEFAULT 'Pending',
    admission_granted TINYINT(1) DEFAULT 0,
    fee_paid TINYINT(1) DEFAULT 0,
    documents_verified TINYINT(1) DEFAULT 0,
    
    -- Timestamps
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_application_no (application_no),
    INDEX idx_course (course),
    INDEX idx_status (status),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 6. CONTACT FORM SUBMISSIONS
-- ============================================
CREATE TABLE IF NOT EXISTS contact_submissions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    phone VARCHAR(20),
    inquiry_type VARCHAR(100),
    message TEXT NOT NULL,
    status VARCHAR(50) DEFAULT 'New',
    responded_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_status (status),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 7. STUDENT FEEDBACK
-- ============================================
CREATE TABLE IF NOT EXISTS student_feedback (
    id INT AUTO_INCREMENT PRIMARY KEY,
    category VARCHAR(100),
    subject VARCHAR(500),
    message TEXT NOT NULL,
    is_anonymous TINYINT(1) DEFAULT 1,
    contact_info VARCHAR(255),
    status VARCHAR(50) DEFAULT 'New',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_category (category),
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 8. ADMIN USERS TABLE
-- ============================================
CREATE TABLE IF NOT EXISTS admin_users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    role VARCHAR(50) DEFAULT 'admin',
    is_active TINYINT(1) DEFAULT 1,
    last_login TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_username (username)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Default Admin User (Password: admin123)
INSERT INTO admin_users (username, password, full_name, email, role) VALUES
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Administrator', 'admin@icop.edu.in', 'admin');

-- ============================================
-- 9. ACTIVITY LOG TABLE
-- ============================================
CREATE TABLE IF NOT EXISTS activity_log (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    action VARCHAR(255) NOT NULL,
    table_name VARCHAR(100),
    record_id INT,
    ip_address VARCHAR(45),
    user_agent TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_user_id (user_id),
    INDEX idx_created_at (created_at),
    FOREIGN KEY (user_id) REFERENCES admin_users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 10. WEBSITE SETTINGS TABLE
-- ============================================
CREATE TABLE IF NOT EXISTS website_settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    setting_key VARCHAR(100) UNIQUE NOT NULL,
    setting_value TEXT,
    setting_type VARCHAR(50) DEFAULT 'text',
    description TEXT,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_setting_key (setting_key)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Default Settings
INSERT INTO website_settings (setting_key, setting_value, setting_type, description) VALUES
('college_name', 'Indira College of Pharmacy', 'text', 'College Name'),
('college_email', 'info@sssicop.org', 'email', 'College Email'),
('college_phone', '+91-20-XXXX-XXXX', 'text', 'College Phone'),
('college_address', 'Vishnupuri Nanded 431606', 'text', 'College Address'),
('admission_open', '1', 'boolean', 'Are admissions currently open?'),
('current_academic_year', '2026-27', 'text', 'Current Academic Year'),
('maintenance_mode', '0', 'boolean', 'Website Maintenance Mode');

-- ============================================
-- Database Creation Complete!
-- ============================================
-- Total Tables: 10
-- 1. banners - Homepage slider content
-- 2. gallery - Photo gallery images
-- 3. notices - Announcements and circulars
-- 4. results - Examination results
-- 5. admission_applications - Student admission forms
-- 6. contact_submissions - Contact form data
-- 7. student_feedback - Anonymous feedback
-- 8. admin_users - Admin panel users
-- 9. activity_log - Admin activity tracking
-- 10. website_settings - Site configuration
-- ============================================
