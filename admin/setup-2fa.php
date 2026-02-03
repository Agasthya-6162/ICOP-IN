<?php
require_once 'config.php';
requireAdminLogin();

$securityManager = new SecurityManager();
$twoFactorAuth = new TwoFactorAuth();
$userId = $_SESSION['admin_user_id'];
$username = $_SESSION['admin_username'];

$message = '';
$error = '';
$setupData = null;

// Check if 2FA is already enabled
$twoFactorEnabled = $twoFactorAuth->isEnabled($userId);

// Handle 2FA setup
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['setup_2fa'])) {
    $result = $twoFactorAuth->setupTwoFactor($userId, $username);

    if ($result['success']) {
        $setupData = $result;
        $_SESSION['pending_2fa_setup'] = true;
    } else {
        $error = $result['message'];
    }
}

// Handle 2FA verification and activation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['verify_setup'])) {
    $code = $_POST['verification_code'] ?? '';

    if (empty($code)) {
        $error = 'Please enter the verification code';
    } else {
        $result = $twoFactorAuth->verifyAndActivate($userId, $code);

        if ($result['success']) {
            unset($_SESSION['pending_2fa_setup']);
            $securityManager->logSecurityEvent($userId, '2fa_enabled', 'User enabled 2FA', 'success');
            header('Location: security-settings.php?2fa_enabled=1');
            exit;
        } else {
            $error = $result['message'];
        }
    }
}

// Handle 2FA disable
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['disable_2fa'])) {
    $password = $_POST['password'] ?? '';

    if (empty($password)) {
        $error = 'Please enter your password to disable 2FA';
    } else {
        // Verify password
        $db = getDB();
        $stmt = $db->prepare("SELECT password FROM admin_users WHERE id = ?");
        $stmt->execute([$userId]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            $result = $twoFactorAuth->disable($userId);

            if ($result['success']) {
                $securityManager->logSecurityEvent($userId, '2fa_disabled', 'User disabled 2FA', 'warning');
                header('Location: security-settings.php?2fa_disabled=1');
                exit;
            } else {
                $error = $result['message'];
            }
        } else {
            $error = 'Invalid password';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Two-Factor Authentication Setup - ICOP Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap');

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Roboto', sans-serif;
            background: #f0f2f5;
            padding: 20px;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            padding: 40px;
        }

        h1 {
            color: #003366;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .subtitle {
            color: #666;
            margin-bottom: 30px;
            font-size: 14px;
        }

        .alert {
            padding: 15px;
            border-radius: 4px;
            margin-bottom: 20px;
        }

        .alert-success {
            background: #d4edda;
            border: 1px solid #c3e6cb;
            color: #155724;
        }

        .alert-error {
            background: #f8d7da;
            border: 1px solid #f5c6cb;
            color: #721c24;
        }

        .alert-warning {
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            color: #856404;
        }

        .alert-info {
            background: #d1ecf1;
            border: 1px solid #bee5eb;
            color: #0c5460;
        }

        .setup-box {
            border: 2px solid #003366;
            border-radius: 8px;
            padding: 30px;
            margin: 20px 0;
            text-align: center;
        }

        .qr-code {
            margin: 20px 0;
            padding: 20px;
            background: white;
            display: inline-block;
            border: 1px solid #ddd;
            border-radius: 8px;
        }

        .qr-code img {
            max-width: 250px;
            height: auto;
        }

        .secret-key {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 4px;
            font-family: 'Courier New', monospace;
            font-size: 16px;
            letter-spacing: 2px;
            margin: 15px 0;
            word-break: break-all;
        }

        .backup-codes {
            background: #fff3cd;
            border: 2px solid #ffc107;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
        }

        .backup-codes h3 {
            color: #856404;
            margin-bottom: 15px;
        }

        .codes-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 10px;
            margin: 15px 0;
        }

        .code-item {
            background: white;
            padding: 10px;
            border-radius: 4px;
            font-family: 'Courier New', monospace;
            font-size: 16px;
            font-weight: bold;
            text-align: center;
        }

        .form-group {
            margin: 20px 0;
            text-align: left;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: #333;
        }

        .form-group input {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 16px;
        }

        .btn {
            padding: 12px 30px;
            border: none;
            border-radius: 4px;
            font-size: 16px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s;
            text-decoration: none;
            display: inline-block;
            margin: 5px;
        }

        .btn-primary {
            background: #003366;
            color: white;
        }

        .btn-primary:hover {
            background: #002244;
        }

        .btn-danger {
            background: #dc3545;
            color: white;
        }

        .btn-danger:hover {
            background: #c82333;
        }

        .btn-secondary {
            background: #6c757d;
            color: white;
        }

        .btn-secondary:hover {
            background: #5a6268;
        }

        .steps {
            text-align: left;
            margin: 20px 0;
        }

        .step {
            margin: 15px 0;
            padding-left: 30px;
            position: relative;
        }

        .step::before {
            content: attr(data-step);
            position: absolute;
            left: 0;
            top: 0;
            background: #003366;
            color: white;
            width: 24px;
            height: 24px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            font-weight: bold;
        }

        .back-link {
            margin-top: 30px;
            text-align: center;
        }

        .back-link a {
            color: #003366;
            text-decoration: none;
        }

        .back-link a:hover {
            text-decoration: underline;
        }

        .status-enabled {
            color: #28a745;
            font-weight: bold;
        }

        .status-disabled {
            color: #dc3545;
            font-weight: bold;
        }
    </style>
</head>

<body>
    <div class="container">
        <h1>
            <i class="fas fa-shield-alt"></i>
            Two-Factor Authentication
        </h1>
        <p class="subtitle">Add an extra layer of security to your account</p>

        <?php if (!empty($error)): ?>
            <div class="alert alert-error">
                <i class="fas fa-exclamation-circle"></i>
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($message)): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i>
                <?= htmlspecialchars($message) ?>
            </div>
        <?php endif; ?>

        <?php if ($setupData): ?>
            <!-- Setup Instructions -->
            <div class="alert alert-info">
                <strong><i class="fas fa-info-circle"></i> Setup Instructions</strong><br>
                Follow the steps below to enable two-factor authentication on your account.
            </div>

            <div class="steps">
                <div class="step" data-step="1">
                    <strong>Download an Authenticator App</strong><br>
                    <small>Install Google Authenticator, Authy, or Microsoft Authenticator on your phone</small>
                </div>

                <div class="step" data-step="2">
                    <strong>Scan the QR Code</strong><br>
                    <small>Open your authenticator app and scan the QR code below</small>
                </div>

                <div class="step" data-step="3">
                    <strong>Enter Verification Code</strong><br>
                    <small>Enter the 6-digit code from your app to verify the setup</small>
                </div>
            </div>

            <div class="setup-box">
                <h3>Scan this QR Code</h3>
                <div class="qr-code">
                    <img src="<?= htmlspecialchars($setupData['qr_code_url']) ?>" alt="2FA QR Code">
                </div>

                <p><strong>Or enter this secret key manually:</strong></p>
                <div class="secret-key">
                    <?= htmlspecialchars($setupData['secret']) ?>
                </div>
            </div>

            <!-- Backup Codes -->
            <div class="backup-codes">
                <h3><i class="fas fa-key"></i> Backup Codes</h3>
                <p><strong>Important:</strong> Save these backup codes in a secure location. You can use them to access your
                    account if you lose your phone.</p>

                <div class="codes-grid">
                    <?php foreach ($setupData['backup_codes'] as $code): ?>
                        <div class="code-item">
                            <?= htmlspecialchars($code) ?>
                        </div>
                    <?php endforeach; ?>
                </div>

                <button onclick="downloadBackupCodes()" class="btn btn-secondary">
                    <i class="fas fa-download"></i> Download Backup Codes
                </button>
            </div>

            <!-- Verification Form -->
            <form method="POST">
                <div class="form-group">
                    <label for="verification_code">Enter Verification Code from App:</label>
                    <input type="text" id="verification_code" name="verification_code" placeholder="000000" maxlength="6"
                        pattern="[0-9]{6}" required autofocus
                        style="text-align: center; font-size: 24px; letter-spacing: 10px;">
                </div>

                <button type="submit" name="verify_setup" class="btn btn-primary">
                    <i class="fas fa-check"></i> Verify and Enable 2FA
                </button>
                <a href="security-settings.php" class="btn btn-secondary">Cancel</a>
            </form>

        <?php elseif ($twoFactorEnabled): ?>
            <!-- 2FA Already Enabled -->
            <div class="alert alert-success">
                <strong><i class="fas fa-check-circle"></i> Two-Factor Authentication is Enabled</strong><br>
                Your account is protected with 2FA. You'll need to enter a code from your authenticator app when logging in.
            </div>

            <div class="alert alert-warning">
                <strong><i class="fas fa-exclamation-triangle"></i> Disable 2FA</strong><br>
                If you want to disable two-factor authentication, enter your password below. This will make your account
                less secure.
            </div>

            <form method="POST"
                onsubmit="return confirm('Are you sure you want to disable 2FA? This will make your account less secure.');">
                <div class="form-group">
                    <label for="password">Enter Your Password to Disable 2FA:</label>
                    <input type="password" id="password" name="password" placeholder="Enter your password" required>
                </div>

                <button type="submit" name="disable_2fa" class="btn btn-danger">
                    <i class="fas fa-times"></i> Disable 2FA
                </button>
                <a href="security-settings.php" class="btn btn-secondary">Cancel</a>
            </form>

        <?php else: ?>
            <!-- Enable 2FA -->
            <div class="alert alert-warning">
                <strong><i class="fas fa-exclamation-triangle"></i> Two-Factor Authentication is Disabled</strong><br>
                Your account is not protected with 2FA. Enable it now to add an extra layer of security.
            </div>

            <div style="margin: 30px 0;">
                <h3>Why Enable 2FA?</h3>
                <ul style="margin: 15px 0; padding-left: 20px;">
                    <li>Protects your account even if your password is compromised</li>
                    <li>Prevents unauthorized access to sensitive data</li>
                    <li>Industry-standard security practice</li>
                    <li>Takes only a few minutes to set up</li>
                </ul>
            </div>

            <form method="POST">
                <button type="submit" name="setup_2fa" class="btn btn-primary">
                    <i class="fas fa-shield-alt"></i> Enable Two-Factor Authentication
                </button>
                <a href="security-settings.php" class="btn btn-secondary">Maybe Later</a>
            </form>
        <?php endif; ?>

        <div class="back-link">
            <a href="security-settings.php"><i class="fas fa-arrow-left"></i> Back to Security Settings</a>
        </div>
    </div>

    <script>
        function downloadBackupCodes() {
            const codes = <?= json_encode($setupData['backup_codes'] ?? []) ?>;
            const text = "ICOP Admin - Two-Factor Authentication Backup Codes\n" +
                "Generated: <?= date('Y-m-d H:i:s') ?>\n" +
                "Username: <?= htmlspecialchars($username) ?>\n\n" +
                "IMPORTANT: Keep these codes in a secure location.\n" +
                "Each code can only be used once.\n\n" +
                "Backup Codes:\n" +
                codes.join('\n');

            const blob = new Blob([text], { type: 'text/plain' });
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = 'icop-2fa-backup-codes.txt';
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
            window.URL.revokeObjectURL(url);
        }
    </script>
</body>

</html>