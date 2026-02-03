<aside class="sidebar" id="sidebar">
    <div class="sidebar-header">
        <img src="../images/sanstha-logo.png" alt="Logo" class="sidebar-logo">
        <span>ICOP Admin</span>
    </div>
    <nav class="sidebar-nav">
        <a href="dashboard.php" class="nav-item <?= basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : '' ?>">
            <i class="fas fa-chart-line"></i>
            <span>Dashboard</span>
        </a>
        <a href="banners.php" class="nav-item <?= basename($_SERVER['PHP_SELF']) == 'banners.php' ? 'active' : '' ?>">
            <i class="fas fa-images"></i>
            <span>Banners</span>
        </a>
        <a href="updates.php" class="nav-item <?= basename($_SERVER['PHP_SELF']) == 'updates.php' ? 'active' : '' ?>">
            <i class="fas fa-bullhorn"></i>
            <span>Updates</span>
        </a>
        <a href="gallery.php" class="nav-item <?= basename($_SERVER['PHP_SELF']) == 'gallery.php' ? 'active' : '' ?>">
            <i class="fas fa-photo-video"></i>
            <span>Gallery</span>
        </a>
        <a href="notices.php?type=news" class="nav-item <?= (basename($_SERVER['PHP_SELF']) == 'notices.php' && ($_GET['type'] ?? '') == 'news') ? 'active' : '' ?>">
            <i class="fas fa-newspaper"></i>
            <span>News</span>
        </a>
        <a href="notices.php?type=event" class="nav-item <?= (basename($_SERVER['PHP_SELF']) == 'notices.php' && ($_GET['type'] ?? '') == 'event') ? 'active' : '' ?>">
            <i class="fas fa-calendar-alt"></i>
            <span>Events</span>
        </a>
        <a href="notices.php" class="nav-item <?= (basename($_SERVER['PHP_SELF']) == 'notices.php' && !isset($_GET['type'])) ? 'active' : '' ?>">
            <i class="fas fa-bell"></i>
            <span>All Notices</span>
        </a>
        <a href="results.php" class="nav-item <?= basename($_SERVER['PHP_SELF']) == 'results.php' ? 'active' : '' ?>">
            <i class="fas fa-graduation-cap"></i>
            <span>Results</span>
        </a>
        <a href="syllabus.php" class="nav-item <?= basename($_SERVER['PHP_SELF']) == 'syllabus.php' ? 'active' : '' ?>">
            <i class="fas fa-book"></i>
            <span>Syllabus</span>
        </a>
        <a href="examinations.php" class="nav-item <?= basename($_SERVER['PHP_SELF']) == 'examinations.php' ? 'active' : '' ?>">
            <i class="fas fa-clock"></i>
            <span>Examinations</span>
        </a>
        <a href="applications.php" class="nav-item <?= basename($_SERVER['PHP_SELF']) == 'applications.php' ? 'active' : '' ?>">
            <i class="fas fa-file-alt"></i>
            <span>Applications</span>
        </a>
        <a href="enquiries.php" class="nav-item <?= basename($_SERVER['PHP_SELF']) == 'enquiries.php' ? 'active' : '' ?>">
            <i class="fas fa-envelope"></i>
            <span>Enquiries</span>
        </a>
        <a href="feedbacks.php" class="nav-item <?= basename($_SERVER['PHP_SELF']) == 'feedbacks.php' ? 'active' : '' ?>">
            <i class="fas fa-comment-dots"></i>
            <span>Feedback</span>
        </a>
        <div class="nav-divider"></div>
        <a href="settings.php" class="nav-item <?= basename($_SERVER['PHP_SELF']) == 'settings.php' ? 'active' : '' ?>">
            <i class="fas fa-cog"></i>
            <span>Settings</span>
        </a>
    </nav>
</aside>
