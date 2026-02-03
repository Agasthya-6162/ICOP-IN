-- ============================================
-- ICOP Website - Complete Database with Security
-- Indira College of Pharmacy
-- Version: 2.0 - Production Ready with Security
-- Date: 2026-02-02
-- ============================================
-- 
-- INSTALLATION INSTRUCTIONS:
-- 1. Open phpMyAdmin
-- 2. Create a new database named 'icop_website' (or use existing)
-- 3. Select the database
-- 4. Click 'Import' tab
-- 5. Choose this file and click 'Go'
-- 
-- DEFAULT ADMIN CREDENTIALS (CHANGE IMMEDIATELY):
-- Username: admin
-- Password: admin123
-- 
-- IMPORTANT: After installation, change the default password!
-- ============================================

-- Create Database
CREATE DATABASE IF NOT EXISTS icop_website CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE icop_website;

-- ============================================
-- CORE TABLES
-- ============================================

-- ============================================
-- 1. ADMIN USERS TABLE (Enhanced with Security)
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
    password_expires_at TIMESTAMP NULL,
    force_password_change TINYINT(1) DEFAULT 0,
    failed_login_attempts INT DEFAULT 0,
    locked_until TIMESTAMP NULL,
    two_factor_enabled TINYINT(1) DEFAULT 0,
    last_password_change TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_username (username)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Default Admin User
-- Username: admin
-- Password: admin123 (CHANGE AFTER FIRST LOGIN!)
INSERT INTO admin_users (username, password, full_name, email, role, last_password_change) VALUES
('admin', '$2y$10$b6XlRF0EQHW2vmAk02UcOefbI6oKT84ywvPWuKFz.yklxNbjoGlae', 'Administrator', 'admin@icop.edu.in', 'admin', NOW());

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
-- SECURITY ENHANCEMENT TABLES
-- ============================================

-- ============================================
-- 16. TWO-FACTOR AUTHENTICATION TABLE
-- ============================================
CREATE TABLE IF NOT EXISTS two_factor_auth (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    secret_key VARCHAR(255) NOT NULL,
    backup_codes TEXT,
    is_verified TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    verified_at TIMESTAMP NULL,
    last_used TIMESTAMP NULL,
    INDEX idx_user_id (user_id),
    FOREIGN KEY (user_id) REFERENCES admin_users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 17. PASSWORD HISTORY TABLE
-- ============================================
CREATE TABLE IF NOT EXISTS password_history (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_user_id (user_id),
    INDEX idx_created_at (created_at),
    FOREIGN KEY (user_id) REFERENCES admin_users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 18. ACTIVE SESSIONS TABLE
-- ============================================
CREATE TABLE IF NOT EXISTS active_sessions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    session_token VARCHAR(255) UNIQUE NOT NULL,
    device_fingerprint VARCHAR(255),
    ip_address VARCHAR(45) NOT NULL,
    user_agent TEXT,
    last_activity TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    expires_at TIMESTAMP NOT NULL,
    is_remembered TINYINT(1) DEFAULT 0,
    INDEX idx_user_id (user_id),
    INDEX idx_session_token (session_token),
    INDEX idx_expires_at (expires_at),
    FOREIGN KEY (user_id) REFERENCES admin_users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 19. SECURITY SETTINGS TABLE
-- ============================================
CREATE TABLE IF NOT EXISTS security_settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    setting_key VARCHAR(100) UNIQUE NOT NULL,
    setting_value TEXT,
    description TEXT,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_setting_key (setting_key)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Default Security Settings
INSERT INTO security_settings (setting_key, setting_value, description) VALUES
('max_login_attempts', '5', 'Maximum failed login attempts before lockout'),
('lockout_duration', '900', 'Account lockout duration in seconds (15 minutes)'),
('session_timeout', '1800', 'Session timeout in seconds (30 minutes)'),
('password_min_length', '12', 'Minimum password length'),
('password_require_uppercase', '1', 'Require uppercase letters in password'),
('password_require_lowercase', '1', 'Require lowercase letters in password'),
('password_require_numbers', '1', 'Require numbers in password'),
('password_require_special', '1', 'Require special characters in password'),
('password_expiry_days', '90', 'Password expiration in days (0 = never)'),
('password_history_count', '5', 'Number of previous passwords to prevent reuse'),
('enable_2fa', '1', 'Enable two-factor authentication'),
('session_remember_days', '30', 'Remember me duration in days')
ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value);

-- ============================================
-- 20. SECURITY AUDIT LOG TABLE
-- ============================================
CREATE TABLE IF NOT EXISTS security_audit_log (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    event_type VARCHAR(100) NOT NULL,
    event_description TEXT,
    ip_address VARCHAR(45),
    user_agent TEXT,
    status VARCHAR(50) DEFAULT 'success',
    metadata JSON,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_user_id (user_id),
    INDEX idx_event_type (event_type),
    INDEX idx_created_at (created_at),
    INDEX idx_status (status),
    FOREIGN KEY (user_id) REFERENCES admin_users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 21. TRUSTED DEVICES TABLE
-- ============================================
CREATE TABLE IF NOT EXISTS trusted_devices (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    device_token VARCHAR(255) UNIQUE NOT NULL,
    device_name VARCHAR(255),
    device_fingerprint VARCHAR(255),
    ip_address VARCHAR(45),
    user_agent TEXT,
    last_used TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    expires_at TIMESTAMP NOT NULL,
    is_active TINYINT(1) DEFAULT 1,
    INDEX idx_user_id (user_id),
    INDEX idx_device_token (device_token),
    INDEX idx_expires_at (expires_at),
    FOREIGN KEY (user_id) REFERENCES admin_users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- STORED PROCEDURES
-- ============================================

DELIMITER //

-- Cleanup expired sessions and tokens
CREATE PROCEDURE IF NOT EXISTS cleanup_expired_sessions()
BEGIN
    -- Delete expired sessions
    DELETE FROM active_sessions WHERE expires_at < NOW();
    
    -- Delete expired trusted devices
    DELETE FROM trusted_devices WHERE expires_at < NOW() OR is_active = 0;
    
    -- Delete old login attempts (older than 30 days)
    DELETE FROM login_attempts WHERE attempt_time < DATE_SUB(NOW(), INTERVAL 30 DAY);
    
    -- Delete old security audit logs (older than 180 days)
    DELETE FROM security_audit_log WHERE created_at < DATE_SUB(NOW(), INTERVAL 180 DAY);
    
    -- Delete used password reset tokens (older than 7 days)
    DELETE FROM password_reset_tokens WHERE is_used = 1 AND created_at < DATE_SUB(NOW(), INTERVAL 7 DAY);
END //

DELIMITER ;

-- ============================================
-- TRIGGERS
-- ============================================

DELIMITER //

-- Trigger to log password changes
CREATE TRIGGER IF NOT EXISTS after_password_change
AFTER UPDATE ON admin_users
FOR EACH ROW
BEGIN
    IF OLD.password != NEW.password THEN
        -- Add to password history
        INSERT INTO password_history (user_id, password_hash)
        VALUES (NEW.id, NEW.password);
        
        -- Log security event
        INSERT INTO security_audit_log (user_id, event_type, event_description, status)
        VALUES (NEW.id, 'password_change', 'User password was changed', 'success');
        
        -- Clean up old password history (keep only last 5)
        DELETE FROM password_history 
        WHERE user_id = NEW.id 
        AND id NOT IN (
            SELECT id FROM (
                SELECT id FROM password_history 
                WHERE user_id = NEW.id 
                ORDER BY created_at DESC 
                LIMIT 5
            ) AS recent_passwords
        );
    END IF;
END //

DELIMITER ;

-- ============================================
-- DATABASE SETUP COMPLETE!
-- ============================================

SELECT 'Database installation complete!' AS Status;
SELECT COUNT(*) AS 'Total Tables Created' FROM information_schema.tables 
WHERE table_schema = 'icop_website';

-- ============================================
-- POST-INSTALLATION INSTRUCTIONS
-- ============================================
-- 
-- 1. CHANGE DEFAULT PASSWORD:
--    - Login with admin/admin123
--    - Go to Security Settings
--    - Change password immediately
-- 
-- 2. ENABLE TWO-FACTOR AUTHENTICATION:
--    - Go to Security Settings > Two-Factor Authentication
--    - Click "Enable 2FA"
--    - Scan QR code with authenticator app
--    - Save backup codes securely
-- 
-- 3. CONFIGURE SECURITY SETTINGS:
--    - Review security_settings table
--    - Adjust lockout duration, session timeout as needed
-- 
-- 4. SETUP AUTOMATED CLEANUP (Optional):
--    - Schedule cleanup_expired_sessions() to run daily
--    - Example cron: 0 2 * * * mysql -u root -p icop_website -e "CALL cleanup_expired_sessions();"
-- 
-- 5. MONITOR SECURITY:
--    - Regularly check security_audit_log table
--    - Review login_attempts for suspicious activity
--    - Monitor active_sessions for unauthorized access
-- 
-- ============================================
-- SECURITY FEATURES INCLUDED
-- ============================================
-- 
-- ✓ Two-Factor Authentication (2FA/TOTP)
-- ✓ Password Strength Validation
-- ✓ Password History (prevents reuse)
-- ✓ Account Lockout (5 attempts, 15 min)
-- ✓ Session Management (30 min timeout)
-- ✓ Device Fingerprinting
-- ✓ Security Audit Logging
-- ✓ Brute Force Protection
-- ✓ Remember Me (30 days)
-- ✓ Backup Codes for 2FA
-- 
-- ============================================
-- DATABASE STATISTICS
-- ============================================
-- Total Tables: 21
-- Foreign Keys: 8
-- Indexes: 50+
-- Stored Procedures: 1
-- Triggers: 1
-- Default Admin Account: 1 (username: admin)
-- Default Settings: 7 website + 12 security
-- 
-- For support, see SECURITY_README.md
-- Database Version: 2.0 with Security (Feb 2026)
-- ============================================
