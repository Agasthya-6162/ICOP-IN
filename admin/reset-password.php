<?php
/**
 * Password Reset Form Page
 * Indira College of Pharmacy Admin Panel
 */

require_once 'config.php';

$message = '';
$messageType = '';
$validToken = false;
$token = $_GET['token'] ?? '';

// Validate token
if (!empty($token)) {
    try {
        $db = getDB();
        
        // Check if token exists and is valid
        $stmt = $db->prepare("
            SELECT prt.*, au.username, au.full_name 
            FROM password_reset_tokens prt
            JOIN admin_users au ON prt.user_id = au.id
            WHERE prt.token = ? 
            AND prt.is_used = 0 
            AND prt.expires_at > NOW()
            LIMIT 1
        ");
        $stmt->execute([$token]);
        $tokenData = $stmt->fetch();
        
        if ($tokenData) {
            $validToken = true;
        } else {
            $message = 'This password reset link is invalid or has expired. Please request a new one.';
            $messageType = 'error';
        }
    } catch (PDOException $e) {
        $message = 'An error occurred. Please try again.';
        $messageType = 'error';
        if (DEBUG_MODE) {
            $message .= ' Error: ' . $e->getMessage();
        }
    }
} else {
    $message = 'No reset token provided.';
    $messageType = 'error';
}

// Handle password reset submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $validToken) {
    $newPassword = $_POST['new_password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';
    
    // Validate passwords
    if (empty($newPassword) || empty($confirmPassword)) {
        $message = 'Please enter and confirm your new password.';
        $messageType = 'error';
    } elseif ($newPassword !== $confirmPassword) {
        $message = 'Passwords do not match. Please try again.';
        $messageType = 'error';
    } elseif (strlen($newPassword) < 8) {
        $message = 'Password must be at least 8 characters long.';
        $messageType = 'error';
    } else {
        try {
            $db = getDB();
            
            // Hash new password
            $passwordHash = password_hash($newPassword, PASSWORD_DEFAULT);
            
            // Update user's password
            $stmt = $db->prepare("UPDATE admin_users SET password = ? WHERE id = ?");
            $stmt->execute([$passwordHash, $tokenData['user_id']]);
            
            // Mark token as used
            $stmt = $db->prepare("UPDATE password_reset_tokens SET is_used = 1 WHERE id = ?");
            $stmt->execute([$tokenData['id']]);
            
            // Log activity
            logActivity('Password Reset Completed', 'admin_users', $tokenData['user_id']);
            
            $message = 'Password reset successfully! You can now login with your new password.';
            $messageType = 'success';
            $validToken = false; // Prevent form from showing again
            
        } catch (PDOException $e) {
            $message = 'An error occurred while resetting your password. Please try again.';
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
    <title>Reset Password - ICOP Admin</title>
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
        
        .user-info {
            background: #e7f3ff;
            padding: 15px;
            border-radius: 4px;
            margin-bottom: 25px;
            font-size: 14px;
            color: #004085;
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
        
        .password-input-wrapper {
            position: relative;
        }
        
        .form-group input {
            width: 100%;
            padding: 14px 45px 14px 15px;
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
        
        .toggle-password {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #666;
            font-size: 16px;
        }
        
        .toggle-password:hover {
            color: #003366;
        }
        
        .password-strength {
            margin-top: 8px;
            font-size: 13px;
        }
        
        .password-strength .strength-bar {
            height: 4px;
            background: #ddd;
            border-radius: 2px;
            margin-bottom: 5px;
            overflow: hidden;
        }
        
        .password-strength .strength-fill {
            height: 100%;
            transition: all 0.3s;
            width: 0%;
        }
        
        .password-strength.weak .strength-fill {
            width: 33%;
            background: #dc3545;
        }
        
        .password-strength.medium .strength-fill {
            width: 66%;
            background: #ffc107;
        }
        
        .password-strength.strong .strength-fill {
            width: 100%;
            background: #28a745;
        }
        
        .password-requirements {
            font-size: 12px;
            color: #666;
            margin-top: 8px;
            line-height: 1.6;
        }
        
        .password-requirements li {
            margin-bottom: 4px;
        }
        
        .password-requirements li.met {
            color: #28a745;
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
        
        .btn-submit:hover:not(:disabled) {
            transform: translateY(-2px);
            background: #002244;
            box-shadow: 0 5px 15px rgba(0, 51, 102, 0.2);
        }
        
        .btn-submit:disabled {
            opacity: 0.6;
            cursor: not-allowed;
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
        }
        
        .back-links a:hover {
            color: #003366;
        }
    </style>
</head>
<body>
    <div class="reset-container">
        <div class="reset-header">
            <img src="../images/sanstha-logo.png" alt="Sahayog Sevabhavi Sanstha" class="reset-logo">
            <h1>Reset Your Password</h1>
            <p>Choose a strong, unique password for your account</p>
        </div>
        
        <?php if ($validToken && $tokenData): ?>
            <div class="user-info">
                <i class="fas fa-user"></i> 
                Resetting password for: <strong><?= htmlspecialchars($tokenData['full_name']) ?></strong> 
                (<?= htmlspecialchars($tokenData['username']) ?>)
            </div>
        <?php endif; ?>
        
        <?php if ($message): ?>
            <div class="message-box <?= $messageType ?>">
                <i class="fas fa-<?= $messageType === 'success' ? 'check-circle' : 'exclamation-circle' ?>"></i>
                <div><?= $message ?></div>
            </div>
        <?php endif; ?>
        
        <?php if ($validToken): ?>
            <form method="POST" id="resetForm">
                <div class="form-group">
                    <label for="new_password"><i class="fas fa-lock"></i> New Password</label>
                    <div class="password-input-wrapper">
                        <input 
                            type="password" 
                            id="new_password" 
                            name="new_password" 
                            placeholder="Enter new password"
                            required 
                            autofocus
                            minlength="8"
                        >
                        <i class="fas fa-eye toggle-password" onclick="togglePassword('new_password')"></i>
                    </div>
                    <div class="password-strength" id="strengthIndicator">
                        <div class="strength-bar">
                            <div class="strength-fill"></div>
                        </div>
                        <span class="strength-text"></span>
                    </div>
                    <ul class="password-requirements" id="requirements">
                        <li id="req-length"><i class="fas fa-circle"></i> At least 8 characters</li>
                        <li id="req-letter"><i class="fas fa-circle"></i> Contains letters</li>
                        <li id="req-number"><i class="fas fa-circle"></i> Contains numbers</li>
                    </ul>
                </div>
                
                <div class="form-group">
                    <label for="confirm_password"><i class="fas fa-lock"></i> Confirm Password</label>
                    <div class="password-input-wrapper">
                        <input 
                            type="password" 
                            id="confirm_password" 
                            name="confirm_password" 
                            placeholder="Confirm new password"
                            required
                            minlength="8"
                        >
                        <i class="fas fa-eye toggle-password" onclick="togglePassword('confirm_password')"></i>
                    </div>
                </div>
                
                <button type="submit" class="btn-submit" id="submitBtn">
                    <i class="fas fa-check"></i> Reset Password
                </button>
            </form>
        <?php elseif ($messageType === 'success'): ?>
            <div style="text-align: center; margin-top: 20px;">
                <a href="index.php" class="btn-submit" style="display: inline-flex; align-items: center; justify-content: center; text-decoration: none;">
                    <i class="fas fa-sign-in-alt"></i> Go to Login Page
                </a>
            </div>
        <?php endif; ?>
        
        <div class="back-links">
            <a href="index.php"><i class="fas fa-arrow-left"></i> Back to Login</a>
        </div>
    </div>
    
    <script>
        function togglePassword(fieldId) {
            const field = document.getElementById(fieldId);
            const icon = event.target;
            
            if (field.type === 'password') {
                field.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                field.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }
        
        // Password strength checker
        const passwordField = document.getElementById('new_password');
        const strengthIndicator = document.getElementById('strengthIndicator');
        
        if (passwordField) {
            passwordField.addEventListener('input', function() {
                const password = this.value;
                let strength = 0;
                let strengthText = '';
                let strengthClass = '';
                
                // Check requirements
                const hasLength = password.length >= 8;
                const hasLetter = /[a-zA-Z]/.test(password);
                const hasNumber = /[0-9]/.test(password);
                
                // Update requirement indicators
                document.getElementById('req-length').classList.toggle('met', hasLength);
                document.getElementById('req-letter').classList.toggle('met', hasLetter);
                document.getElementById('req-number').classList.toggle('met', hasNumber);
                
                // Calculate strength
                if (hasLength) strength++;
                if (hasLetter) strength++;
                if (hasNumber) strength++;
                if (password.length >= 12) strength++;
                if (/[!@#$%^&*(),.?":{}|<>]/.test(password)) strength++;
                
                // Set strength class and text
                if (password.length === 0) {
                    strengthClass = '';
                    strengthText = '';
                } else if (strength <= 2) {
                    strengthClass = 'weak';
                    strengthText = 'Weak password';
                } else if (strength <= 3) {
                    strengthClass = 'medium';
                    strengthText = 'Medium password';
                } else {
                    strengthClass = 'strong';
                    strengthText = 'Strong password';
                }
                
                strengthIndicator.className = 'password-strength ' + strengthClass;
                strengthIndicator.querySelector('.strength-text').textContent = strengthText;
            });
        }
        
        // Form validation
        const form = document.getElementById('resetForm');
        if (form) {
            form.addEventListener('submit', function(e) {
                const newPassword = document.getElementById('new_password').value;
                const confirmPassword = document.getElementById('confirm_password').value;
                
                if (newPassword !== confirmPassword) {
                    e.preventDefault();
                    alert('Passwords do not match!');
                    return false;
                }
                
                if (newPassword.length < 8) {
                    e.preventDefault();
                    alert('Password must be at least 8 characters long!');
                    return false;
                }
            });
        }
    </script>
</body>
</html>
