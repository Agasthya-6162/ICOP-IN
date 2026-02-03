<?php
/**
 * Quick Password Reset Tool
 * Use this to reset a user's password when login isn't working
 * 
 * Usage: php reset_password.php username new_password
 */

require_once __DIR__ . '/config.php';

echo "\n";
echo "╔════════════════════════════════════════════════════════════╗\n";
echo "║  ICOP Admin - Password Reset Tool                         ║\n";
echo "╚════════════════════════════════════════════════════════════╝\n";
echo "\n";

// Get credentials from command line or prompt
if ($argc >= 3) {
    $username = $argv[1];
    $newPassword = $argv[2];
} else {
    echo "Enter username: ";
    $username = trim(fgets(STDIN));

    echo "Enter new password: ";
    if (strtoupper(substr(PHP_OS, 0, 3)) !== 'WIN') {
        system('stty -echo');
        $newPassword = trim(fgets(STDIN));
        system('stty echo');
        echo "\n";
    } else {
        $newPassword = trim(fgets(STDIN));
    }
}

echo "\n";

try {
    $db = Database::getInstance()->getConnection();
    $securityManager = new SecurityManager();

    // Check if user exists
    $stmt = $db->prepare("SELECT id, username, full_name FROM admin_users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        echo "❌ ERROR: User '$username' not found\n\n";

        echo "Available users:\n";
        $stmt = $db->query("SELECT username, full_name FROM admin_users");
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            echo "  - {$row['username']} ({$row['full_name']})\n";
        }
        exit(1);
    }

    echo "Resetting password for: {$user['full_name']} ({$user['username']})\n";
    echo "═══════════════════════════════════════════════════════════\n\n";

    // Validate password strength
    echo "Validating password strength...\n";
    $validation = $securityManager->validatePasswordStrength($newPassword);

    if (!$validation['valid']) {
        echo "❌ Password validation failed:\n";
        foreach ($validation['errors'] as $error) {
            echo "  - $error\n";
        }
        echo "\nPassword Requirements:\n";
        echo "  - Minimum 12 characters\n";
        echo "  - At least one uppercase letter\n";
        echo "  - At least one lowercase letter\n";
        echo "  - At least one number\n";
        echo "  - At least one special character\n";
        exit(1);
    }

    echo "✓ Password meets requirements\n\n";

    // Generate password hash
    echo "Generating password hash...\n";
    $passwordHash = password_hash($newPassword, PASSWORD_DEFAULT);
    echo "✓ Hash generated\n\n";

    // Update password
    echo "Updating password in database...\n";
    $stmt = $db->prepare("
        UPDATE admin_users 
        SET password = ?, 
            last_password_change = NOW(),
            failed_login_attempts = 0,
            locked_until = NULL,
            force_password_change = 0
        WHERE id = ?
    ");

    $stmt->execute([$passwordHash, $user['id']]);
    echo "✓ Password updated\n\n";

    // Log the event
    $securityManager->logSecurityEvent($user['id'], 'password_reset', 'Password reset via CLI tool', 'success');

    echo "╔════════════════════════════════════════════════════════════╗\n";
    echo "║  SUCCESS! Password has been reset                         ║\n";
    echo "╚════════════════════════════════════════════════════════════╝\n";
    echo "\n";
    echo "Username: {$user['username']}\n";
    echo "New Password: $newPassword\n";
    echo "\n";
    echo "✓ Account unlocked (if it was locked)\n";
    echo "✓ Failed login attempts cleared\n";
    echo "✓ Password change logged in security audit\n";
    echo "\n";
    echo "You can now login at: http://localhost/In-ICOP/admin/\n";
    echo "\n";

} catch (Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
    exit(1);
}
