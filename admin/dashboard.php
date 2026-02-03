<?php
require_once 'config.php';
requireAdminLogin();

// Get statistics using PDO
try {
    $db = getDB();
    
    // Get banner count
    $stmt = $db->query("SELECT COUNT(*) as count FROM banners WHERE is_active = 1");
    $bannerCount = $stmt->fetch()['count'];
    
    // Get gallery count
    $stmt = $db->query("SELECT COUNT(*) as count FROM gallery WHERE is_active = 1");
    $galleryCount = $stmt->fetch()['count'];
    
    // Get notices count
    $stmt = $db->query("SELECT COUNT(*) as count FROM notices");
    $noticesCount = $stmt->fetch()['count'];
    
    // Get results count
    $stmt = $db->query("SELECT COUNT(*) as count FROM results");
    $resultsCount = $stmt->fetch()['count'];

    // Get pending applications count
    $stmt = $db->query("SELECT COUNT(*) as count FROM admission_applications WHERE status = 'Pending'");
    $appCount = $stmt->fetch()['count'];

    // Get new enquiries count
    $stmt = $db->query("SELECT COUNT(*) as count FROM contact_submissions WHERE status != 'Replied'");
    $enquiryCount = $stmt->fetch()['count'];

    // Get syllabus count
    $stmt = $db->query("SELECT COUNT(*) as count FROM syllabus WHERE is_active = 1");
    $syllabusCount = $stmt->fetch()['count'];

    // Get examinations count
    $stmt = $db->query("SELECT COUNT(*) as count FROM examinations WHERE is_active = 1");
    $examCount = $stmt->fetch()['count'];
    
    $stats = [
        'banners' => $bannerCount,
        'gallery' => $galleryCount,
        'notices' => $noticesCount,
        'results' => $resultsCount,
        'applications' => $appCount,
        'enquiries' => $enquiryCount,
        'syllabus' => $syllabusCount,
        'examinations' => $examCount
    ];
} catch (PDOException $e) {
    $stats = [
        'banners' => 0,
        'gallery' => 0,
        'notices' => 0,
        'results' => 0,
        'applications' => 0,
        'enquiries' => 0,
        'syllabus' => 0,
        'examinations' => 0
    ];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - ICOP</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="assets/admin.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>
    
    <div class="dashboard-container">
        <?php include 'includes/sidebar.php'; ?>
        
        <main class="main-content">
            <div class="page-header">
                <h1><i class="fas fa-chart-line"></i> Dashboard Overview</h1>
                <p>Welcome back, <strong><?= htmlspecialchars($_SESSION['admin_full_name'] ?? $_SESSION['admin_username']) ?></strong>! 
                <?php
                // Get last login time
                try {
                    $stmt = $db->prepare("SELECT last_login FROM admin_users WHERE id = ?");
                    $stmt->execute([$_SESSION['admin_user_id']]);
                    $lastLogin = $stmt->fetch()['last_login'];
                    if ($lastLogin && $lastLogin != '0000-00-00 00:00:00') {
                        $loginTime = new DateTime($lastLogin);
                        $now = new DateTime();
                        $interval = $now->diff($loginTime);
                        if ($interval->days > 0) {
                            echo "Last login: " . $interval->days . " day(s) ago";
                        } elseif ($interval->h > 0) {
                            echo "Last login: " . $interval->h . " hour(s) ago";
                        } else {
                            echo "Last login: " . $interval->i . " minute(s) ago";
                        }
                    }
                } catch (Exception $e) {
                    // Silently fail
                }
                ?>
                </p>
            </div>
            
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon" style="background: #667eea;">
                        <i class="fas fa-images"></i>
                    </div>
                    <div class="stat-info">
                        <h3><?= $stats['banners'] ?></h3>
                        <p>Active Banners</p>
                    </div>
                    <a href="banners.php" class="stat-link">Manage <i class="fas fa-arrow-right"></i></a>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon" style="background: #f093fb;">
                        <i class="fas fa-photo-video"></i>
                    </div>
                    <div class="stat-info">
                        <h3><?= $stats['gallery'] ?></h3>
                        <p>Gallery Images</p>
                    </div>
                    <a href="gallery.php" class="stat-link">Manage <i class="fas fa-arrow-right"></i></a>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon" style="background: #4facfe;">
                        <i class="fas fa-bell"></i>
                    </div>
                    <div class="stat-info">
                        <h3><?= $stats['notices'] ?></h3>
                        <p>Active Notices</p>
                    </div>
                    <a href="notices.php" class="stat-link">Manage <i class="fas fa-arrow-right"></i></a>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon" style="background: #43e97b;">
                        <i class="fas fa-graduation-cap"></i>
                    </div>
                    <div class="stat-info">
                        <h3><?= $stats['results'] ?></h3>
                        <p>Published Results</p>
                    </div>
                    <a href="results.php" class="stat-link">Manage <i class="fas fa-arrow-right"></i></a>
                </div>

                <div class="stat-card">
                    <div class="stat-icon" style="background: #ff9a9e;">
                        <i class="fas fa-file-alt"></i>
                    </div>
                    <div class="stat-info">
                        <h3><?= $stats['applications'] ?></h3>
                        <p>Pending Applications</p>
                    </div>
                    <a href="applications.php" class="stat-link">Manage <i class="fas fa-arrow-right"></i></a>
                </div>

                <div class="stat-card">
                    <div class="stat-icon" style="background: #a18cd1;">
                        <i class="fas fa-envelope"></i>
                    </div>
                    <div class="stat-info">
                        <h3><?= $stats['enquiries'] ?></h3>
                        <p>New Enquiries</p>
                    </div>
                    <a href="enquiries.php" class="stat-link">Manage <i class="fas fa-arrow-right"></i></a>
                </div>

                <div class="stat-card">
                    <div class="stat-icon" style="background: #fad0c4;">
                        <i class="fas fa-book"></i>
                    </div>
                    <div class="stat-info">
                        <h3><?= $stats['syllabus'] ?></h3>
                        <p>Syllabus Files</p>
                    </div>
                    <a href="syllabus.php" class="stat-link">Manage <i class="fas fa-arrow-right"></i></a>
                </div>

                <div class="stat-card">
                    <div class="stat-icon" style="background: #84fab0;">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div class="stat-info">
                        <h3><?= $stats['examinations'] ?></h3>
                        <p>Active Exams</p>
                    </div>
                    <a href="examinations.php" class="stat-link">Manage <i class="fas fa-arrow-right"></i></a>
                </div>
            </div>
            
            <div class="quick-actions">
                <h2><i class="fas fa-bolt"></i> Quick Actions</h2>
                <div class="actions-grid">
                    <a href="banners.php?action=add" class="action-btn">
                        <i class="fas fa-plus-circle"></i>
                        <span>Add New Banner</span>
                    </a>
                    <a href="gallery.php?action=add" class="action-btn">
                        <i class="fas fa-plus-circle"></i>
                        <span>Add Gallery Image</span>
                    </a>
                    <a href="notices.php?action=add" class="action-btn">
                        <i class="fas fa-plus-circle"></i>
                        <span>Post New Notice</span>
                    </a>
                    <a href="results.php?action=add" class="action-btn">
                        <i class="fas fa-plus-circle"></i>
                        <span>Publish Result</span>
                    </a>
                </div>
            </div>
            
            <div class="recent-activity">
                <h2><i class="fas fa-clock"></i> Recent Activity</h2>
                <p style="text-align: center; padding: 40px; color: #999;">
                    <i class="fas fa-info-circle"></i> Activity tracking coming soon...
                </p>
            </div>
        </main>
    </div>
    
    <script src="assets/admin.js"></script>
</body>
</html>
