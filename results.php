<?php
require_once 'backend/config.php';

// Filter parameters
$program = isset($_GET['program']) ? $_GET['program'] : '';
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 10;
$offset = ($page - 1) * $limit;
$totalPages = 0; // Initialize variable

try {
    $db = Database::getInstance()->getConnection();
    
    // Base query
    $sql = "SELECT * FROM results WHERE is_active = 1";
    $params = [];
    
    // Apply filters
    if (!empty($program)) {
        $sql .= " AND program = :program";
        $params[':program'] = $program;
    }
    
    // Count total for pagination
    $countSql = str_replace("SELECT *", "SELECT COUNT(*)", $sql);
    $countStmt = $db->prepare($countSql);
    $countStmt->execute($params);
    $totalResults = $countStmt->fetchColumn();
    $totalPages = ceil($totalResults / $limit);
    
    // Fetch Data
    $sql .= " ORDER BY publish_date DESC LIMIT :limit OFFSET :offset";
    $stmt = $db->prepare($sql);
    
    foreach ($params as $key => $val) {
        $stmt->bindValue($key, $val);
    }
    
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    $results = $stmt->fetchAll();
    
    // Get unique programs for filter dropdown
    $progStmt = $db->query("SELECT DISTINCT program FROM results WHERE is_active = 1 ORDER BY program");
    $programs = $progStmt->fetchAll(PDO::FETCH_COLUMN);
    
} catch (Exception $e) {
    error_log("Error fetching results: " . $e->getMessage());
    $results = [];
    $programs = [];
    $totalPages = 0;
}

$pageTitle = "Results - Indira College of Pharmacy";
include 'includes/header.php';
?>

<style>
    .filter-section {
        background: #f8f9fa;
        padding: 20px;
        border-radius: 8px;
        margin-bottom: 30px;
        border: 1px solid #e9ecef;
    }
    
    .filter-form {
        display: flex;
        gap: 15px;
        flex-wrap: wrap;
        align-items: center;
    }
    
    .form-select {
        padding: 10px;
        border: 1px solid #ddd;
        border-radius: 4px;
        min-width: 200px;
    }
    
    .btn-filter {
        background: var(--primary-blue);
        color: white;
        border: none;
        padding: 10px 20px;
        border-radius: 4px;
        cursor: pointer;
        transition: background 0.3s;
    }
    
    .btn-filter:hover {
        background: #004494;
    }

    .pagination {
        display: flex;
        justify-content: center;
        gap: 10px;
        margin-top: 30px;
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

    <main id="main-content" style="padding: 60px 0;">
        <div class="container">
            <h1 style="color: var(--primary-blue); margin-bottom: 30px;">Examination Results</h1>
            
            <div class="filter-section">
                <form class="filter-form" method="GET" action="results.php">
                    <select name="program" class="form-select">
                        <option value="">All Programs</option>
                        <?php foreach ($programs as $prog): ?>
                            <option value="<?php echo htmlspecialchars($prog); ?>" <?php echo $program === $prog ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($prog); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <button type="submit" class="btn-filter"><i class="fas fa-filter"></i> Filter</button>
                    <?php if (!empty($program)): ?>
                        <a href="results.php" class="btn-filter" style="background: #6c757d; text-decoration: none;">Clear</a>
                    <?php endif; ?>
                </form>
            </div>

            <div class="content-box"
                style="background: #fff; padding: 40px; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.1);">
                <p>Check the latest academic results for B.Pharm, D.Pharm and M.Pharm programs.</p>
                
                <div class="results-list" style="margin-top: 30px;">
                    <?php if (empty($results)): ?>
                        <p style="text-align: center; color: #666; padding: 20px;">No results found.</p>
                    <?php else: ?>
                        <?php foreach ($results as $result): ?>
                            <div style="background: #E8F4F8; padding: 20px; border-radius: 6px; margin-bottom: 20px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px;">
                                <div>
                                    <h4 style="margin-bottom: 5px;"><?php echo htmlspecialchars($result['title']); ?></h4>
                                    <div style="font-size: 0.9rem; color: #666;">
                                        <span style="margin-right: 15px;"><i class="fas fa-graduation-cap"></i> <?php echo htmlspecialchars($result['program']); ?></span>
                                        <span style="margin-right: 15px;"><i class="far fa-calendar-alt"></i> Published: <?php echo date('d M Y', strtotime($result['publish_date'])); ?></span>
                                    </div>
                                </div>
                                <a href="<?php echo htmlspecialchars($result['result_file']); ?>" class="cta-button" style="padding: 10px 25px;" target="_blank" download>
                                    <i class="fas fa-download"></i> Download Result
                                </a>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>

                <!-- Pagination -->
                <?php if ($totalPages > 1): ?>
                <div class="pagination">
                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <a href="?page=<?php echo $i; ?><?php echo $program ? '&program=' . urlencode($program) : ''; ?>" 
                           class="page-link <?php echo $page === $i ? 'active' : ''; ?>">
                            <?php echo $i; ?>
                        </a>
                    <?php endfor; ?>
                </div>
                <?php endif; ?>

            </div>
        </div>
    </main>

<?php include 'includes/footer.php'; ?>