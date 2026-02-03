-- ============================================
-- ICOP Website - Security Enhancement Schema
-- Indira College of Pharmacy
-- Version: 2.0 Security Update
-- Date: 2026-02-02
-- ============================================

USE icop_website;

-- ============================================
-- 1. MODIFY ADMIN_USERS TABLE
-- Add security-related fields
-- ============================================

ALTER TABLE admin_users
ADD COLUMN IF NOT EXISTS password_expires_at TIMESTAMP NULL AFTER password,
ADD COLUMN IF NOT EXISTS force_password_change TINYINT(1) DEFAULT 0 AFTER password_expires_at,
ADD COLUMN IF NOT EXISTS failed_login_attempts INT DEFAULT 0 AFTER force_password_change,
ADD COLUMN IF NOT EXISTS locked_until TIMESTAMP NULL AFTER failed_login_attempts,
ADD COLUMN IF NOT EXISTS two_factor_enabled TINYINT(1) DEFAULT 0 AFTER locked_until,
ADD COLUMN IF NOT EXISTS last_password_change TIMESTAMP NULL AFTER two_factor_enabled;

-- ============================================
-- 2. TWO-FACTOR AUTHENTICATION TABLE
-- Store TOTP secrets and backup codes
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
-- 3. PASSWORD HISTORY TABLE
-- Prevent password reuse
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
-- 4. ACTIVE SESSIONS TABLE
-- Track active user sessions
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
-- 5. SECURITY SETTINGS TABLE
-- Store security configuration
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
-- 6. SECURITY AUDIT LOG TABLE
-- Enhanced activity logging for security events
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
-- 7. TRUSTED DEVICES TABLE
-- Store trusted devices for "Remember Me" functionality
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
-- 8. CREATE CLEANUP PROCEDURES
-- Automatic cleanup of expired sessions and tokens
-- ============================================

DELIMITER //

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
-- 9. CREATE SECURITY EVENT TRIGGERS
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
-- SECURITY SCHEMA SETUP COMPLETE!
-- ============================================

-- Post-Installation Notes:
-- 1. Run this script on your existing database
-- 2. Existing admin users will need to set up 2FA on next login
-- 3. Schedule cleanup_expired_sessions() to run daily via cron
-- 4. Review security_settings and adjust as needed
-- 5. Monitor security_audit_log for suspicious activity

-- To schedule automatic cleanup (add to cron):
-- 0 2 * * * mysql -u root -p icop_website -e "CALL cleanup_expired_sessions();"

SELECT 'Security schema installation complete!' AS Status;
SELECT COUNT(*) AS 'New Tables Created' FROM information_schema.tables 
WHERE table_schema = 'icop_website' 
AND table_name IN ('two_factor_auth', 'password_history', 'active_sessions', 'security_settings', 'security_audit_log', 'trusted_devices');
