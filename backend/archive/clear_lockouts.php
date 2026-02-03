<?php
/**
 * Clear Login Attempt Lockouts
 * Removes all failed login attempt records to clear IP lockouts
 */

require_once 'backend/config.php';

try {
    $db = Database::getInstance()->getConnection();

    // Delete all login attempt records
    $stmt = $db->prepare('DELETE FROM login_attempts');
    $stmt->execute();

    $deletedCount = $stmt->rowCount();

    echo "✅ Cleared $deletedCount login attempt record(s)\n";
    echo "All IP lockouts have been removed\n";
    echo "\nYou can now login at: http://localhost/In-ICOP/admin/\n";
    echo "Username: admin\n";
    echo "Password: admin123\n";

} catch (PDOException $e) {
    echo "❌ Error: " . $e->getMessage();
}
?>