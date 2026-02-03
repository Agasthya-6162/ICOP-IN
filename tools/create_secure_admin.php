<?php
/**
 * Create Secure Admin User Script
 * Run this script from command line to create a new admin user with strong password
 * 
 * Usage: php create_secure_admin.php
 */

// Include configuration
require_once __DIR__ . '/config.php';

echo "\n";
echo "╔════════════════════════════════════════════════════════════╗\n";
echo "║  ICOP Admin - Create Secure Admin User                    ║\n";
echo "╚════════════════════════════════════════════════════════════╝\n";
echo "\n";

// Initialize security manager
$securityManager = new SecurityManager();
$db = Database::getInstance()->getConnection();

// Get username
echo "Enter username: ";
$username = trim(fgets(STDIN));

if (empty($username)) {
    die("Error: Username cannot be empty\n");
}

// Check if username already exists
$stmt = $db->prepare("SELECT id FROM admin_users WHERE username = ?");
$stmt->execute([$username]);
if ($stmt->fetch()) {
    die("Error: Username already exists\n");
}

// Get full name
echo "Enter full name: ";
$fullName = trim(fgets(STDIN));

if (empty($fullName)) {
    die("Error: Full name cannot be empty\n");
}

// Get email
echo "Enter email: ";
$email = trim(fgets(STDIN));

if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    die("Error: Invalid email address\n");
}

// Get password
echo "\nPassword Requirements:\n";
echo "- Minimum 12 characters\n";
echo "- At least one uppercase letter\n";
echo "- At least one lowercase letter\n";
echo "- At least one number\n";
echo "- At least one special character\n";
echo "\n";

$passwordValid = false;
$password = '';

while (!$passwordValid) {
    echo "Enter password: ";

    // Hide password input (works on Unix-like systems)
    if (strtoupper(substr(PHP_OS, 0, 3)) !== 'WIN') {
        system('stty -echo');
        $password = trim(fgets(STDIN));
        system('stty echo');
        echo "\n";
    } else {
        $password = trim(fgets(STDIN));
    }

    if (empty($password)) {
        echo "Error: Password cannot be empty\n";
        continue;
    }

    // Validate password strength
    $validation = $securityManager->validatePasswordStrength($password);

    if ($validation['valid']) {
        $passwordValid = true;
    } else {
        echo "\nPassword validation failed:\n";
        foreach ($validation['errors'] as $error) {
            echo "  - $error\n";
        }
        echo "\nPlease try again.\n\n";
    }
}

// Confirm password
echo "Confirm password: ";
if (strtoupper(substr(PHP_OS, 0, 3)) !== 'WIN') {
    system('stty -echo');
    $confirmPassword = trim(fgets(STDIN));
    system('stty echo');
    echo "\n";
} else {
    $confirmPassword = trim(fgets(STDIN));
}

if ($password !== $confirmPassword) {
    die("Error: Passwords do not match\n");
}

// Ask about 2FA
echo "\nEnable Two-Factor Authentication? (recommended) [Y/n]: ";
$enable2FA = trim(fgets(STDIN));
$enable2FA = empty($enable2FA) || strtolower($enable2FA) === 'y';

try {
    // Start transaction
    $db->beginTransaction();

    // Hash password
    $passwordHash = password_hash($password, PASSWORD_DEFAULT);

    // Insert user
    $stmt = $db->prepare("
        INSERT INTO admin_users 
        (username, password, full_name, email, role, is_active, last_password_change, two_factor_enabled) 
        VALUES (?, ?, ?, ?, 'admin', 1, NOW(), ?)
    ");

    $stmt->execute([
        $username,
        $passwordHash,
        $fullName,
        $email,
        $enable2FA ? 1 : 0
    ]);

    $userId = $db->lastInsertId();

    // Setup 2FA if enabled
    $backupCodes = [];
    if ($enable2FA) {
        $twoFactorAuth = new TwoFactorAuth();
        $result = $twoFactorAuth->setupTwoFactor($userId, $username);

        if ($result['success']) {
            // Mark as verified
            $stmt = $db->prepare("UPDATE two_factor_auth SET is_verified = 1, verified_at = NOW() WHERE user_id = ?");
            $stmt->execute([$userId]);

            $backupCodes = $result['backup_codes'];
        }
    }

    // Log activity
    $securityManager->logSecurityEvent($userId, 'user_created', 'Admin user created via CLI', 'success');

    // Commit transaction
    $db->commit();

    echo "\n";
    echo "╔════════════════════════════════════════════════════════════╗\n";
    echo "║  SUCCESS! Admin user created successfully                 ║\n";
    echo "╚════════════════════════════════════════════════════════════╝\n";
    echo "\n";
    echo "Username: $username\n";
    echo "Email: $email\n";
    echo "2FA Enabled: " . ($enable2FA ? 'Yes' : 'No') . "\n";
    echo "\n";

    if ($enable2FA && !empty($backupCodes)) {
        echo "╔════════════════════════════════════════════════════════════╗\n";
        echo "║  IMPORTANT: Save these backup codes in a secure location  ║\n";
        echo "╚════════════════════════════════════════════════════════════╝\n";
        echo "\n";
        echo "Backup Codes:\n";
        foreach ($backupCodes as $code) {
            echo "  $code\n";
        }
        echo "\n";

        // Save to file
        $filename = "backup_codes_{$username}_" . date('Y-m-d_His') . ".txt";
        $content = "ICOP Admin - Two-Factor Authentication Backup Codes\n";
        $content .= "Generated: " . date('Y-m-d H:i:s') . "\n";
        $content .= "Username: $username\n\n";
        $content .= "IMPORTANT: Keep these codes in a secure location.\n";
        $content .= "Each code can only be used once.\n\n";
        $content .= "Backup Codes:\n";
        foreach ($backupCodes as $code) {
            $content .= "$code\n";
        }

        file_put_contents($filename, $content);
        echo "Backup codes saved to: $filename\n";
        echo "\n";

        echo "Next steps:\n";
        echo "1. Login to the admin panel\n";
        echo "2. Go to Security Settings\n";
        echo "3. Scan the QR code with your authenticator app\n";
        echo "\n";
    }

    echo "You can now login at: " . SITE_URL . "/admin/\n";
    echo "\n";

} catch (PDOException $e) {
    $db->rollBack();
    echo "\nError: Failed to create user\n";
    echo $e->getMessage() . "\n";
    exit(1);
}
