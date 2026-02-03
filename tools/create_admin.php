<?php
/**
 * Admin User Setup Script
 * Creates/Resets the default admin user
 */

require_once __DIR__ . '/config.php';

try {
    $db = Database::getInstance()->getConnection();
    
    // Delete existing admin user if exists
    $db->exec("DELETE FROM admin_users WHERE username = 'admin'");
    
    // Create new admin user with proper password hash
    $username = 'admin';
    $password = 'admin123';
    $passwordHash = password_hash($password, PASSWORD_DEFAULT);
    $fullName = 'Administrator';
    $email = 'admin@icop.edu.in';
    
    $stmt = $db->prepare("
        INSERT INTO admin_users (username, password, full_name, email, role, is_active) 
        VALUES (?, ?, ?, ?, 'admin', 1)
    ");
    
    $stmt->execute([$username, $passwordHash, $fullName, $email]);
    
    echo "<!DOCTYPE html>
<html lang='en'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Admin User Setup - ICOP</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .container {
            background: white;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
            max-width: 500px;
            text-align: center;
        }
        h1 {
            color: #28a745;
            margin-bottom: 20px;
        }
        .info-box {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 5px;
            margin: 20px 0;
            text-align: left;
        }
        .info-box strong {
            color: #333;
        }
        .btn {
            display: inline-block;
            padding: 12px 24px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin: 10px 5px;
            font-weight: 600;
        }
        .btn:hover {
            opacity: 0.9;
        }
        .warning {
            background: #fff3cd;
            border: 1px solid #ffc107;
            padding: 15px;
            border-radius: 5px;
            margin-top: 20px;
            color: #856404;
        }
    </style>
</head>
<body>
    <div class='container'>
        <h1>✅ Admin User Created Successfully!</h1>
        
        <div class='info-box'>
            <p><strong>Username:</strong> admin</p>
            <p><strong>Password:</strong> admin123</p>
            <p><strong>Email:</strong> admin@icop.edu.in</p>
            <p><strong>Role:</strong> Administrator</p>
        </div>
        
        <div class='warning'>
            <strong>⚠️ Important:</strong> Change the default password after logging in!
        </div>
        
        <div style='margin-top: 30px;'>
            <a href='../admin/' class='btn'>Go to Admin Login</a>
            <a href='init_check.php' class='btn'>Check System Status</a>
        </div>
        
        <p style='margin-top: 30px; color: #666; font-size: 14px;'>
            You can now login to the admin panel using the credentials above.
        </p>
    </div>
</body>
</html>";
    
} catch (PDOException $e) {
    echo "<!DOCTYPE html>
<html>
<head>
    <title>Error - Admin Setup</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f5f5f5;
            padding: 40px;
            text-align: center;
        }
        .error {
            background: #f8d7da;
            color: #721c24;
            padding: 20px;
            border-radius: 5px;
            max-width: 600px;
            margin: 0 auto;
        }
    </style>
</head>
<body>
    <div class='error'>
        <h1>❌ Error Creating Admin User</h1>
        <p><strong>Error:</strong> " . htmlspecialchars($e->getMessage()) . "</p>
        <p>Please make sure the database is set up correctly.</p>
        <p><a href='db_setup.php'>Run Database Setup</a></p>
    </div>
</body>
</html>";
}
?>
