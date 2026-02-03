<?php
require_once 'config.php';
requireAdminLogin();

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_password'])) {
    $current_password = $_POST['current_password'] ?? '';
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
        $error = 'All fields are required.';
    } elseif ($new_password !== $confirm_password) {
        $error = 'New passwords do not match.';
    } elseif (strlen($new_password) < 6) {
        $error = 'New password must be at least 6 characters long.';
    } else {
        try {
            $db = getDB();
            $stmt = $db->prepare("SELECT password FROM admin_users WHERE id = ?");
            $stmt->execute([$_SESSION['admin_user_id']]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user && password_verify($current_password, $user['password'])) {
                $new_hash = password_hash($new_password, PASSWORD_DEFAULT);
                $updateStmt = $db->prepare("UPDATE admin_users SET password = ? WHERE id = ?");
                
                if ($updateStmt->execute([$new_hash, $_SESSION['admin_user_id']])) {
                    $message = 'Password updated successfully.';
                } else {
                    $error = 'Failed to update password.';
                }
            } else {
                $error = 'Incorrect current password.';
            }
        } catch (PDOException $e) {
            $error = 'Database error: ' . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings - ICOP Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="assets/admin.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>
    
    <div class="dashboard-container">
        <?php include 'includes/sidebar.php'; ?>
        
        <main class="main-content">
            <div class="page-header">
                <h1><i class="fas fa-cog"></i> Settings</h1>
                <p>Manage your account and system settings</p>
            </div>
            
            <?php if ($message): ?>
                <div class="alert alert-success"><i class="fas fa-check-circle"></i> <?= $message ?></div>
            <?php endif; ?>
            
            <?php if ($error): ?>
                <div class="alert alert-error"><i class="fas fa-exclamation-circle"></i> <?= $error ?></div>
            <?php endif; ?>
            
            <div class="content-card">
                <div class="card-header">
                    <h3><i class="fas fa-lock"></i> Change Password</h3>
                </div>
                <div class="card-body">
                    <form method="POST" action="" class="admin-form" style="max-width: 500px;">
                        <div class="form-group">
                            <label for="current_password">Current Password</label>
                            <input type="password" id="current_password" name="current_password" class="form-control" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="new_password">New Password</label>
                            <input type="password" id="new_password" name="new_password" class="form-control" required minlength="6">
                            <small class="text-muted">Minimum 6 characters</small>
                        </div>
                        
                        <div class="form-group">
                            <label for="confirm_password">Confirm New Password</label>
                            <input type="password" id="confirm_password" name="confirm_password" class="form-control" required minlength="6">
                        </div>
                        
                        <div class="form-actions">
                            <button type="submit" name="change_password" class="btn-primary">
                                <i class="fas fa-save"></i> Update Password
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="content-card" style="margin-top: 30px;">
                <div class="card-header">
                    <h3><i class="fas fa-server"></i> System Information</h3>
                </div>
                <div class="card-body">
                    <div class="detail-grid">
                        <div class="detail-item">
                            <label>PHP Version</label>
                            <div><?= phpversion() ?></div>
                        </div>
                        <div class="detail-item">
                            <label>Server Software</label>
                            <div><?= $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown' ?></div>
                        </div>
                        <div class="detail-item">
                            <label>Database</label>
                            <div>MySQL</div>
                        </div>
                        <div class="detail-item">
                            <label>Admin User</label>
                            <div><?= htmlspecialchars($_SESSION['admin_username'] ?? 'Unknown') ?> (ID: <?= htmlspecialchars($_SESSION['admin_user_id'] ?? '-') ?>)</div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
    
    <script src="assets/admin.js"></script>
</body>
</html>
