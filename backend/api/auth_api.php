<?php
/**
 * Authentication API
 * Handles 2FA verification, password changes, and session management
 */

require_once dirname(__DIR__) . '/config.php';

header('Content-Type: application/json');

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$response = ['success' => false, 'message' => 'Invalid request'];

// Get request method and action
$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? $_POST['action'] ?? '';

// Initialize security classes
$securityManager = new SecurityManager();
$twoFactorAuth = new TwoFactorAuth();
$sessionManager = new SessionManager();

// ============================================
// VERIFY 2FA CODE (Login)
// ============================================
if ($action === 'verify-2fa' && $method === 'POST') {
    $code = $_POST['code'] ?? '';
    $userId = $_SESSION['pending_2fa_user_id'] ?? null;
    $rememberMe = isset($_POST['remember_me']) && $_POST['remember_me'] === '1';

    if (!$userId) {
        $response = ['success' => false, 'message' => 'No pending authentication'];
    } else {
        $result = $twoFactorAuth->verifyLogin($userId, $code);

        if ($result['success']) {
            // Get user details
            $db = Database::getInstance()->getConnection();
            $stmt = $db->prepare("SELECT * FROM admin_users WHERE id = ?");
            $stmt->execute([$userId]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            // Create session
            $sessionResult = $sessionManager->createSession($userId, $rememberMe);

            if ($sessionResult['success']) {
                // Set session variables
                $_SESSION['admin_logged_in'] = true;
                $_SESSION['admin_username'] = $user['username'];
                $_SESSION['admin_user_id'] = $user['id'];
                $_SESSION['admin_full_name'] = $user['full_name'];

                // Clear pending 2FA
                unset($_SESSION['pending_2fa_user_id']);

                // Reset failed login attempts
                $securityManager->resetFailedAttempts($userId);

                // Log successful login
                $securityManager->logSecurityEvent($userId, 'login_success', 'User logged in successfully with 2FA', 'success');

                $response = [
                    'success' => true,
                    'message' => 'Login successful',
                    'redirect' => 'dashboard.php',
                    'warning' => $result['warning'] ?? null
                ];
            } else {
                $response = ['success' => false, 'message' => 'Failed to create session'];
            }
        } else {
            // Log failed 2FA attempt
            $securityManager->logSecurityEvent($userId, 'login_2fa_failed', 'Failed 2FA verification', 'failed');
            $response = $result;
        }
    }
}

// ============================================
// SETUP 2FA
// ============================================
elseif ($action === 'setup-2fa' && $method === 'POST') {
    if (!isset($_SESSION['admin_logged_in']) || !$_SESSION['admin_logged_in']) {
        $response = ['success' => false, 'message' => 'Not authenticated'];
    } else {
        $userId = $_SESSION['admin_user_id'];
        $username = $_SESSION['admin_username'];

        $result = $twoFactorAuth->setupTwoFactor($userId, $username);

        if ($result['success']) {
            // Store secret in session temporarily for verification
            $_SESSION['pending_2fa_secret'] = $result['secret'];

            $securityManager->logSecurityEvent($userId, '2fa_setup_initiated', 'User initiated 2FA setup', 'success');
        }

        $response = $result;
    }
}

// ============================================
// VERIFY AND ACTIVATE 2FA
// ============================================
elseif ($action === 'activate-2fa' && $method === 'POST') {
    if (!isset($_SESSION['admin_logged_in']) || !$_SESSION['admin_logged_in']) {
        $response = ['success' => false, 'message' => 'Not authenticated'];
    } else {
        $userId = $_SESSION['admin_user_id'];
        $code = $_POST['code'] ?? '';

        $result = $twoFactorAuth->verifyAndActivate($userId, $code);

        if ($result['success']) {
            unset($_SESSION['pending_2fa_secret']);
            $securityManager->logSecurityEvent($userId, '2fa_activated', 'User activated 2FA', 'success');
        }

        $response = $result;
    }
}

// ============================================
// DISABLE 2FA
// ============================================
elseif ($action === 'disable-2fa' && $method === 'POST') {
    if (!isset($_SESSION['admin_logged_in']) || !$_SESSION['admin_logged_in']) {
        $response = ['success' => false, 'message' => 'Not authenticated'];
    } else {
        $userId = $_SESSION['admin_user_id'];
        $password = $_POST['password'] ?? '';

        // Verify password before disabling 2FA
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("SELECT password FROM admin_users WHERE id = ?");
        $stmt->execute([$userId]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            $result = $twoFactorAuth->disable($userId);

            if ($result['success']) {
                $securityManager->logSecurityEvent($userId, '2fa_disabled', 'User disabled 2FA', 'warning');
            }

            $response = $result;
        } else {
            $response = ['success' => false, 'message' => 'Invalid password'];
        }
    }
}

// ============================================
// CHANGE PASSWORD
// ============================================
elseif ($action === 'change-password' && $method === 'POST') {
    if (!isset($_SESSION['admin_logged_in']) || !$_SESSION['admin_logged_in']) {
        $response = ['success' => false, 'message' => 'Not authenticated'];
    } else {
        $userId = $_SESSION['admin_user_id'];
        $currentPassword = $_POST['current_password'] ?? '';
        $newPassword = $_POST['new_password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';

        // Verify passwords match
        if ($newPassword !== $confirmPassword) {
            $response = ['success' => false, 'message' => 'New passwords do not match'];
        } else {
            // Verify current password
            $db = Database::getInstance()->getConnection();
            $stmt = $db->prepare("SELECT password FROM admin_users WHERE id = ?");
            $stmt->execute([$userId]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$user || !password_verify($currentPassword, $user['password'])) {
                $response = ['success' => false, 'message' => 'Current password is incorrect'];
            } else {
                // Validate new password strength
                $strengthCheck = $securityManager->validatePasswordStrength($newPassword);

                if (!$strengthCheck['valid']) {
                    $response = [
                        'success' => false,
                        'message' => 'Password does not meet requirements',
                        'errors' => $strengthCheck['errors']
                    ];
                } else {
                    // Check password history
                    $historyCheck = $securityManager->checkPasswordHistory($userId, $newPassword);

                    if (!$historyCheck['allowed']) {
                        $response = ['success' => false, 'message' => $historyCheck['message']];
                    } else {
                        // Update password
                        $newPasswordHash = password_hash($newPassword, PASSWORD_DEFAULT);
                        $stmt = $db->prepare("
                            UPDATE admin_users 
                            SET password = ?, 
                                last_password_change = NOW(),
                                force_password_change = 0
                            WHERE id = ?
                        ");

                        if ($stmt->execute([$newPasswordHash, $userId])) {
                            $securityManager->logSecurityEvent($userId, 'password_changed', 'User changed password', 'success');

                            $response = [
                                'success' => true,
                                'message' => 'Password changed successfully'
                            ];
                        } else {
                            $response = ['success' => false, 'message' => 'Failed to update password'];
                        }
                    }
                }
            }
        }
    }
}

// ============================================
// GET ACTIVE SESSIONS
// ============================================
elseif ($action === 'get-sessions' && $method === 'GET') {
    if (!isset($_SESSION['admin_logged_in']) || !$_SESSION['admin_logged_in']) {
        $response = ['success' => false, 'message' => 'Not authenticated'];
    } else {
        $userId = $_SESSION['admin_user_id'];
        $response = $sessionManager->getUserSessions($userId);
    }
}

// ============================================
// LOGOUT FROM SPECIFIC SESSION
// ============================================
elseif ($action === 'logout-session' && $method === 'POST') {
    if (!isset($_SESSION['admin_logged_in']) || !$_SESSION['admin_logged_in']) {
        $response = ['success' => false, 'message' => 'Not authenticated'];
    } else {
        $sessionToken = $_POST['session_token'] ?? '';

        if ($sessionToken) {
            $result = $sessionManager->destroySession($sessionToken);
            $response = $result;
        } else {
            $response = ['success' => false, 'message' => 'Session token required'];
        }
    }
}

// ============================================
// LOGOUT FROM ALL DEVICES
// ============================================
elseif ($action === 'logout-all-devices' && $method === 'POST') {
    if (!isset($_SESSION['admin_logged_in']) || !$_SESSION['admin_logged_in']) {
        $response = ['success' => false, 'message' => 'Not authenticated'];
    } else {
        $userId = $_SESSION['admin_user_id'];
        $exceptCurrent = isset($_POST['except_current']) && $_POST['except_current'] === '1';

        $result = $sessionManager->destroyAllUserSessions($userId, $exceptCurrent);

        if ($result['success']) {
            $securityManager->logSecurityEvent($userId, 'logout_all_devices', 'User logged out from all devices', 'success');
        }

        $response = $result;
    }
}

// ============================================
// GET 2FA STATUS
// ============================================
elseif ($action === 'get-2fa-status' && $method === 'GET') {
    if (!isset($_SESSION['admin_logged_in']) || !$_SESSION['admin_logged_in']) {
        $response = ['success' => false, 'message' => 'Not authenticated'];
    } else {
        $userId = $_SESSION['admin_user_id'];
        $isEnabled = $twoFactorAuth->isEnabled($userId);
        $backupCodesCount = $twoFactorAuth->getRemainingBackupCodes($userId);

        $response = [
            'success' => true,
            'enabled' => $isEnabled,
            'backup_codes_remaining' => $backupCodesCount
        ];
    }
}

// Output response
echo json_encode($response);
exit;
