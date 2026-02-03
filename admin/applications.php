<?php
require_once 'config.php';
requireAdminLogin();

// Handle Delete
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    try {
        $db = getDB();
        
        // Get file paths
        $stmt = $db->prepare("SELECT photo_path, signature_path, aadhaar_doc_path, ssc_doc_path, hsc_doc_path, lc_doc_path FROM admission_applications WHERE id = ?");
        $stmt->execute([$id]);
        $files = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($files) {
            // Delete physical files
            foreach ($files as $path) {
                if ($path) {
                    deleteFile(dirname(__DIR__) . '/' . $path);
                }
            }
            
            // Delete record
            $stmt = $db->prepare("DELETE FROM admission_applications WHERE id = ?");
            $stmt->execute([$id]);
            
            header('Location: applications.php?msg=deleted');
            exit;
        }
    } catch (PDOException $e) {
        $error = "Error deleting application: " . $e->getMessage();
    }
}

// Get applications
$applications = [];
try {
    $db = getDB();
    $stmt = $db->query("SELECT * FROM admission_applications ORDER BY created_at DESC");
    $applications = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = "Error fetching applications: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Applications - ICOP Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="assets/admin.css">
    <style>
        .status-badge {
            padding: 5px 10px;
            border-radius: 15px;
            font-size: 0.85rem;
            font-weight: 500;
        }
        .status-pending { background: #ffeeba; color: #856404; }
        .status-approved { background: #d4edda; color: #155724; }
        .status-rejected { background: #f8d7da; color: #721c24; }
    </style>
</head>
<body>
    <?php include 'includes/header.php'; ?>
    
    <div class="dashboard-container">
        <?php include 'includes/sidebar.php'; ?>
        
        <main class="main-content">
            <div class="page-header">
                <h1><i class="fas fa-file-alt"></i> Admission Applications</h1>
                <div class="header-actions">
                    <button class="btn-primary" onclick="window.print()"><i class="fas fa-print"></i> Print List</button>
                </div>
            </div>
            
            <?php if (isset($_GET['msg']) && $_GET['msg'] == 'deleted'): ?>
                <div class="alert alert-success">Application deleted successfully!</div>
            <?php endif; ?>

            <?php if (isset($error)): ?>
                <div class="alert alert-error"><?= $error ?></div>
            <?php endif; ?>
            
            <div class="content-card">
                <div class="table-responsive">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>App No.</th>
                                <th>Date</th>
                                <th>Student Name</th>
                                <th>Course</th>
                                <th>Mobile</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($applications)): ?>
                                <tr>
                                    <td colspan="7" style="text-align: center; padding: 20px;">No applications found</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($applications as $app): ?>
                                    <tr>
                                        <td><strong><?= htmlspecialchars($app['application_no']) ?></strong></td>
                                        <td><?= date('d M Y', strtotime($app['created_at'])) ?></td>
                                        <td><?= htmlspecialchars($app['full_name']) ?></td>
                                        <td><?= htmlspecialchars($app['course']) ?></td>
                                        <td><?= htmlspecialchars($app['mobile']) ?></td>
                                        <td>
                                            <?php
                                            $statusClass = 'status-pending';
                                            if ($app['status'] == 'Approved') $statusClass = 'status-approved';
                                            if ($app['status'] == 'Rejected') $statusClass = 'status-rejected';
                                            ?>
                                            <span class="status-badge <?= $statusClass ?>">
                                                <?= htmlspecialchars($app['status']) ?>
                                            </span>
                                        </td>
                                        <td class="actions">
                                            <a href="view_application.php?id=<?= $app['id'] ?>" class="btn-icon" title="View Details">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="applications.php?delete=<?= $app['id'] ?>" class="btn-icon delete-btn" title="Delete" onclick="return confirm('Are you sure you want to delete this application? This action cannot be undone.')">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>
    
    <script src="assets/admin.js"></script>
</body>
</html>
