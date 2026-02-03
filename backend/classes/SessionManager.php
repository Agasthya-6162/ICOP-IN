<?php
/**
 * Session Manager Class
 * Handles secure session creation, validation, and management
 */

class SessionManager
{
    private $db;
    private $sessionTimeout;
    private $rememberDays;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();

        // Load settings
        $securityManager = new SecurityManager();
        $this->sessionTimeout = (int) $securityManager->getSetting('session_timeout', 1800);
        $this->rememberDays = (int) $securityManager->getSetting('session_remember_days', 30);
    }

    /**
     * Create a new session
     */
    public function createSession($userId, $rememberMe = false)
    {
        try {
            $sessionToken = $this->generateSecureToken();
            $deviceFingerprint = $this->getDeviceFingerprint();
            $expiresAt = $rememberMe
                ? date('Y-m-d H:i:s', time() + ($this->rememberDays * 86400))
                : date('Y-m-d H:i:s', time() + $this->sessionTimeout);

            $stmt = $this->db->prepare("
                INSERT INTO active_sessions 
                (user_id, session_token, device_fingerprint, ip_address, user_agent, expires_at, is_remembered) 
                VALUES (?, ?, ?, ?, ?, ?, ?)
            ");

            $stmt->execute([
                $userId,
                $sessionToken,
                $deviceFingerprint,
                $_SERVER['REMOTE_ADDR'] ?? null,
                $_SERVER['HTTP_USER_AGENT'] ?? null,
                $expiresAt,
                $rememberMe ? 1 : 0
            ]);

            // Set session variables
            $_SESSION['session_token'] = $sessionToken;
            $_SESSION['user_id'] = $userId;
            $_SESSION['device_fingerprint'] = $deviceFingerprint;
            $_SESSION['last_activity'] = time();

            // If remember me, create trusted device
            if ($rememberMe) {
                $this->createTrustedDevice($userId, $deviceFingerprint);
            }

            return [
                'success' => true,
                'session_token' => $sessionToken,
                'expires_at' => $expiresAt
            ];

        } catch (PDOException $e) {
            return [
                'success' => false,
                'message' => 'Failed to create session'
            ];
        }
    }

    /**
     * Validate session
     */
    public function validateSession()
    {
        // Check if session token exists
        if (!isset($_SESSION['session_token']) || !isset($_SESSION['user_id'])) {
            return ['valid' => false, 'reason' => 'no_session'];
        }

        try {
            $stmt = $this->db->prepare("
                SELECT s.*, u.username, u.is_active 
                FROM active_sessions s
                JOIN admin_users u ON s.user_id = u.id
                WHERE s.session_token = ? AND s.user_id = ?
            ");

            $stmt->execute([
                $_SESSION['session_token'],
                $_SESSION['user_id']
            ]);

            $session = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$session) {
                return ['valid' => false, 'reason' => 'session_not_found'];
            }

            // Check if user is active
            if (!$session['is_active']) {
                $this->destroySession($_SESSION['session_token']);
                return ['valid' => false, 'reason' => 'user_inactive'];
            }

            // Check if session has expired
            if (strtotime($session['expires_at']) < time()) {
                $this->destroySession($_SESSION['session_token']);
                return ['valid' => false, 'reason' => 'session_expired'];
            }

            // Check device fingerprint (basic session hijacking prevention)
            $currentFingerprint = $this->getDeviceFingerprint();
            if ($session['device_fingerprint'] !== $currentFingerprint) {
                // Log suspicious activity
                $securityManager = new SecurityManager();
                $securityManager->logSecurityEvent(
                    $session['user_id'],
                    'session_hijacking_attempt',
                    'Device fingerprint mismatch detected',
                    'warning',
                    ['expected' => $session['device_fingerprint'], 'actual' => $currentFingerprint]
                );

                $this->destroySession($_SESSION['session_token']);
                return ['valid' => false, 'reason' => 'fingerprint_mismatch'];
            }

            // Check for session timeout (if not remembered)
            if (!$session['is_remembered']) {
                $lastActivity = $_SESSION['last_activity'] ?? 0;
                if (time() - $lastActivity > $this->sessionTimeout) {
                    $this->destroySession($_SESSION['session_token']);
                    return ['valid' => false, 'reason' => 'timeout'];
                }
            }

            // Update last activity
            $_SESSION['last_activity'] = time();
            $this->updateSessionActivity($_SESSION['session_token']);

            return [
                'valid' => true,
                'user' => [
                    'id' => $session['user_id'],
                    'username' => $session['username']
                ]
            ];

        } catch (PDOException $e) {
            return ['valid' => false, 'reason' => 'database_error'];
        }
    }

    /**
     * Update session activity timestamp
     */
    private function updateSessionActivity($sessionToken)
    {
        try {
            $stmt = $this->db->prepare("
                UPDATE active_sessions 
                SET last_activity = NOW() 
                WHERE session_token = ?
            ");
            $stmt->execute([$sessionToken]);
        } catch (PDOException $e) {
            // Silently fail
        }
    }

    /**
     * Destroy a specific session
     */
    public function destroySession($sessionToken = null)
    {
        try {
            if ($sessionToken === null) {
                $sessionToken = $_SESSION['session_token'] ?? null;
            }

            if ($sessionToken) {
                $stmt = $this->db->prepare("DELETE FROM active_sessions WHERE session_token = ?");
                $stmt->execute([$sessionToken]);
            }

            // Clear PHP session
            $_SESSION = [];

            // Destroy session cookie
            if (isset($_COOKIE[session_name()])) {
                setcookie(session_name(), '', time() - 3600, '/');
            }

            return ['success' => true];

        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Failed to destroy session'];
        }
    }

    /**
     * Destroy all sessions for a user
     */
    public function destroyAllUserSessions($userId, $exceptCurrent = false)
    {
        try {
            if ($exceptCurrent && isset($_SESSION['session_token'])) {
                $stmt = $this->db->prepare("
                    DELETE FROM active_sessions 
                    WHERE user_id = ? AND session_token != ?
                ");
                $stmt->execute([$userId, $_SESSION['session_token']]);
            } else {
                $stmt = $this->db->prepare("DELETE FROM active_sessions WHERE user_id = ?");
                $stmt->execute([$userId]);
            }

            return ['success' => true];

        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Failed to destroy sessions'];
        }
    }

    /**
     * Get all active sessions for a user
     */
    public function getUserSessions($userId)
    {
        try {
            $stmt = $this->db->prepare("
                SELECT 
                    session_token,
                    ip_address,
                    user_agent,
                    last_activity,
                    created_at,
                    expires_at,
                    is_remembered,
                    CASE 
                        WHEN session_token = ? THEN 1 
                        ELSE 0 
                    END as is_current
                FROM active_sessions 
                WHERE user_id = ? 
                ORDER BY last_activity DESC
            ");

            $currentToken = $_SESSION['session_token'] ?? '';
            $stmt->execute([$currentToken, $userId]);

            $sessions = [];
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $sessions[] = [
                    'token' => substr($row['session_token'], 0, 8) . '...', // Partial token for display
                    'full_token' => $row['session_token'], // For deletion
                    'ip_address' => $row['ip_address'],
                    'device' => $this->parseUserAgent($row['user_agent']),
                    'last_activity' => $row['last_activity'],
                    'created_at' => $row['created_at'],
                    'expires_at' => $row['expires_at'],
                    'is_remembered' => (bool) $row['is_remembered'],
                    'is_current' => (bool) $row['is_current']
                ];
            }

            return ['success' => true, 'sessions' => $sessions];

        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Failed to get sessions'];
        }
    }

    /**
     * Create trusted device
     */
    private function createTrustedDevice($userId, $deviceFingerprint)
    {
        try {
            $deviceToken = $this->generateSecureToken();
            $expiresAt = date('Y-m-d H:i:s', time() + ($this->rememberDays * 86400));

            $stmt = $this->db->prepare("
                INSERT INTO trusted_devices 
                (user_id, device_token, device_fingerprint, ip_address, user_agent, expires_at) 
                VALUES (?, ?, ?, ?, ?, ?)
            ");

            $stmt->execute([
                $userId,
                $deviceToken,
                $deviceFingerprint,
                $_SERVER['REMOTE_ADDR'] ?? null,
                $_SERVER['HTTP_USER_AGENT'] ?? null,
                $expiresAt
            ]);

            return true;
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * Check if device is trusted
     */
    public function isTrustedDevice($userId)
    {
        try {
            $deviceFingerprint = $this->getDeviceFingerprint();

            $stmt = $this->db->prepare("
                SELECT id FROM trusted_devices 
                WHERE user_id = ? 
                AND device_fingerprint = ? 
                AND expires_at > NOW() 
                AND is_active = 1
            ");

            $stmt->execute([$userId, $deviceFingerprint]);

            return (bool) $stmt->fetch();
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * Generate device fingerprint
     */
    private function getDeviceFingerprint()
    {
        $components = [
            $_SERVER['HTTP_USER_AGENT'] ?? '',
            $_SERVER['HTTP_ACCEPT_LANGUAGE'] ?? '',
            $_SERVER['HTTP_ACCEPT_ENCODING'] ?? ''
        ];

        return hash('sha256', implode('|', $components));
    }

    /**
     * Generate secure token
     */
    private function generateSecureToken($length = 32)
    {
        return bin2hex(random_bytes($length));
    }

    /**
     * Parse user agent for display
     */
    private function parseUserAgent($userAgent)
    {
        if (empty($userAgent)) {
            return 'Unknown Device';
        }

        // Simple browser detection
        $browsers = [
            'Chrome' => '/Chrome\/[\d.]+/',
            'Firefox' => '/Firefox\/[\d.]+/',
            'Safari' => '/Safari\/[\d.]+/',
            'Edge' => '/Edg\/[\d.]+/',
            'Opera' => '/Opera\/[\d.]+/',
        ];

        foreach ($browsers as $name => $pattern) {
            if (preg_match($pattern, $userAgent)) {
                return $name;
            }
        }

        // Simple OS detection
        if (stripos($userAgent, 'Windows') !== false)
            return 'Windows';
        if (stripos($userAgent, 'Mac') !== false)
            return 'Mac';
        if (stripos($userAgent, 'Linux') !== false)
            return 'Linux';
        if (stripos($userAgent, 'Android') !== false)
            return 'Android';
        if (stripos($userAgent, 'iOS') !== false)
            return 'iOS';

        return 'Unknown Device';
    }

    /**
     * Cleanup expired sessions
     */
    public function cleanupExpiredSessions()
    {
        try {
            $stmt = $this->db->prepare("DELETE FROM active_sessions WHERE expires_at < NOW()");
            $stmt->execute();

            $stmt = $this->db->prepare("DELETE FROM trusted_devices WHERE expires_at < NOW()");
            $stmt->execute();

            return ['success' => true];
        } catch (PDOException $e) {
            return ['success' => false];
        }
    }
}
