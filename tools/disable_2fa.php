<?php
/**
 * Disable 2FA for a user
 */

require_once __DIR__ . '/config.php';

$username = 'ICOP@731';

try {
    $db = Database::getInstance()->getConnection();

    echo "\nDisabling 2FA for user: $username\n";
    echo "═══════════════════════════════════════════════════════════\n\n";

    // Disable 2FA
    $stmt = $db->prepare("UPDATE admin_users SET two_factor_enabled = 0 WHERE username = ?");
    $stmt->execute([$username]);

    // Delete 2FA data
    $stmt = $db->prepare("DELETE FROM two_factor_auth WHERE user_id = (SELECT id FROM admin_users WHERE username = ?)");
    $stmt->execute([$username]);

    echo "✓ 2FA disabled\n";
    echo "✓ 2FA data removed\n\n";

    echo "╔════════════════════════════════════════════════════════════╗\n";
    echo "║  SUCCESS! 2FA has been disabled                           ║\n";
    echo "╚════════════════════════════════════════════════════════════╝\n";
    echo "\n";
    echo "You can now login with:\n";
    echo "  Username: ICOP@731\n";
    echo "  Password: Agasthya@731\n";
    echo "\n";
    echo "No 2FA code needed!\n";
    echo "\n";

} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    exit(1);
}
