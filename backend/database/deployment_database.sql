-- ============================================
-- ICOP Website - Production Deployment Database
-- Indira College of Pharmacy
-- Version: 1.0 Production
-- Date: 2026-02-01
-- CLEAN PRODUCTION-READY SQL FILE
-- ============================================

-- Create Database
CREATE DATABASE IF NOT EXISTS icop_website CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE icop_website;

-- ============================================
-- 1. ADMIN USERS TABLE
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

-- Default Admin User
-- Username: admin
-- Password: admin123 (CHANGE AFTER FIRST LOGIN!)
INSERT INTO admin_users (username, password, full_name, email, role) VALUES
('admin', '$2y$10$b6XlRF0EQHW2vmAk02UcOefbI6oKT84ywvPWuKFz.yklxNbjoGlae', 'Administrator', 'admin@icop.edu.in', 'admin');

-- ============================================
-- 2. BANNERS TABLE (Homepage Slider)
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

-- ============================================
-- 3. GALLERY TABLE (Photo Gallery)
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

-- ============================================
-- 4. NOTICES TABLE (Announcements)
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

-- ============================================
-- 5. RESULTS TABLE (Examination Results)
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

-- ============================================
-- 6. ADMISSION APPLICATIONS TABLE
-- ============================================
CREATE TABLE IF NOT EXISTS admission_applications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    application_no VARCHAR(50) UNIQUE NOT NULL,
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
    mobile VARCHAR(10) NOT NULL,
    email VARCHAR(255) NOT NULL,
    address TEXT NOT NULL,
    city VARCHAR(100) NOT NULL,
    district VARCHAR(100) NOT NULL,
    state VARCHAR(100) NOT NULL,
    pincode VARCHAR(6) NOT NULL,
    father_name VARCHAR(255) NOT NULL,
    father_occupation VARCHAR(255),
    father_income VARCHAR(50),
    father_mobile VARCHAR(10),
    father_email VARCHAR(255),
    mother_name VARCHAR(255) NOT NULL,
    mother_occupation VARCHAR(255),
    mother_mobile VARCHAR(10),
    guardian_name VARCHAR(255),
    ssc_board VARCHAR(255),
    ssc_year INT,
    ssc_percentage DECIMAL(5,2),
    ssc_seat_no VARCHAR(50),
    hsc_stream VARCHAR(100),
    hsc_year INT,
    hsc_percentage DECIMAL(5,2),
    pcb_marks DECIMAL(5,2),
    entrance_exam VARCHAR(100),
    entrance_score DECIMAL(8,2),
    entrance_roll_no VARCHAR(50),
    entrance_rank INT,
    photo_path VARCHAR(255),
    signature_path VARCHAR(255),
    aadhaar_doc_path VARCHAR(255),
    ssc_doc_path VARCHAR(255),
    hsc_doc_path VARCHAR(255),
    lc_doc_path VARCHAR(255),
    status VARCHAR(50) DEFAULT 'Pending',
    admission_granted TINYINT(1) DEFAULT 0,
    fee_paid TINYINT(1) DEFAULT 0,
    documents_verified TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_application_no (application_no),
    INDEX idx_course (course),
    INDEX idx_status (status),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 7. CONTACT FORM SUBMISSIONS
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
-- 8. STUDENT FEEDBACK
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
-- 11. SYLLABUS TABLE
-- ============================================
CREATE TABLE IF NOT EXISTS syllabus (
    id INT AUTO_INCREMENT PRIMARY KEY,
    course_name VARCHAR(100) NOT NULL,
    semester VARCHAR(50) NOT NULL,
    subject_name VARCHAR(255) NOT NULL,
    file_path VARCHAR(255) NOT NULL,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_course (course_name),
    INDEX idx_semester (semester)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 12. EXAMINATIONS TABLE
-- ============================================
CREATE TABLE IF NOT EXISTS examinations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    exam_date DATE NOT NULL,
    description TEXT,
    file_path VARCHAR(255),
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_exam_date (exam_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 13. LOGIN ATTEMPTS TABLE (Security)
-- ============================================
CREATE TABLE IF NOT EXISTS login_attempts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ip_address VARCHAR(45) NOT NULL,
    attempt_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    is_success TINYINT(1) DEFAULT 0,
    INDEX idx_ip_time (ip_address, attempt_time)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 14. LATEST UPDATES TABLE (News Ticker)
-- ============================================
CREATE TABLE IF NOT EXISTS latest_updates (
    id INT AUTO_INCREMENT PRIMARY KEY,
    content TEXT NOT NULL,
    link_url VARCHAR(500),
    display_order INT DEFAULT 0,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_active_order (is_active, display_order)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 15. PASSWORD RESET TOKENS TABLE
-- ============================================
CREATE TABLE IF NOT EXISTS password_reset_tokens (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    token VARCHAR(255) UNIQUE NOT NULL,
    expires_at TIMESTAMP NOT NULL,
    is_used TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_token (token),
    INDEX idx_user_id (user_id),
    FOREIGN KEY (user_id) REFERENCES admin_users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- DATABASE SETUP COMPLETE!
-- ============================================

-- IMPORTANT POST-INSTALLATION STEPS:
-- 1. Change admin password from default 'admin123'
-- 2. Update website_settings with your actual contact information
-- 3. Add your first banner images via admin panel
-- 4. Create your first notice/announcement
-- 5. Upload gallery images
-- 6. Test all forms (admissions, contact, feedback)

-- SECURITY REMINDERS:
-- - Create a dedicated MySQL user (not root) for this database
-- - Use a strong password for the database user
-- - Set DEBUG_MODE to false in backend/config.php for production
-- - Enable HTTPS on your web server
-- - Regularly backup this database

-- DATABASE STATISTICS:
-- Total Tables: 15
-- Foreign Keys: 2 (activity_log, password_reset_tokens)
-- Indexes: 25+ for optimized queries
-- Default Admin Account: 1 (username: admin)
-- Default Settings: 7

-- For support or questions, contact the development team.
-- Website: Indira College of Pharmacy
-- Database Version: 1.0 Production (Feb 2026)
