<?php
require_once 'config.php';
requireAdminLogin();

$securityManager = new SecurityManager();
$sessionManager = new SessionManager();
$twoFactorAuth = new TwoFactorAuth();
$userId = $_SESSION['admin_user_id'];

$message = '';
$error = '';

// Get user info
$db = getDB();
$stmt = $db->prepare("SELECT * FROM admin_users WHERE id = ?");
$stmt->execute([$userId]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Get 2FA status
$twoFactorEnabled = $twoFactorAuth->isEnabled($userId);
$backupCodesCount = $twoFactorAuth->getRemainingBackupCodes($userId);

// Get active sessions
$sessionsResult = $sessionManager->getUserSessions($userId);
$activeSessions = $sessionsResult['sessions'] ?? [];

// Handle messages from redirects
if (isset($_GET['2fa_enabled'])) {
    $message = '2FA has been successfully enabled!';
} elseif (isset($_GET['2fa_disabled'])) {
    $message = '2FA has been disabled.';
} elseif (isset($_GET['password_changed'])) {
    $message = 'Password changed successfully!';
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Security Settings - ICOP Admin</title>
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
            max-width: 1000px;
            margin: 0 auto;
        }

        .header {
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }

        .header h1 {
            color: #003366;
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 10px;
        }

        .header .subtitle {
            color: #666;
            font-size: 14px;
        }

        .section {
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }

        .section h2 {
            color: #003366;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 20px;
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

        .status-badge {
            display: inline-block;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
        }

        .status-enabled {
            background: #d4edda;
            color: #155724;
        }

        .status-disabled {
            background: #f8d7da;
            color: #721c24;
        }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin: 20px 0;
        }

        .info-item {
            padding: 15px;
            background: #f8f9fa;
            border-radius: 4px;
        }

        .info-item label {
            display: block;
            font-size: 12px;
            color: #666;
            margin-bottom: 5px;
            text-transform: uppercase;
        }

        .info-item value {
            display: block;
            font-size: 16px;
            color: #333;
            font-weight: 500;
        }

        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s;
            text-decoration: none;
            display: inline-block;
            margin: 5px 5px 5px 0;
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

        .sessions-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }

        .sessions-table th,
        .sessions-table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        .sessions-table th {
            background: #f8f9fa;
            font-weight: 600;
            color: #333;
        }

        .current-session {
            background: #d1ecf1;
        }

        .back-link {
            margin-top: 20px;
            text-align: center;
        }

        .back-link a {
            color: #003366;
            text-decoration: none;
        }

        .back-link a:hover {
            text-decoration: underline;
        }

        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
        }

        .modal-content {
            background: white;
            margin: 10% auto;
            padding: 30px;
            border-radius: 8px;
            max-width: 500px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
        }

        .modal-header {
            margin-bottom: 20px;
        }

        .modal-header h3 {
            color: #003366;
            margin-bottom: 10px;
        }

        .form-group {
            margin: 15px 0;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: #333;
        }

        .form-group input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
        }

        .close {
            float: right;
            font-size: 28px;
            font-weight: bold;
            color: #aaa;
            cursor: pointer;
        }

        .close:hover {
            color: #000;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h1>
                <i class="fas fa-shield-alt"></i>
                Security Settings
            </h1>
            <p class="subtitle">Manage your account security and active sessions</p>
        </div>

        <?php if (!empty($message)): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i>
                <?= htmlspecialchars($message) ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($error)): ?>
            <div class="alert alert-error">
                <i class="fas fa-exclamation-circle"></i>
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <!-- Account Information -->
        <div class="section">
            <h2><i class="fas fa-user"></i> Account Information</h2>
            <div class="info-grid">
                <div class="info-item">
                    <label>Username</label>
                    <value>
                        <?= htmlspecialchars($user['username']) ?>
                    </value>
                </div>
                <div class="info-item">
                    <label>Full Name</label>
                    <value>
                        <?= htmlspecialchars($user['full_name']) ?>
                    </value>
                </div>
                <div class="info-item">
                    <label>Email</label>
                    <value>
                        <?= htmlspecialchars($user['email']) ?>
                    </value>
                </div>
                <div class="info-item">
                    <label>Last Login</label>
                    <value>
                        <?= $user['last_login'] ? date('M d, Y H:i', strtotime($user['last_login'])) : 'Never' ?>
                    </value>
                </div>
            </div>
        </div>

        <!-- Two-Factor Authentication -->
        <div class="section">
            <h2><i class="fas fa-mobile-alt"></i> Two-Factor Authentication</h2>
            <p style="margin-bottom: 15px;">
                Status:
                <?php if ($twoFactorEnabled): ?>
                    <span class="status-badge status-enabled"><i class="fas fa-check"></i> Enabled</span>
                <?php else: ?>
                    <span class="status-badge status-disabled"><i class="fas fa-times"></i> Disabled</span>
                <?php endif; ?>
            </p>

            <?php if ($twoFactorEnabled): ?>
                <p>Your account is protected with two-factor authentication.</p>
                <p style="margin: 10px 0;">Backup codes remaining: <strong>
                        <?= $backupCodesCount ?>
                    </strong></p>
                <?php if ($backupCodesCount < 3): ?>
                    <div class="alert alert-error" style="margin: 15px 0;">
                        <i class="fas fa-exclamation-triangle"></i> You have less than 3 backup codes remaining. Consider
                        generating new ones.
                    </div>
                <?php endif; ?>
                <a href="setup-2fa.php" class="btn btn-danger">
                    <i class="fas fa-times"></i> Disable 2FA
                </a>
            <?php else: ?>
                <p>Add an extra layer of security to your account by enabling two-factor authentication.</p>
                <a href="setup-2fa.php" class="btn btn-primary">
                    <i class="fas fa-shield-alt"></i> Enable 2FA
                </a>
            <?php endif; ?>
        </div>

        <!-- Password Management -->
        <div class="section">
            <h2><i class="fas fa-key"></i> Password Management</h2>
            <p style="margin-bottom: 15px;">
                Last changed:
                <?= $user['last_password_change'] ? date('M d, Y', strtotime($user['last_password_change'])) : 'Never' ?>
            </p>
            <button onclick="openPasswordModal()" class="btn btn-primary">
                <i class="fas fa-lock"></i> Change Password
            </button>
        </div>

        <!-- Active Sessions -->
        <div class="section">
            <h2><i class="fas fa-desktop"></i> Active Sessions</h2>
            <p style="margin-bottom: 15px;">These are the devices currently logged into your account.</p>

            <?php if (!empty($activeSessions)): ?>
                <table class="sessions-table">
                    <thead>
                        <tr>
                            <th>Device</th>
                            <th>IP Address</th>
                            <th>Last Activity</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($activeSessions as $session): ?>
                            <tr class="<?= $session['is_current'] ? 'current-session' : '' ?>">
                                <td>
                                    <i
                                        class="fas fa-<?= strpos(strtolower($session['device']), 'mobile') !== false ? 'mobile-alt' : 'desktop' ?>"></i>
                                    <?= htmlspecialchars($session['device']) ?>
                                    <?= $session['is_current'] ? '<span class="status-badge status-enabled">Current</span>' : '' ?>
                                </td>
                                <td>
                                    <?= htmlspecialchars($session['ip_address']) ?>
                                </td>
                                <td>
                                    <?= date('M d, Y H:i', strtotime($session['last_activity'])) ?>
                                </td>
                                <td>
                                    <?php if (!$session['is_current']): ?>
                                        <button onclick="logoutSession('<?= htmlspecialchars($session['full_token']) ?>')"
                                            class="btn btn-danger btn-sm">
                                            <i class="fas fa-sign-out-alt"></i> Logout
                                        </button>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <?php if (count($activeSessions) > 1): ?>
                    <button onclick="logoutAllDevices()" class="btn btn-danger">
                        <i class="fas fa-power-off"></i> Logout All Other Devices
                    </button>
                <?php endif; ?>
            <?php else: ?>
                <p>No active sessions found.</p>
            <?php endif; ?>
        </div>

        <div class="back-link">
            <a href="dashboard.php"><i class="fas fa-arrow-left"></i> Back to Dashboard</a>
        </div>
    </div>

    <!-- Change Password Modal -->
    <div id="passwordModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closePasswordModal()">&times;</span>
            <div class="modal-header">
                <h3><i class="fas fa-key"></i> Change Password</h3>
                <p style="color: #666; font-size: 14px;">Enter your current password and choose a new one</p>
            </div>

            <form id="changePasswordForm">
                <div class="form-group">
                    <label for="current_password">Current Password</label>
                    <input type="password" id="current_password" name="current_password" required>
                </div>

                <div class="form-group">
                    <label for="new_password">New Password</label>
                    <input type="password" id="new_password" name="new_password" required>
                    <small style="color: #666;">Minimum 12 characters, must include uppercase, lowercase, numbers, and
                        special characters</small>
                </div>

                <div class="form-group">
                    <label for="confirm_password">Confirm New Password</label>
                    <input type="password" id="confirm_password" name="confirm_password" required>
                </div>

                <div id="passwordError" class="alert alert-error" style="display: none;"></div>
                <div id="passwordSuccess" class="alert alert-success" style="display: none;"></div>

                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Change Password
                </button>
                <button type="button" onclick="closePasswordModal()" class="btn btn-secondary">Cancel</button>
            </form>
        </div>
    </div>

    <script>
        function openPasswordModal() {
            document.getElementById('passwordModal').style.display = 'block';
        }

        function closePasswordModal() {
            document.getElementById('passwordModal').style.display = 'none';
            document.getElementById('changePasswordForm').reset();
            document.getElementById('passwordError').style.display = 'none';
            document.getElementById('passwordSuccess').style.display = 'none';
        }

        document.getElementById('changePasswordForm').addEventListener('submit', async function (e) {
            e.preventDefault();

            const formData = new FormData(this);
            formData.append('action', 'change-password');

            try {
                const response = await fetch('../backend/api/auth_api.php', {
                    method: 'POST',
                    body: formData
                });

                const result = await response.json();

                if (result.success) {
                    document.getElementById('passwordSuccess').textContent = result.message;
                    document.getElementById('passwordSuccess').style.display = 'block';
                    document.getElementById('passwordError').style.display = 'none';

                    setTimeout(() => {
                        window.location.href = 'security-settings.php?password_changed=1';
                    }, 1500);
                } else {
                    let errorMsg = result.message;
                    if (result.errors && result.errors.length > 0) {
                        errorMsg += ':\n' + result.errors.join('\n');
                    }
                    document.getElementById('passwordError').textContent = errorMsg;
                    document.getElementById('passwordError').style.display = 'block';
                    document.getElementById('passwordSuccess').style.display = 'none';
                }
            } catch (error) {
                document.getElementById('passwordError').textContent = 'An error occurred. Please try again.';
                document.getElementById('passwordError').style.display = 'block';
            }
        });

        async function logoutSession(token) {
            if (!confirm('Are you sure you want to logout this session?')) {
                return;
            }

            const formData = new FormData();
            formData.append('action', 'logout-session');
            formData.append('session_token', token);

            try {
                const response = await fetch('../backend/api/auth_api.php', {
                    method: 'POST',
                    body: formData
                });

                const result = await response.json();

                if (result.success) {
                    window.location.reload();
                } else {
                    alert('Failed to logout session: ' + result.message);
                }
            } catch (error) {
                alert('An error occurred. Please try again.');
            }
        }

        async function logoutAllDevices() {
            if (!confirm('Are you sure you want to logout all other devices? You will remain logged in on this device.')) {
                return;
            }

            const formData = new FormData();
            formData.append('action', 'logout-all-devices');
            formData.append('except_current', '1');

            try {
                const response = await fetch('../backend/api/auth_api.php', {
                    method: 'POST',
                    body: formData
                });

                const result = await response.json();

                if (result.success) {
                    alert('All other devices have been logged out.');
                    window.location.reload();
                } else {
                    alert('Failed to logout devices: ' + result.message);
                }
            } catch (error) {
                alert('An error occurred. Please try again.');
            }
        }

        // Close modal when clicking outside
        window.onclick = function (event) {
            const modal = document.getElementById('passwordModal');
            if (event.target == modal) {
                closePasswordModal();
            }
        }
    </script>
</body>

</html>