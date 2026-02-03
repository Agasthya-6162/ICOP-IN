<?php
/**
 * Security Manager Class
 * Handles password policies, account lockout, and security validation
 */

class SecurityManager
{
    private $db;
    private $settings;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
        $this->loadSettings();
    }

    /**
     * Load security settings from database
     */
    private function loadSettings()
    {
        try {
            $stmt = $this->db->query("SELECT setting_key, setting_value FROM security_settings");
            $this->settings = [];
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $this->settings[$row['setting_key']] = $row['setting_value'];
            }
        } catch (PDOException $e) {
            // Use default settings if database query fails
            $this->settings = [
                'max_login_attempts' => 5,
                'lockout_duration' => 900,
                'session_timeout' => 1800,
                'password_min_length' => 12,
                'password_require_uppercase' => 1,
                'password_require_lowercase' => 1,
                'password_require_numbers' => 1,
                'password_require_special' => 1,
                'password_expiry_days' => 90,
                'password_history_count' => 5
            ];
        }
    }

    /**
     * Validate password strength based on NIST guidelines
     */
    public function validatePasswordStrength($password)
    {
        $errors = [];
        
        // Minimum length
        $minLength = (int)$this->settings['password_min_length'];
        if (strlen($password) < $minLength) {
            $errors[] = "Password must be at least {$minLength} characters long";
        }

        // Maximum length (prevent DoS)
        if (strlen($password) > 128) {
            $errors[] = "Password must not exceed 128 characters";
        }

        // Uppercase requirement
        if ($this->settings['password_require_uppercase'] && !preg_match('/[A-Z]/', $password)) {
            $errors[] = "Password must contain at least one uppercase letter";
        }

        // Lowercase requirement
        if ($this->settings['password_require_lowercase'] && !preg_match('/[a-z]/', $password)) {
            $errors[] = "Password must contain at least one lowercase letter";
        }

        // Number requirement
        if ($this->settings['password_require_numbers'] && !preg_match('/[0-9]/', $password)) {
            $errors[] = "Password must contain at least one number";
        }

        // Special character requirement
        if ($this->settings['password_require_special'] && !preg_match('/[^A-Za-z0-9]/', $password)) {
            $errors[] = "Password must contain at least one special character";
        }

        // Check for common passwords
        if ($this->isCommonPassword($password)) {
            $errors[] = "This password is too common. Please choose a more unique password";
        }

        return [
            'valid' => empty($errors),
            'errors' => $errors
        ];
    }

    /**
     * Check if password is in common password list
     */
    private function isCommonPassword($password)
    {
        $commonPasswords = [
            'password', 'password123', '123456', '12345678', 'qwerty', 'abc123',
            'monkey', '1234567', 'letmein', 'trustno1', 'dragon', 'baseball',
            'iloveyou', 'master', 'sunshine', 'ashley', 'bailey', 'passw0rd',
            'shadow', '123123', '654321', 'superman', 'qazwsx', 'michael',
            'admin', 'admin123', 'administrator', 'root', 'toor', 'pass',
            'test', 'guest', 'info', 'adm', 'mysql', 'user', 'administrator',
            'oracle', 'ftp', 'pi', 'puppet', 'ansible', 'ec2-user', 'vagrant',
            'azureuser', 'welcome', 'welcome123', 'password1', 'Password1'
        ];
        
        return in_array(strtolower($password), $commonPasswords);
    }

    /**
     * Check password against history
     */
    public function checkPasswordHistory($userId, $newPassword)
    {
        try {
            $historyCount = (int)$this->settings['password_history_count'];
            
            $stmt = $this->db->prepare("
                SELECT password_hash 
                FROM password_history 
                WHERE user_id = ? 
                ORDER BY created_at DESC 
                LIMIT ?
            ");
            $stmt->execute([$userId, $historyCount]);
            
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                if (password_verify($newPassword, $row['password_hash'])) {
                    return [
                        'allowed' => false,
                        'message' => "You cannot reuse any of your last {$historyCount} passwords"
                    ];
                }
            }
            
            return ['allowed' => true];
        } catch (PDOException $e) {
            // If history check fails, allow password change
            return ['allowed' => true];
        }
    }

    /**
     * Check if account is locked
     */
    public function isAccountLocked($userId)
    {
        try {
            $stmt = $this->db->prepare("
                SELECT locked_until, failed_login_attempts 
                FROM admin_users 
                WHERE id = ?
            ");
            $stmt->execute([$userId]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$user) {
                return false;
            }
            
            // Check if locked_until is in the future
            if ($user['locked_until'] && strtotime($user['locked_until']) > time()) {
                $remainingTime = strtotime($user['locked_until']) - time();
                return [
                    'locked' => true,
                    'remaining_seconds' => $remainingTime,
                    'message' => "Account is locked. Try again in " . ceil($remainingTime / 60) . " minutes"
                ];
            }
            
            // If lock has expired, reset failed attempts
            if ($user['locked_until'] && strtotime($user['locked_until']) <= time()) {
                $this->resetFailedAttempts($userId);
            }
            
            return ['locked' => false];
        } catch (PDOException $e) {
            return ['locked' => false];
        }
    }

    /**
     * Record failed login attempt
     */
    public function recordFailedLogin($userId)
    {
        try {
            $maxAttempts = (int)$this->settings['max_login_attempts'];
            $lockoutDuration = (int)$this->settings['lockout_duration'];
            
            // Increment failed attempts
            $stmt = $this->db->prepare("
                UPDATE admin_users 
                SET failed_login_attempts = failed_login_attempts + 1 
                WHERE id = ?
            ");
            $stmt->execute([$userId]);
            
            // Get current attempt count
            $stmt = $this->db->prepare("SELECT failed_login_attempts FROM admin_users WHERE id = ?");
            $stmt->execute([$userId]);
            $attempts = $stmt->fetchColumn();
            
            // Lock account if max attempts reached
            if ($attempts >= $maxAttempts) {
                $lockUntil = date('Y-m-d H:i:s', time() + $lockoutDuration);
                $stmt = $this->db->prepare("
                    UPDATE admin_users 
                    SET locked_until = ? 
                    WHERE id = ?
                ");
                $stmt->execute([$lockUntil, $userId]);
                
                // Log security event
                $this->logSecurityEvent($userId, 'account_locked', 'Account locked due to too many failed login attempts', 'warning');
                
                return [
                    'locked' => true,
                    'message' => "Account locked due to too many failed attempts. Try again in " . ($lockoutDuration / 60) . " minutes"
                ];
            }
            
            $remainingAttempts = $maxAttempts - $attempts;
            return [
                'locked' => false,
                'remaining_attempts' => $remainingAttempts,
                'message' => "Invalid credentials. {$remainingAttempts} attempts remaining"
            ];
            
        } catch (PDOException $e) {
            return ['locked' => false, 'message' => 'Login failed'];
        }
    }

    /**
     * Reset failed login attempts
     */
    public function resetFailedAttempts($userId)
    {
        try {
            $stmt = $this->db->prepare("
                UPDATE admin_users 
                SET failed_login_attempts = 0, locked_until = NULL 
                WHERE id = ?
            ");
            $stmt->execute([$userId]);
            return true;
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * Check if password has expired
     */
    public function isPasswordExpired($userId)
    {
        try {
            $expiryDays = (int)$this->settings['password_expiry_days'];
            
            // If expiry is 0, passwords never expire
            if ($expiryDays === 0) {
                return ['expired' => false];
            }
            
            $stmt = $this->db->prepare("
                SELECT last_password_change, force_password_change 
                FROM admin_users 
                WHERE id = ?
            ");
            $stmt->execute([$userId]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Force password change flag
            if ($user['force_password_change']) {
                return [
                    'expired' => true,
                    'message' => 'You must change your password before continuing'
                ];
            }
            
            // Check expiry date
            if ($user['last_password_change']) {
                $expiryDate = strtotime($user['last_password_change'] . " + {$expiryDays} days");
                if (time() > $expiryDate) {
                    return [
                        'expired' => true,
                        'message' => 'Your password has expired. Please change it'
                    ];
                }
            }
            
            return ['expired' => false];
        } catch (PDOException $e) {
            return ['expired' => false];
        }
    }

    /**
     * Log security event
     */
    public function logSecurityEvent($userId, $eventType, $description, $status = 'success', $metadata = null)
    {
        try {
            $stmt = $this->db->prepare("
                INSERT INTO security_audit_log 
                (user_id, event_type, event_description, ip_address, user_agent, status, metadata) 
                VALUES (?, ?, ?, ?, ?, ?, ?)
            ");
            
            $stmt->execute([
                $userId,
                $eventType,
                $description,
                $_SERVER['REMOTE_ADDR'] ?? null,
                $_SERVER['HTTP_USER_AGENT'] ?? null,
                $status,
                $metadata ? json_encode($metadata) : null
            ]);
            
            return true;
        } catch (PDOException $e) {
            error_log("Security log error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Generate secure random token
     */
    public function generateSecureToken($length = 32)
    {
        return bin2hex(random_bytes($length));
    }

    /**
     * Get security setting
     */
    public function getSetting($key, $default = null)
    {
        return $this->settings[$key] ?? $default;
    }
}
