<?php
/**
 * ICOP Website Backend Configuration (PRODUCTION)
 * Indira College of Pharmacy
 * 
 * INSTRUCTIONS FOR DEPLOYMENT:
 * 1. Rename this file to 'config.php' on the server.
 * 2. Update the database credentials below.
 * 3. Update the SITE_URL to your actual domain name.
 */

// ============================================
// DATABASE CONFIGURATION
// ============================================

define('DB_HOST', 'localhost');           // Usually 'localhost'
define('DB_NAME', 'u123456789_icop');     // CHANGE THIS to your cPanel database name
define('DB_USER', 'u123456789_user');     // CHANGE THIS to your cPanel database username
define('DB_PASS', 'YourStrongPassword');  // CHANGE THIS to your cPanel database password
define('DB_CHARSET', 'utf8mb4');

// ============================================
// WEBSITE CONFIGURATION
// ============================================

define('SITE_NAME', 'Indira College of Pharmacy');
define('SITE_URL', 'https://www.sssicop.org');  // CHANGE THIS to your actual website URL (e.g., https://icop.edu.in)
define('ADMIN_EMAIL', 'info@sssicop.org');        // CHANGE THIS to your admin email

// ============================================
// FILE UPLOAD SETTINGS
// ============================================

define('UPLOAD_DIR', __DIR__ . '/../uploads/');
define('BANNER_DIR', UPLOAD_DIR . 'banners/');
define('GALLERY_DIR', UPLOAD_DIR . 'gallery/');
define('NOTICE_DIR', UPLOAD_DIR . 'notices/');
define('RESULT_DIR', UPLOAD_DIR . 'results/');
define('APPLICATION_DIR', UPLOAD_DIR . 'applications/');
define('SYLLABUS_DIR', UPLOAD_DIR . 'syllabus/');
define('EXAM_DIR', UPLOAD_DIR . 'examinations/');

// Maximum file sizes (in bytes)
define('MAX_IMAGE_SIZE', 5 * 1024 * 1024);    // 5MB
define('MAX_PDF_SIZE', 10 * 1024 * 1024);     // 10MB

// Allowed file types
define('ALLOWED_IMAGE_TYPES', ['image/jpeg', 'image/jpg', 'image/png', 'image/gif']);
define('ALLOWED_DOC_TYPES', ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document']);

// ============================================
// SECURITY SETTINGS
// ============================================

define('ADMIN_SESSION_NAME', 'icop_admin_session');
define('SESSION_TIMEOUT', 3600); // 1 hour in seconds

// Admin credentials (Should be changed in database, but here as fallback/initial)
define('ADMIN_USERNAME', 'admin');
// Default password is 'admin123' - You should change this immediately after login!
define('ADMIN_PASSWORD', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');

// ============================================
// ERROR REPORTING
// ============================================

// Set to FALSE in production!
define('DEBUG_MODE', false);

if (DEBUG_MODE) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}

// ============================================
// TIMEZONE
// ============================================

date_default_timezone_set('Asia/Kolkata');

// ============================================
// DATABASE CONNECTION CLASS
// ============================================

class Database {
    private static $instance = null;
    private $conn;
    
    private function __construct() {
        try {
            $this->conn = new PDO(
                "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET,
                DB_USER,
                DB_PASS,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false
                ]
            );
        } catch(PDOException $e) {
            // In production, don't show full error details to users
            if (DEBUG_MODE) {
                die("Connection failed: " . $e->getMessage());
            } else {
                die("Database connection error. Please contact administrator.");
            }
        }
    }
    
    public static function getInstance() {
        if (self::$instance == null) {
            self::$instance = new Database();
        }
        return self::$instance;
    }
    
    public function getConnection() {
        return $this->conn;
    }
}
