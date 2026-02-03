<?php
/**
 * Admin Panel Configuration - Unified with Backend
 * Indira College of Pharmacy
 * Uses MySQL Database
 */

// Start session only if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include backend configuration
require_once dirname(__DIR__) . '/backend/config.php';

// Security Constants
if (!defined('MAX_LOGIN_ATTEMPTS'))
    define('MAX_LOGIN_ATTEMPTS', 5);
if (!defined('LOGIN_LOCKOUT_TIME'))
    define('LOGIN_LOCKOUT_TIME', 15 * 60); // 15 minutes
if (!defined('SESSION_TIMEOUT'))
    define('SESSION_TIMEOUT', 30 * 60); // 30 minutes

// Upload directories
if (!defined('UPLOAD_DIR'))
    define('UPLOAD_DIR', dirname(__DIR__) . '/uploads/');
if (!defined('BANNER_DIR'))
    define('BANNER_DIR', UPLOAD_DIR . 'banners/');
if (!defined('GALLERY_DIR'))
    define('GALLERY_DIR', UPLOAD_DIR . 'gallery/');
if (!defined('NOTICE_DIR'))
    define('NOTICE_DIR', UPLOAD_DIR . 'notices/');
if (!defined('RESULT_DIR'))
    define('RESULT_DIR', UPLOAD_DIR . 'results/');
if (!defined('SYLLABUS_DIR'))
    define('SYLLABUS_DIR', UPLOAD_DIR . 'syllabus/');
if (!defined('EXAM_DIR'))
    define('EXAM_DIR', UPLOAD_DIR . 'examinations/');

// Create directories if they don't exist
$directories = [BANNER_DIR, GALLERY_DIR, NOTICE_DIR, RESULT_DIR, SYLLABUS_DIR, EXAM_DIR];
foreach ($directories as $dir) {
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
}

// Get database connection using backend Database class
function getDB()
{
    return Database::getInstance()->getConnection();
}

// Check if user is logged in & Session Timeout
function isAdminLoggedIn()
{
    if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
        // Check for session timeout
        if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > SESSION_TIMEOUT)) {
            session_unset();
            session_destroy();
            return false;
        }
        $_SESSION['last_activity'] = time();
        return true;
    }
    return false;
}

// Redirect if not logged in
function requireAdminLogin()
{
    if (!isAdminLoggedIn()) {
        header('Location: index.php?timeout=1');
        exit;
    }
}

// Brute Force Protection: Check Attempts
function checkBruteForce($ip_address)
{
    try {
        $db = getDB();
        // Count failed attempts in the last LOCKOUT_TIME
        $stmt = $db->prepare("SELECT COUNT(*) FROM login_attempts WHERE ip_address = ? AND is_success = 0 AND attempt_time > (NOW() - INTERVAL " . LOGIN_LOCKOUT_TIME . " SECOND)");
        $stmt->execute([$ip_address]);
        $attempts = $stmt->fetchColumn();
        return $attempts >= MAX_LOGIN_ATTEMPTS;
    } catch (PDOException $e) {
        return false; // Fail open in case of DB error, but log it
    }
}

// Brute Force Protection: Record Attempt
function recordLoginAttempt($ip_address, $is_success)
{
    try {
        $db = getDB();
        $stmt = $db->prepare("INSERT INTO login_attempts (ip_address, is_success) VALUES (?, ?)");
        $stmt->execute([$ip_address, $is_success ? 1 : 0]);
    } catch (PDOException $e) {
        // Ignore logging errors
    }
}

// Verify admin credentials from database
function verifyAdminCredentials($username, $password)
{
    $ip_address = $_SERVER['REMOTE_ADDR'];

    // Initialize security manager
    $securityManager = new SecurityManager();
    $twoFactorAuth = new TwoFactorAuth();

    // Check for lockout
    if (checkBruteForce($ip_address)) {
        $securityManager->logSecurityEvent(null, 'login_blocked', "Login blocked for IP: {$ip_address}", 'warning');
        return ['success' => false, 'message' => 'Too many failed attempts. Please try again later.'];
    }

    try {
        $db = getDB();
        $stmt = $db->prepare("SELECT * FROM admin_users WHERE username = ? AND is_active = 1 LIMIT 1");
        $stmt->execute([$username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            // Record failed attempt
            recordLoginAttempt($ip_address, false);
            $securityManager->logSecurityEvent(null, 'login_failed', "Failed login attempt for username: {$username}", 'failed');
            return ['success' => false, 'message' => 'Invalid credentials'];
        }

        // Check if account is locked
        $lockStatus = $securityManager->isAccountLocked($user['id']);
        if ($lockStatus['locked']) {
            $securityManager->logSecurityEvent($user['id'], 'login_locked', 'Login attempt on locked account', 'warning');
            return ['success' => false, 'message' => $lockStatus['message']];
        }

        // Verify password
        if (!password_verify($password, $user['password'])) {
            // Record failed attempt
            recordLoginAttempt($ip_address, false);
            $failedResult = $securityManager->recordFailedLogin($user['id']);
            $securityManager->logSecurityEvent($user['id'], 'login_failed', 'Invalid password', 'failed');

            return [
                'success' => false,
                'message' => $failedResult['message'] ?? 'Invalid credentials'
            ];
        }

        // Check if password has expired
        $expiryCheck = $securityManager->isPasswordExpired($user['id']);
        if ($expiryCheck['expired']) {
            return [
                'success' => false,
                'password_expired' => true,
                'message' => $expiryCheck['message']
            ];
        }

        // Check if 2FA is enabled
        if ($twoFactorAuth->isEnabled($user['id'])) {
            // Store user ID in session for 2FA verification
            $_SESSION['pending_2fa_user_id'] = $user['id'];
            $_SESSION['pending_2fa_username'] = $user['username'];

            $securityManager->logSecurityEvent($user['id'], 'login_2fa_required', 'Password verified, awaiting 2FA', 'info');

            return [
                'success' => false,
                'requires_2fa' => true,
                'message' => 'Please enter your 2FA code',
                'user' => $user
            ];
        }

        // Update last login
        $updateStmt = $db->prepare("UPDATE admin_users SET last_login = NOW() WHERE id = ?");
        $updateStmt->execute([$user['id']]);

        // Record successful attempt
        recordLoginAttempt($ip_address, true);

        return [
            'success' => true,
            'user' => $user
        ];

    } catch (PDOException $e) {
        if (DEBUG_MODE) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
        return ['success' => false, 'message' => 'Database error'];
    }
}

// Sanitize input
if (!function_exists('sanitizeInput')) {
    function sanitizeInput($data)
    {
        return sanitize($data);
    }
}

// Handle file upload with better validation
function handleAdminFileUpload($file, $targetDir, $allowedTypes = ['jpg', 'jpeg', 'png', 'gif', 'pdf'])
{
    if (!isset($file['error']) || is_array($file['error'])) {
        return ['success' => false, 'message' => 'Invalid file upload'];
    }

    if ($file['error'] !== UPLOAD_ERR_OK) {
        return ['success' => false, 'message' => 'Upload error code: ' . $file['error']];
    }

    // Check file extension
    $fileExt = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($fileExt, $allowedTypes)) {
        return ['success' => false, 'message' => 'Invalid file type. Allowed: ' . implode(', ', $allowedTypes)];
    }

    // Check file size
    $maxSize = in_array($fileExt, ['pdf']) ? MAX_PDF_SIZE : MAX_IMAGE_SIZE;
    if ($file['size'] > $maxSize) {
        return ['success' => false, 'message' => 'File size exceeds maximum allowed'];
    }

    // Verify MIME type
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mimeType = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);

    $allowedMimes = array_merge(ALLOWED_IMAGE_TYPES, ALLOWED_DOC_TYPES);
    if (!in_array($mimeType, $allowedMimes)) {
        return ['success' => false, 'message' => 'Invalid file MIME type'];
    }

    // Create directory if it doesn't exist
    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0755, true);
    }

    // Generate unique filename
    $fileName = uniqid() . '_' . time() . '.' . $fileExt;
    $targetPath = $targetDir . $fileName;

    if (move_uploaded_file($file['tmp_name'], $targetPath)) {
        // Normalize slashes for web usage
        $relativePath = str_replace(dirname(__DIR__) . DIRECTORY_SEPARATOR, '', $targetPath);
        $relativePath = str_replace('\\', '/', $relativePath); // Convert backslashes to forward slashes

        return [
            'success' => true,
            'filename' => $fileName,
            'path' => $targetPath,
            'relative_path' => $relativePath
        ];
    }

    return ['success' => false, 'message' => 'Failed to move uploaded file'];
}

// Delete file helper
function deleteFile($filePath)
{
    if (file_exists($filePath) && is_file($filePath)) {
        return unlink($filePath);
    }
    return false;
}
?>