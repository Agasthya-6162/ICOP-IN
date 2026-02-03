<?php
/**
 * Database Update Script - Add Password Reset Table
 * Run this once to add the password_reset_tokens table
 */

require_once __DIR__ . '/config.php';

try {
    $db = Database::getInstance()->getConnection();
    
    echo "<!DOCTYPE html>
    <html>
    <head>
        <title>Database Update - ICOP</title>
        <style>
            body { font-family: Arial, sans-serif; padding: 40px; background: #f0f2f5;  }
            .container { max-width: 800px; margin: 0 auto; background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
            h1 { color: #003366; margin-bottom: 20px; }
            .success { background: #d4edda; color: #155724; padding: 15px; border-radius: 4px; margin: 10px 0; border-left: 5px solid #28a745; }
            .error { background: #f8d7da; color: #721c24; padding: 15px; border-radius: 4px; margin: 10px 0; border-left: 5px solid #dc3545; }
            .info { background: #d1ecf1; color: #0c5460; padding: 15px; border-radius: 4px; margin: 10px 0; border-left: 5px solid #17a2b8; }
            .btn { display: inline-block; padding: 10px 20px; background: #003366; color: white; text-decoration: none; border-radius: 4px; margin-top: 20px; }
        </style>
    </head>
    <body>
        <div class='container'>
            <h1>🔄 Database Update Script</h1>";
    
    // Check if password_reset_tokens table exists
    try {
        $stmt = $db->query("SELECT 1 FROM password_reset_tokens LIMIT 1");
        echo "<div class='info'>✅ Password reset tokens table already exists. No update needed.</div>";
    } catch (PDOException $e) {
        // Table doesn't exist, create it
        echo "<div class='info'>Creating password_reset_tokens table...</div>";
        
        $sql = "CREATE TABLE IF NOT EXISTS password_reset_tokens (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            token VARCHAR(255) UNIQUE NOT NULL,
            expires_at TIMESTAMP NOT NULL,
            is_used TINYINT(1) DEFAULT 0,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            INDEX idx_token (token),
            INDEX idx_user_id (user_id),
            FOREIGN KEY (user_id) REFERENCES admin_users(id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
        
        $db->exec($sql);
        echo "<div class='success'>✅ Password reset tokens table created successfully!</div>";
    }
    
    // Verify admin user exists
    $stmt = $db->query("SELECT COUNT(*) as count FROM admin_users");
    $count = $stmt->fetch()['count'];
    
    if ($count == 0) {
        echo "<div class='error'>⚠️  No admin users found. Please run backend/create_admin.php to create an admin user.</div>";
    } else {
        echo "<div class='success'>✅ Found {$count} admin user(s) in database.</div>";
    }
    
    echo "<p style='margin-top: 30px;'><strong>Update Complete!</strong></p>
            <a href='../admin/' class='btn'>Go to Admin Panel</a>
            <a href='diagnose_admin.php' class='btn' style='background: #28a745;'>Run Diagnostics</a>
        </div>
    </body>
    </html>";
    
} catch (PDOException $e) {
    echo "<div class='error'>❌ Database Error: " . htmlspecialchars($e->getMessage()) . "</div>";
    echo "<p>Please check your database configuration in backend/config.php</p></div></body></html>";
}
?>
