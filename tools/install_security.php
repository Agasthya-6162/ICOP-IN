<?php
/**
 * Simple Security Schema Installer
 * Installs security tables without complex delimiters
 */

require_once __DIR__ . '/config.php';

echo "\n";
echo "╔════════════════════════════════════════════════════════════╗\n";
echo "║  Installing Security Schema                                ║\n";
echo "╚════════════════════════════════════════════════════════════╝\n";
echo "\n";

try {
    $db = Database::getInstance()->getConnection();

    $tables = [];

    // Check which tables already exist
    echo "Checking existing tables...\n";
    $existingTables = [];
    $stmt = $db->query("SHOW TABLES");
    while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
        $existingTables[] = $row[0];
    }

    // Two-Factor Authentication Table
    if (!in_array('two_factor_auth', $existingTables)) {
        echo "Creating two_factor_auth table...\n";
        $db->exec("
            CREATE TABLE two_factor_auth (
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
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
        echo "✓ Created two_factor_auth\n";
    } else {
        echo "⚠ two_factor_auth already exists\n";
    }

    // Password History Table
    if (!in_array('password_history', $existingTables)) {
        echo "Creating password_history table...\n";
        $db->exec("
            CREATE TABLE password_history (
                id INT AUTO_INCREMENT PRIMARY KEY,
                user_id INT NOT NULL,
                password_hash VARCHAR(255) NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                INDEX idx_user_id (user_id),
                INDEX idx_created_at (created_at),
                FOREIGN KEY (user_id) REFERENCES admin_users(id) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
        echo "✓ Created password_history\n";
    } else {
        echo "⚠ password_history already exists\n";
    }

    // Active Sessions Table
    if (!in_array('active_sessions', $existingTables)) {
        echo "Creating active_sessions table...\n";
        $db->exec("
            CREATE TABLE active_sessions (
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
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
        echo "✓ Created active_sessions\n";
    } else {
        echo "⚠ active_sessions already exists\n";
    }

    // Security Settings Table
    if (!in_array('security_settings', $existingTables)) {
        echo "Creating security_settings table...\n";
        $db->exec("
            CREATE TABLE security_settings (
                id INT AUTO_INCREMENT PRIMARY KEY,
                setting_key VARCHAR(100) UNIQUE NOT NULL,
                setting_value TEXT,
                description TEXT,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                INDEX idx_setting_key (setting_key)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");

        // Insert default settings
        $db->exec("
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
        ");
        echo "✓ Created security_settings with defaults\n";
    } else {
        echo "⚠ security_settings already exists\n";
    }

    // Security Audit Log Table
    if (!in_array('security_audit_log', $existingTables)) {
        echo "Creating security_audit_log table...\n";
        $db->exec("
            CREATE TABLE security_audit_log (
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
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
        echo "✓ Created security_audit_log\n";
    } else {
        echo "⚠ security_audit_log already exists\n";
    }

    // Trusted Devices Table
    if (!in_array('trusted_devices', $existingTables)) {
        echo "Creating trusted_devices table...\n";
        $db->exec("
            CREATE TABLE trusted_devices (
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
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
        echo "✓ Created trusted_devices\n";
    } else {
        echo "⚠ trusted_devices already exists\n";
    }

    echo "\n";
    echo "╔════════════════════════════════════════════════════════════╗\n";
    echo "║  Installation Complete!                                    ║\n";
    echo "╚════════════════════════════════════════════════════════════╝\n";
    echo "\n";

    // Verify all tables
    echo "Verifying tables...\n";
    $requiredTables = ['two_factor_auth', 'password_history', 'active_sessions', 'security_settings', 'security_audit_log', 'trusted_devices'];
    $stmt = $db->query("SHOW TABLES");
    $currentTables = [];
    while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
        $currentTables[] = $row[0];
    }

    $allPresent = true;
    foreach ($requiredTables as $table) {
        if (in_array($table, $currentTables)) {
            echo "  ✓ $table\n";
        } else {
            echo "  ✗ $table MISSING\n";
            $allPresent = false;
        }
    }

    echo "\n";
    if ($allPresent) {
        echo "✅ All security tables are installed!\n";
        echo "\nYou can now:\n";
        echo "  - Create users with: php create_secure_admin.php\n";
        echo "  - Reset passwords with: php reset_password.php\n";
        echo "  - Use the admin panel normally\n";
    } else {
        echo "⚠ Some tables are missing. Please check errors above.\n";
    }
    echo "\n";

} catch (PDOException $e) {
    echo "\n❌ ERROR: " . $e->getMessage() . "\n\n";
    exit(1);
}
