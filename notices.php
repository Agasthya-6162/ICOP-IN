<?php
require_once 'backend/config.php';

$category = isset($_GET['category']) ? sanitize($_GET['category']) : 'all';
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 20;
$offset = ($page - 1) * $limit;
$totalPages = 0; // Initialize variable

try {
    $db = Database::getInstance()->getConnection();
    $today = date('Y-m-d');
    
    // Build Query
    $sql = "SELECT * FROM notices WHERE is_active = 1 AND publish_date <= :today AND (expiry_date IS NULL OR expiry_date >= :today)";
    $params = [':today' => $today];
    
    if ($category !== 'all') {
        $sql .= " AND category = :category";
        $params[':category'] = $category;
    }
    
    // Count total for pagination
    $countSql = str_replace("SELECT *", "SELECT COUNT(*)", $sql);
    $countStmt = $db->prepare($countSql);
    $countStmt->execute($params);
    $totalNotices = $countStmt->fetchColumn();
    $totalPages = ceil($totalNotices / $limit);
    
    // Fetch Data
    $sql .= " ORDER BY is_important DESC, publish_date DESC LIMIT :limit OFFSET :offset";
    $stmt = $db->prepare($sql);
    foreach ($params as $key => $val) {
        $stmt->bindValue($key, $val);
    }
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    $notices = $stmt->fetchAll();
    
} catch (Exception $e) {
    $notices = [];
    $error = "Error fetching notices: " . $e->getMessage();
}

$pageTitle = "Notices | Indira College of Pharmacy";
include 'includes/header.php';
?>

<style>
    .notice-item {
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .notice-item:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }
    .pagination {
        display: flex;
        justify-content: center;
        margin-top: 30px;
        gap: 10px;
    }
    .pagination a {
        padding: 8px 12px;
        border: 1px solid #ddd;
        color: #003366;
        text-decoration: none;
        border-radius: 4px;
    }
    .pagination a.active {
        background: #003366;
        color: white;
        border-color: #003366;
    }
</style>

    <!-- Breadcrumb -->
    <div class="breadcrumb">
        <div class="container">
            <nav class="breadcrumb-nav" aria-label="Breadcrumb">
                <a href="index.php"><i class="fas fa-home"></i> Home</a>
                <span class="separator">/</span>
                <span class="current">Notices</span>
            </nav>
        </div>
    </div>

    <main id="main-content" style="padding: 60px 0; background: #f5f5f5; min-height: 60vh;">
        <div class="container">
            <div class="section-header">
                <h2>Notices & Circulars</h2>
                
                <!-- Category Filter -->
                <div class="filter-controls" style="margin-top: 20px;">
                    <a href="notices.php?category=all" class="filter-btn <?php echo $category == 'all' ? 'active' : ''; ?>">All</a>
                    <a href="notices.php?category=General" class="filter-btn <?php echo $category == 'General' ? 'active' : ''; ?>">General</a>
                    <a href="notices.php?category=Admission" class="filter-btn <?php echo $category == 'Admission' ? 'active' : ''; ?>">Admissions</a>
                    <a href="notices.php?category=Examination" class="filter-btn <?php echo $category == 'Examination' ? 'active' : ''; ?>">Examinations</a>
                    <a href="notices.php?category=Event" class="filter-btn <?php echo $category == 'Event' ? 'active' : ''; ?>">Events</a>
                </div>
            </div>

            <div class="notices-list" style="background: white; padding: 30px; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.05);">
                <?php if (count($notices) > 0): ?>
                    <?php foreach ($notices as $notice): ?>
                        <div class="notice-item" style="border-bottom: 1px solid #eee; padding: 20px 0; display: flex; align-items: flex-start;">
                            <div class="notice-date" style="background: #E8F4F8; padding: 10px; border-radius: 6px; text-align: center; min-width: 80px; margin-right: 20px;">
                                <span style="display: block; font-size: 1.5rem; font-weight: 700; color: #003366;"><?php echo date('d', strtotime($notice['publish_date'])); ?></span>
                                <span style="display: block; font-size: 0.9rem; color: #666;"><?php echo date('M Y', strtotime($notice['publish_date'])); ?></span>
                            </div>
                            <div class="notice-content" style="flex: 1;">
                                <h3 style="margin: 0 0 10px 0; font-size: 1.2rem; color: #333;">
                                    <?php echo htmlspecialchars($notice['title']); ?>
                                    <?php if ($notice['is_important']): ?>
                                        <span class="new-badge" style="font-size: 0.7rem; padding: 2px 6px; vertical-align: middle;">IMPORTANT</span>
                                    <?php endif; ?>
                                </h3>
                                <p style="color: #666; margin-bottom: 10px;"><?php echo nl2br(htmlspecialchars($notice['content'])); ?></p>
                                <div class="notice-meta" style="font-size: 0.9rem; color: #888;">
                                    <span style="margin-right: 15px;"><i class="fas fa-tag"></i> <?php echo htmlspecialchars($notice['category']); ?></span>
                                    <?php if ($notice['attachment_path']): ?>
                                        <a href="<?php echo htmlspecialchars(SITE_URL . '/' . $notice['attachment_path']); ?>" target="_blank" style="color: #0066CC; text-decoration: none;"><i class="fas fa-download"></i> Download Attachment</a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p style="text-align: center; color: #666; padding: 40px;">No notices found.</p>
                <?php endif; ?>

                <!-- Pagination -->
                <?php if ($totalPages > 1): ?>
                    <div class="pagination">
                        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                            <a href="?page=<?php echo $i; ?>&category=<?php echo $category; ?>" class="<?php echo $i == $page ? 'active' : ''; ?>"><?php echo $i; ?></a>
                        <?php endfor; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </main>

<?php include 'includes/footer.php'; ?>