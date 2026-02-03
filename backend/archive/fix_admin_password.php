<?php
/**
 * Update Admin Password Hash
 * Fixes the login issue by updating the admin user password to the correct hash
 */

require_once 'backend/config.php';

try {
    $db = Database::getInstance()->getConnection();
    
    // New correct hash for 'admin123'
    $newHash = '$2y$10$b6XlRF0EQHW2vmAk02UcOefbI6oKT84ywvPWuKFz.yklxNbjoGlae';
    
    $stmt = $db->prepare('UPDATE admin_users SET password = ? WHERE username = ?');
    $stmt->execute([$newHash, 'admin']);
    
    echo "✅ Password updated successfully for admin user\n";
    echo "Username: admin\n";
    echo "Password: admin123\n";
    echo "\nYou can now login at: http://localhost/In-ICOP/admin/\n";
    
} catch (PDOException $e) {
    echo "❌ Error: " . $e->getMessage();
}
?>
