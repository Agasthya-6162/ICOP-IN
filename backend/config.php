<?php
/**
 * ICOP Website Backend Configuration
 * Indira College of Pharmacy
 * 
 * This file contains all the important settings for your website.
 * Fill in your database details below.
 */

// ============================================
// DATABASE CONFIGURATION
// ============================================
// Change these to match your hosting details

define('DB_HOST', 'localhost');           // Usually 'localhost'
define('DB_NAME', 'icop_website');        // Your database name
define('DB_USER', 'root');                // Your database username
define('DB_PASS', '');                    // Your database password
define('DB_CHARSET', 'utf8mb4');

// ============================================
// WEBSITE CONFIGURATION
// ============================================

define('SITE_NAME', 'Indira College of Pharmacy');
define('SITE_URL', 'http://localhost/In-ICOP');  // Change to your website URL
define('ADMIN_EMAIL', 'admin@icop.edu.in');

// ============================================
// FILE UPLOAD SETTINGS
// ============================================

define('UPLOAD_DIR', __DIR__ . '/../uploads/');
define('BANNER_DIR', UPLOAD_DIR . 'banners/');
define('GALLERY_DIR', UPLOAD_DIR . 'gallery/');
define('NOTICE_DIR', UPLOAD_DIR . 'notices/');
define('RESULT_DIR', UPLOAD_DIR . 'results/');
define('APPLICATION_DIR', UPLOAD_DIR . 'applications/');

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

// Admin credentials (Change these!)
define('ADMIN_USERNAME', 'admin');
define('ADMIN_PASSWORD', password_hash('admin123', PASSWORD_DEFAULT));

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

class Database
{
    private static $instance = null;
    private $conn;

    private function __construct()
    {
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
        } catch (PDOException $e) {
            // Log error
            error_log("Database Connection Error: " . $e->getMessage());

            // If in debug mode, show error
            if (defined('DEBUG_MODE') && DEBUG_MODE) {
                die("Database Connection Error: " . $e->getMessage());
            } else {
                // Friendly error message
                die("<h1>Service Unavailable</h1><p>The website is currently experiencing technical difficulties. Please try again later.</p>");
            }
        }
    }

    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getConnection()
    {
        return $this->conn;
    }

    // Prevent cloning
    private function __clone()
    {
    }

    // Prevent unserialization
    public function __wakeup()
    {
        throw new Exception("Cannot unserialize singleton");
    }
}

// ============================================
// SECURITY CLASSES
// ============================================

// Include security classes
require_once __DIR__ . '/classes/SecurityManager.php';
require_once __DIR__ . '/classes/TwoFactorAuth.php';
require_once __DIR__ . '/classes/SessionManager.php';

// ============================================
// HELPER FUNCTIONS
// ============================================

/**
 * Sanitize user input
 */
function sanitize($data)
{
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    return $data;
}

/**
 * Generate unique reference number
 */
function generateReferenceNumber($prefix = 'ICOP')
{
    $year = date('Y');
    $random = str_pad(mt_rand(1, 999999), 6, '0', STR_PAD_LEFT);
    return $prefix . $year . '-' . $random;
}

/**
 * Handle file upload
 */
function uploadFile($file, $destination, $allowedTypes, $maxSize)
{
    if (!isset($file['error']) || is_array($file['error'])) {
        return ['success' => false, 'message' => 'Invalid file upload'];
    }

    // Check for upload errors
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return ['success' => false, 'message' => 'Upload error code: ' . $file['error']];
    }

    // Check file size
    if ($file['size'] > $maxSize) {
        return ['success' => false, 'message' => 'File size exceeds maximum allowed'];
    }

    // Check file type
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mimeType = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);

    if (!in_array($mimeType, $allowedTypes)) {
        return ['success' => false, 'message' => 'Invalid file type'];
    }

    // Generate unique filename
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = uniqid() . '_' . time() . '.' . $extension;
    $filepath = $destination . $filename;

    // Create directory if it doesn't exist
    if (!is_dir($destination)) {
        mkdir($destination, 0755, true);
    }

    // Move file
    if (!move_uploaded_file($file['tmp_name'], $filepath)) {
        return ['success' => false, 'message' => 'Failed to move uploaded file'];
    }

    return ['success' => true, 'filename' => $filename, 'filepath' => $filepath];
}

/**
 * Send JSON response
 */
function sendJSON($data, $statusCode = 200)
{
    http_response_code($statusCode);
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}

/**
 * Check if user is logged in (admin)
 */
function isLoggedIn()
{
    // Session should already be started by admin/config.php
    // Check session status to avoid errors
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    return isset($_SESSION[ADMIN_SESSION_NAME]) && $_SESSION[ADMIN_SESSION_NAME] === true;
}

/**
 * Require login
 */
function requireLogin()
{
    if (!isLoggedIn()) {
        header('Location: ../admin/index.php');
        exit;
    }
}

/**
 * Log activity
 */
function logActivity($action, $tableName = null, $recordId = null)
{
    try {
        $db = Database::getInstance()->getConnection();
        $userId = $_SESSION['admin_user_id'] ?? null;

        $stmt = $db->prepare("
            INSERT INTO activity_log (user_id, action, table_name, record_id, ip_address, user_agent) 
            VALUES (?, ?, ?, ?, ?, ?)
        ");

        $stmt->execute([
            $userId,
            $action,
            $tableName,
            $recordId,
            $_SERVER['REMOTE_ADDR'] ?? null,
            $_SERVER['HTTP_USER_AGENT'] ?? null
        ]);
    } catch (PDOException $e) {
        // Silently fail - don't break the application
        if (DEBUG_MODE) {
            error_log("Activity log error: " . $e->getMessage());
        }
    }
}

/**
 * Get website setting
 */
function getSetting($key, $default = '')
{
    try {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("SELECT setting_value FROM website_settings WHERE setting_key = ?");
        $stmt->execute([$key]);
        $result = $stmt->fetch();
        return $result ? $result['setting_value'] : $default;
    } catch (PDOException $e) {
        return $default;
    }
}

/**
 * Update website setting
 */
function updateSetting($key, $value)
{
    try {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("
            INSERT INTO website_settings (setting_key, setting_value) 
            VALUES (?, ?) 
            ON DUPLICATE KEY UPDATE setting_value = ?
        ");
        $stmt->execute([$key, $value, $value]);
        return true;
    } catch (PDOException $e) {
        return false;
    }
}

// ============================================
// CREATE UPLOAD DIRECTORIES
// ============================================

$directories = [
    UPLOAD_DIR,
    BANNER_DIR,
    GALLERY_DIR,
    NOTICE_DIR,
    RESULT_DIR,
    APPLICATION_DIR
];

foreach ($directories as $dir) {
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
}

// ============================================
// CORS HEADERS (if needed for AJAX)
// ============================================

if (isset($_SERVER['HTTP_ORIGIN'])) {
    header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
    header('Access-Control-Allow-Credentials: true');
    header('Access-Control-Max-Age: 86400');
}

if (isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD'])) {
        header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
    }
    if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS'])) {
        header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");
    }
    exit(0);
}

?>