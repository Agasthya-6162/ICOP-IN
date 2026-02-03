<?php
require_once 'backend/config.php';

// Filter parameters
$category = isset($_GET['category']) ? $_GET['category'] : '';
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 12; // 12 items per page for a 3-column or 4-column grid
$offset = ($page - 1) * $limit;

try {
    $db = Database::getInstance()->getConnection();
    
    // Base query
    $sql = "SELECT * FROM gallery WHERE is_active = 1";
    $params = [];
    
    // Apply filters
    if (!empty($category)) {
        $sql .= " AND category = :category";
        $params[':category'] = $category;
    }
    
    // Count total for pagination
    $countSql = str_replace("SELECT *", "SELECT COUNT(*)", $sql);
    $countStmt = $db->prepare($countSql);
    $countStmt->execute($params);
    $totalItems = $countStmt->fetchColumn();
    $totalPages = ceil($totalItems / $limit);
    
    // Fetch Data
    $sql .= " ORDER BY display_order ASC, created_at DESC LIMIT :limit OFFSET :offset";
    $stmt = $db->prepare($sql);
    
    foreach ($params as $key => $val) {
        $stmt->bindValue($key, $val);
    }
    
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    $galleryItems = $stmt->fetchAll();
    
    // Get unique categories for filter dropdown
    $catStmt = $db->query("SELECT DISTINCT category FROM gallery WHERE is_active = 1 ORDER BY category");
    $categories = $catStmt->fetchAll(PDO::FETCH_COLUMN);
    
} catch (Exception $e) {
    error_log("Error fetching gallery: " . $e->getMessage());
    $galleryItems = [];
    $categories = [];
    $totalPages = 0;
}

$pageTitle = "Gallery | Indira College of Pharmacy";
include 'includes/header.php';
?>

<style>
    .filter-section {
        background: #fff;
        padding: 20px;
        border-radius: 8px;
        margin-bottom: 30px;
        text-align: center;
    }
    
    .filter-btn {
        background: #f1f1f1;
        border: none;
        padding: 8px 16px;
        margin: 5px;
        border-radius: 20px;
        cursor: pointer;
        transition: all 0.3s;
        color: #333;
        text-decoration: none;
        display: inline-block;
    }
    
    .filter-btn:hover, .filter-btn.active {
        background: var(--primary-blue);
        color: white;
    }

    .gallery-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        gap: 20px;
    }

    .gallery-item {
        position: relative;
        overflow: hidden;
        border-radius: 8px;
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        background: #fff;
        transition: transform 0.3s ease;
    }

    .gallery-item:hover {
        transform: translateY(-5px);
    }

    .gallery-item img {
        width: 100%;
        height: 250px;
        object-fit: cover;
        display: block;
        transition: transform 0.5s ease;
    }
    
    .gallery-item:hover img {
        transform: scale(1.05);
    }

    .gallery-caption {
        padding: 15px;
        background: white;
    }
    
    .gallery-caption h4 {
        margin: 0 0 5px;
        font-size: 1.1rem;
        color: #333;
    }
    
    .gallery-caption p {
        margin: 0;
        font-size: 0.9rem;
        color: #666;
    }

    .pagination {
        display: flex;
        justify-content: center;
        gap: 10px;
        margin-top: 40px;
    }

    .page-link {
        padding: 8px 16px;
        border: 1px solid #ddd;
        border-radius: 4px;
        color: var(--primary-blue);
        text-decoration: none;
        transition: all 0.3s;
    }

    .page-link.active {
        background: var(--primary-blue);
        color: white;
        border-color: var(--primary-blue);
    }

    .page-link:hover:not(.active) {
        background: #f1f1f1;
    }
</style>

    <main id="main-content" style="padding: 60px 0; background: #f5f5f5;">
        <div class="container">
            <h1 style="color: #003366; font-size: 2.5rem; margin-bottom: 30px; text-align: center;">Photo Gallery</h1>
            
            <div class="filter-section">
                <a href="gallery.php" class="filter-btn <?php echo empty($category) ? 'active' : ''; ?>">All</a>
                <?php foreach ($categories as $cat): ?>
                    <a href="gallery.php?category=<?php echo urlencode($cat); ?>" 
                       class="filter-btn <?php echo $category === $cat ? 'active' : ''; ?>">
                        <?php echo htmlspecialchars($cat); ?>
                    </a>
                <?php endforeach; ?>
            </div>

            <div class="gallery-grid">
                <?php if (empty($galleryItems)): ?>
                    <div style="grid-column: 1 / -1; text-align: center; padding: 40px; background: white; border-radius: 8px;">
                        <p>No photos found.</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($galleryItems as $item): ?>
                        <div class="gallery-item">
                            <img src="<?php echo htmlspecialchars(SITE_URL . '/' . $item['image_path']); ?>" 
                                 alt="<?php echo htmlspecialchars($item['title']); ?>" loading="lazy">
                            <div class="gallery-caption">
                                <h4><?php echo htmlspecialchars($item['title']); ?></h4>
                                <?php if (!empty($item['description'])): ?>
                                    <p><?php echo htmlspecialchars($item['description']); ?></p>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <!-- Pagination -->
            <?php if ($totalPages > 1): ?>
            <div class="pagination">
                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <a href="?page=<?php echo $i; ?><?php echo $category ? '&category=' . urlencode($category) : ''; ?>" 
                       class="page-link <?php echo $page === $i ? 'active' : ''; ?>">
                        <?php echo $i; ?>
                    </a>
                <?php endfor; ?>
            </div>
            <?php endif; ?>

        </div>
    </main>

<?php include 'includes/footer.php'; ?>