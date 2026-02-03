<?php
/**
 * Two-Factor Authentication Class
 * Implements TOTP (Time-based One-Time Password) authentication
 */

class TwoFactorAuth
{
    private $db;
    private $codeLength = 6;
    private $period = 30; // 30 seconds

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Generate a secret key for TOTP
     */
    public function generateSecret()
    {
        $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567'; // Base32 characters
        $secret = '';
        for ($i = 0; $i < 32; $i++) {
            $secret .= $chars[random_int(0, strlen($chars) - 1)];
        }
        return $secret;
    }

    /**
     * Generate QR code URL for authenticator apps
     */
    public function getQRCodeUrl($secret, $username, $issuer = 'ICOP Admin')
    {
        $otpauthUrl = sprintf(
            'otpauth://totp/%s:%s?secret=%s&issuer=%s',
            urlencode($issuer),
            urlencode($username),
            $secret,
            urlencode($issuer)
        );

        // Using Google Charts API for QR code generation
        return 'https://chart.googleapis.com/chart?chs=200x200&chld=M|0&cht=qr&chl=' . urlencode($otpauthUrl);
    }

    /**
     * Verify TOTP code
     */
    public function verifyCode($secret, $code, $discrepancy = 1)
    {
        $currentTimeSlice = floor(time() / $this->period);

        // Check current time slice and adjacent ones (to account for time drift)
        for ($i = -$discrepancy; $i <= $discrepancy; $i++) {
            $calculatedCode = $this->getCode($secret, $currentTimeSlice + $i);
            if ($this->timingSafeEquals($calculatedCode, $code)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get TOTP code for a given time slice
     */
    private function getCode($secret, $timeSlice = null)
    {
        if ($timeSlice === null) {
            $timeSlice = floor(time() / $this->period);
        }

        $secretKey = $this->base32Decode($secret);

        // Pack time into binary string
        $time = chr(0) . chr(0) . chr(0) . chr(0) . pack('N*', $timeSlice);

        // Hash it with SHA1
        $hash = hash_hmac('SHA1', $time, $secretKey, true);

        // Use last nibble of result as index/offset
        $offset = ord(substr($hash, -1)) & 0x0F;

        // Grab 4 bytes of the result
        $hashPart = substr($hash, $offset, 4);

        // Unpack binary value
        $value = unpack('N', $hashPart);
        $value = $value[1];

        // Only 32 bits
        $value = $value & 0x7FFFFFFF;

        $modulo = pow(10, $this->codeLength);

        return str_pad($value % $modulo, $this->codeLength, '0', STR_PAD_LEFT);
    }

    /**
     * Decode Base32 string
     */
    private function base32Decode($secret)
    {
        if (empty($secret)) {
            return '';
        }

        $base32chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';
        $base32charsFlipped = array_flip(str_split($base32chars));

        $paddingCharCount = substr_count($secret, '=');
        $allowedValues = [6, 4, 3, 1, 0];

        if (!in_array($paddingCharCount, $allowedValues)) {
            return false;
        }

        for ($i = 0; $i < 4; $i++) {
            if (
                $paddingCharCount == $allowedValues[$i] &&
                substr($secret, -($allowedValues[$i])) != str_repeat('=', $allowedValues[$i])
            ) {
                return false;
            }
        }

        $secret = str_replace('=', '', $secret);
        $secret = str_split($secret);
        $binaryString = '';

        for ($i = 0; $i < count($secret); $i = $i + 8) {
            $x = '';
            if (!in_array($secret[$i], $base32charsFlipped)) {
                return false;
            }

            for ($j = 0; $j < 8; $j++) {
                $x .= str_pad(base_convert(@$base32charsFlipped[@$secret[$i + $j]], 10, 2), 5, '0', STR_PAD_LEFT);
            }

            $eightBits = str_split($x, 8);

            for ($z = 0; $z < count($eightBits); $z++) {
                $binaryString .= (($y = chr(base_convert($eightBits[$z], 2, 10))) || ord($y) == 48) ? $y : '';
            }
        }

        return $binaryString;
    }

    /**
     * Timing-safe string comparison
     */
    private function timingSafeEquals($safe, $user)
    {
        if (function_exists('hash_equals')) {
            return hash_equals($safe, $user);
        }

        $safeLen = strlen($safe);
        $userLen = strlen($user);

        if ($userLen != $safeLen) {
            return false;
        }

        $result = 0;

        for ($i = 0; $i < $userLen; $i++) {
            $result |= (ord($safe[$i]) ^ ord($user[$i]));
        }

        return $result === 0;
    }

    /**
     * Generate backup codes
     */
    public function generateBackupCodes($count = 10)
    {
        $codes = [];
        for ($i = 0; $i < $count; $i++) {
            // Generate 8-character alphanumeric code
            $code = strtoupper(substr(bin2hex(random_bytes(4)), 0, 8));
            // Format as XXXX-XXXX for readability
            $codes[] = substr($code, 0, 4) . '-' . substr($code, 4, 4);
        }
        return $codes;
    }

    /**
     * Setup 2FA for a user
     */
    public function setupTwoFactor($userId, $username)
    {
        try {
            // Check if 2FA already exists
            $stmt = $this->db->prepare("SELECT id FROM two_factor_auth WHERE user_id = ?");
            $stmt->execute([$userId]);

            if ($stmt->fetch()) {
                // Delete existing 2FA setup
                $stmt = $this->db->prepare("DELETE FROM two_factor_auth WHERE user_id = ?");
                $stmt->execute([$userId]);
            }

            // Generate new secret and backup codes
            $secret = $this->generateSecret();
            $backupCodes = $this->generateBackupCodes();

            // Hash backup codes before storing
            $hashedBackupCodes = array_map(function ($code) {
                return password_hash($code, PASSWORD_DEFAULT);
            }, $backupCodes);

            // Store in database
            $stmt = $this->db->prepare("
                INSERT INTO two_factor_auth (user_id, secret_key, backup_codes, is_verified) 
                VALUES (?, ?, ?, 0)
            ");
            $stmt->execute([
                $userId,
                $secret,
                json_encode($hashedBackupCodes)
            ]);

            return [
                'success' => true,
                'secret' => $secret,
                'qr_code_url' => $this->getQRCodeUrl($secret, $username),
                'backup_codes' => $backupCodes
            ];

        } catch (PDOException $e) {
            return [
                'success' => false,
                'message' => 'Failed to setup 2FA: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Verify and activate 2FA
     */
    public function verifyAndActivate($userId, $code)
    {
        try {
            $stmt = $this->db->prepare("SELECT secret_key FROM two_factor_auth WHERE user_id = ?");
            $stmt->execute([$userId]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$result) {
                return ['success' => false, 'message' => '2FA not setup'];
            }

            if ($this->verifyCode($result['secret_key'], $code)) {
                // Mark as verified
                $stmt = $this->db->prepare("
                    UPDATE two_factor_auth 
                    SET is_verified = 1, verified_at = NOW() 
                    WHERE user_id = ?
                ");
                $stmt->execute([$userId]);

                // Enable 2FA for user
                $stmt = $this->db->prepare("
                    UPDATE admin_users 
                    SET two_factor_enabled = 1 
                    WHERE id = ?
                ");
                $stmt->execute([$userId]);

                return ['success' => true, 'message' => '2FA activated successfully'];
            }

            return ['success' => false, 'message' => 'Invalid verification code'];

        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Verification failed'];
        }
    }

    /**
     * Verify 2FA code for login
     */
    public function verifyLogin($userId, $code)
    {
        try {
            $stmt = $this->db->prepare("
                SELECT secret_key, backup_codes 
                FROM two_factor_auth 
                WHERE user_id = ? AND is_verified = 1
            ");
            $stmt->execute([$userId]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$result) {
                return ['success' => false, 'message' => '2FA not configured'];
            }

            // Try TOTP code first
            if ($this->verifyCode($result['secret_key'], $code)) {
                // Update last used
                $stmt = $this->db->prepare("UPDATE two_factor_auth SET last_used = NOW() WHERE user_id = ?");
                $stmt->execute([$userId]);

                return ['success' => true, 'method' => 'totp'];
            }

            // Try backup codes
            $backupCodes = json_decode($result['backup_codes'], true);
            if ($backupCodes && is_array($backupCodes)) {
                foreach ($backupCodes as $index => $hashedCode) {
                    if (password_verify($code, $hashedCode)) {
                        // Remove used backup code
                        unset($backupCodes[$index]);
                        $backupCodes = array_values($backupCodes); // Re-index array

                        $stmt = $this->db->prepare("
                            UPDATE two_factor_auth 
                            SET backup_codes = ?, last_used = NOW() 
                            WHERE user_id = ?
                        ");
                        $stmt->execute([json_encode($backupCodes), $userId]);

                        return [
                            'success' => true,
                            'method' => 'backup_code',
                            'remaining_codes' => count($backupCodes),
                            'warning' => count($backupCodes) === 0 ? 'No backup codes remaining. Please generate new ones.' : null
                        ];
                    }
                }
            }

            return ['success' => false, 'message' => 'Invalid 2FA code'];

        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Verification failed'];
        }
    }

    /**
     * Disable 2FA for a user
     */
    public function disable($userId)
    {
        try {
            $this->db->beginTransaction();

            // Delete 2FA data
            $stmt = $this->db->prepare("DELETE FROM two_factor_auth WHERE user_id = ?");
            $stmt->execute([$userId]);

            // Disable in user table
            $stmt = $this->db->prepare("UPDATE admin_users SET two_factor_enabled = 0 WHERE id = ?");
            $stmt->execute([$userId]);

            $this->db->commit();

            return ['success' => true, 'message' => '2FA disabled successfully'];

        } catch (PDOException $e) {
            $this->db->rollBack();
            return ['success' => false, 'message' => 'Failed to disable 2FA'];
        }
    }

    /**
     * Check if user has 2FA enabled
     */
    public function isEnabled($userId)
    {
        try {
            $stmt = $this->db->prepare("SELECT two_factor_enabled FROM admin_users WHERE id = ?");
            $stmt->execute([$userId]);
            $result = $stmt->fetchColumn();
            return (bool) $result;
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * Get remaining backup codes count
     */
    public function getRemainingBackupCodes($userId)
    {
        try {
            $stmt = $this->db->prepare("SELECT backup_codes FROM two_factor_auth WHERE user_id = ?");
            $stmt->execute([$userId]);
            $result = $stmt->fetchColumn();

            if ($result) {
                $codes = json_decode($result, true);
                return count($codes);
            }

            return 0;
        } catch (PDOException $e) {
            return 0;
        }
    }
}
