<?php
require_once 'config.php';

// Initialize session manager
$sessionManager = new SessionManager();
$securityManager = new SecurityManager();

// Log logout activity
if (isset($_SESSION['admin_user_id'])) {
    $securityManager->logSecurityEvent($_SESSION['admin_user_id'], 'logout', 'User logged out', 'success');
}

// Destroy session properly
$sessionManager->destroySession();

// Redirect to login page
header('Location: index.php?logged_out=1');
exit;
?>