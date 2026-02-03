<?php
/**
 * Password Reset Request Page
 * Indira College of Pharmacy Admin Panel
 */

require_once 'config.php';

$message = '';
$messageType = '';
$resetLink = '';

// Handle password reset request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = sanitize($_POST['username'] ?? '');
    
    if (empty($username)) {
        $message = 'Please enter your username or email address.';
        $messageType = 'error';
    } else {
        try {
            $db = getDB();
            
            // Find user by username or email
            $stmt = $db->prepare("SELECT * FROM admin_users WHERE (username = ? OR email = ?) AND is_active = 1 LIMIT 1");
            $stmt->execute([$username, $username]);
            $user = $stmt->fetch();
            
            if ($user) {
                // Generate secure reset token
                $token = bin2hex(random_bytes(32));
                $expiresAt = date('Y-m-d H:i:s', strtotime('+1 hour'));
                
                // Store token in database
                $stmt = $db->prepare("
                    INSERT INTO password_reset_tokens (user_id, token, expires_at) 
                    VALUES (?, ?, ?)
                ");
                $stmt->execute([$user['id'], $token, $expiresAt]);
                
                // Generate reset link
                $resetLink = SITE_URL . '/admin/reset-password.php?token=' . $token;
                
                // Log activity
                logActivity('Password Reset Requested', 'admin_users', $user['id']);
                
                $message = 'Password reset link generated successfully!';
                $messageType = 'success';
                
                // In production, you would send this via email
                // For now, we'll display it directly
                
            } else {
                // Don't reveal if user exists or not (security best practice)
                $message = 'If an account with that username/email exists, a password reset link has been generated.';
                $messageType = 'info';
            }
        } catch (PDOException $e) {
            $message = 'An error occurred. Please try again later.';
            $messageType = 'error';
            if (DEBUG_MODE) {
                $message .= ' Error: ' . $e->getMessage();
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password - ICOP Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Merriweather:wght@300;400;700&family=Roboto:wght@300;400;500;700&display=swap');

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Roboto', sans-serif;
            background: #f0f2f5;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            background-image: linear-gradient(to bottom, #003366 0%, #003366 50%, #f0f2f5 50%, #f0f2f5 100%);
        }

        .reset-container {
            background: #ffffff;
            padding: 50px;
            border-radius: 8px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 500px;
            color: #333;
            border-top: 5px solid #c0a16b;
        }
        
        .reset-header {
            text-align: center;
            margin-bottom: 40px;
        }

        .reset-logo {
            width: 100px;
            height: auto;
            margin-bottom: 20px;
        }
        
        .reset-header h1 {
            font-family: 'Merriweather', serif;
            color: #003366;
            font-size: 28px;
            margin-bottom: 10px;
            font-weight: 700;
        }
        
        .reset-header p {
            color: #666;
            font-size: 15px;
            line-height: 1.6;
        }
        
        .message-box {
            padding: 15px;
            border-radius: 4px;
            margin-bottom: 25px;
            display: flex;
            align-items: flex-start;
            gap: 10px;
        }
        
        .message-box.success {
            background: #d4edda;
            border: 1px solid #c3e6cb;
            color: #155724;
        }
        
        .message-box.error {
            background: #f8d7da;
            border: 1px solid #f5c6cb;
            color: #721c24;
        }
        
        .message-box.info {
            background: #d1ecf1;
            border: 1px solid #bee5eb;
            color: #0c5460;
        }
        
        .reset-link-box {
            background: #fff3cd;
            border: 2px solid #ffc107;
            padding: 20px;
            border-radius: 6px;
            margin-top: 20px;
        }
        
        .reset-link-box h3 {
            color: #856404;
            margin-bottom: 15px;
            font-size: 16px;
        }
        
        .reset-link-box .link {
            background: white;
            padding: 12px;
            border-radius: 4px;
            word-break: break-all;
            font-size: 13px;
            font-family: monospace;
            color: #003366;
            margin-bottom: 15px;
        }
        
        .reset-link-box .copy-btn {
            width: 100%;
            padding: 10px;
            background: #003366;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 600;
            transition: all 0.3s;
        }
        
        .reset-link-box .copy-btn:hover {
            background: #002244;
        }
        
        .form-group {
            margin-bottom: 25px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 10px;
            color: #444;
            font-weight: 500;
            font-size: 14px;
        }
        
        .form-group input {
            width: 100%;
            padding: 14px 15px;
            background: #f9f9f9;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 15px;
            color: #333;
            transition: all 0.3s;
        }
        
        .form-group input::placeholder {
            color: #aaa;
        }
        
        .form-group input:focus {
            outline: none;
            background: #fff;
            border-color: #003366;
            box-shadow: 0 0 0 3px rgba(0, 51, 102, 0.1);
        }
        
        .btn-submit {
            width: 100%;
            padding: 14px;
            background: #003366;
            color: white;
            border: none;
            border-radius: 4px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .btn-submit:hover {
            transform: translateY(-2px);
            background: #002244;
            box-shadow: 0 5px 15px rgba(0, 51, 102, 0.2);
        }
        
        .back-links {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #eee;
        }
        
        .back-links a {
            color: #666;
            text-decoration: none;
            font-size: 14px;
            transition: color 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            margin: 0 10px;
        }
        
        .back-links a:hover {
            color: #003366;
        }
        
        .info-text {
            background: #e7f3ff;
            padding: 15px;
            border-radius: 4px;
            font-size: 14px;
            color: #004085;
            margin-top: 20px;
            line-height: 1.6;
        }
    </style>
</head>
<body>
    <div class="reset-container">
        <div class="reset-header">
            <img src="../images/sanstha-logo.png" alt="Sahayog Sevabhavi Sanstha" class="reset-logo">
            <h1>Forgot Password?</h1>
            <p>Enter your username or email address to reset your password</p>
        </div>
        
        <?php if ($message): ?>
            <div class="message-box <?= $messageType ?>">
                <i class="fas fa-<?= $messageType === 'success' ? 'check-circle' : ($messageType === 'error' ? 'exclamation-circle' : 'info-circle') ?>"></i>
                <div><?= $message ?></div>
            </div>
        <?php endif; ?>
        
        <?php if ($resetLink): ?>
            <div class="reset-link-box">
                <h3><i class="fas fa-link"></i> Your Password Reset Link</h3>
                <p style="color: #856404; margin-bottom: 10px; font-size: 13px;">
                    This link will expire in 1 hour. Copy and paste it into your browser to reset your password.
                </p>
                <div class="link" id="resetLink"><?= $resetLink ?></div>
                <button class="copy-btn" onclick="copyResetLink()">
                    <i class="fas fa-copy"></i> Copy Link to Clipboard
                </button>
            </div>
            
            <div class="info-text">
                <i class="fas fa-info-circle"></i> <strong>Note:</strong> In a production environment, this link would be sent to your email address. For testing purposes, the link is displayed here.
            </div>
        <?php else: ?>
            <form method="POST">
                <div class="form-group">
                    <label for="username"><i class="fas fa-user"></i> Username or Email</label>
                    <input 
                        type="text" 
                        id="username" 
                        name="username" 
                        placeholder="Enter your username or email"
                        required 
                        autofocus
                    >
                </div>
                
                <button type="submit" class="btn-submit">
                    <i class="fas fa-paper-plane"></i> Request Password Reset
                </button>
            </form>
        <?php endif; ?>
        
        <div class="back-links">
            <a href="index.php"><i class="fas fa-arrow-left"></i> Back to Login</a>
            <span style="color: #ddd;">|</span>
            <a href="../index.php"><i class="fas fa-home"></i> Main Website</a>
        </div>
    </div>
    
    <script>
        function copyResetLink() {
            const linkElement = document.getElementById('resetLink');
            const link = linkElement.textContent;
            
            // Create temporary textarea to copy text
            const textarea = document.createElement('textarea');
            textarea.value = link;
            document.body.appendChild(textarea);
            textarea.select();
            document.execCommand('copy');
            document.body.removeChild(textarea);
            
            // Show feedback
            const btn = event.target;
            const originalText = btn.innerHTML;
            btn.innerHTML = '<i class="fas fa-check"></i> Link Copied!';
            btn.style.background = '#28a745';
            
            setTimeout(() => {
                btn.innerHTML = originalText;
                btn.style.background = '#003366';
            }, 2000);
        }
    </script>
</body>
</html>
