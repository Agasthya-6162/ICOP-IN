<header class="admin-header">
    <div class="header-left">
        <button class="menu-toggle" id="menuToggle">
            <i class="fas fa-bars"></i>
        </button>
        <h2>Indira College of Pharmacy</h2>
    </div>
    <div class="header-right">
        <a href="../index.php" class="btn-view-site" target="_blank">
            <i class="fas fa-external-link-alt"></i> View Website
        </a>
        <div class="user-menu">
            <span><i class="fas fa-user-circle"></i> <?= htmlspecialchars($_SESSION['admin_username']) ?></span>
            <a href="logout.php" class="btn-logout">
                <i class="fas fa-sign-out-alt"></i> Logout
            </a>
        </div>
    </div>
</header>
