<?php
require_once 'config.php';
requireAdminLogin();

// Handle Delete
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    try {
        $db = getDB();
        $stmt = $db->prepare("DELETE FROM contact_submissions WHERE id = ?");
        $stmt->execute([$id]);
        header('Location: enquiries.php?msg=deleted');
        exit;
    } catch (PDOException $e) {
        $error = "Error deleting enquiry: " . $e->getMessage();
    }
}

// Handle Status Update
if (isset($_GET['mark_replied'])) {
    $id = (int)$_GET['mark_replied'];
    try {
        $db = getDB();
        $stmt = $db->prepare("UPDATE contact_submissions SET status = 'Replied' WHERE id = ?");
        $stmt->execute([$id]);
        header('Location: enquiries.php?msg=updated');
        exit;
    } catch (PDOException $e) {
        $error = "Error updating status: " . $e->getMessage();
    }
}

// Get enquiries
$enquiries = [];
try {
    $db = getDB();
    $stmt = $db->query("SELECT * FROM contact_submissions ORDER BY created_at DESC");
    $enquiries = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = "Error fetching enquiries: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Enquiries - ICOP Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="assets/admin.css">
    <style>
        .status-badge { padding: 5px 10px; border-radius: 15px; font-size: 0.85rem; }
        .status-new { background: #d1ecf1; color: #0c5460; }
        .status-read { background: #e2e3e5; color: #383d41; }
        .status-replied { background: #d4edda; color: #155724; }
    </style>
</head>
<body>
    <?php include 'includes/header.php'; ?>
    
    <div class="dashboard-container">
        <?php include 'includes/sidebar.php'; ?>
        
        <main class="main-content">
            <div class="page-header">
                <h1><i class="fas fa-envelope"></i> Contact Enquiries</h1>
            </div>
            
            <?php if (isset($_GET['msg'])): ?>
                <div class="alert alert-success">
                    <?php 
                    if ($_GET['msg'] == 'deleted') echo "Enquiry deleted successfully!";
                    elseif ($_GET['msg'] == 'updated') echo "Status updated successfully!";
                    ?>
                </div>
            <?php endif; ?>

            <?php if (isset($error)): ?>
                <div class="alert alert-error"><?= $error ?></div>
            <?php endif; ?>
            
            <div class="content-card">
                <div class="table-responsive">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Type</th>
                                <th>Message</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($enquiries)): ?>
                                <tr>
                                    <td colspan="7" style="text-align: center; padding: 20px;">No enquiries found</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($enquiries as $enquiry): ?>
                                    <tr>
                                        <td><?= date('d M Y', strtotime($enquiry['created_at'])) ?></td>
                                        <td><?= htmlspecialchars($enquiry['name']) ?></td>
                                        <td><a href="mailto:<?= htmlspecialchars($enquiry['email']) ?>"><?= htmlspecialchars($enquiry['email']) ?></a></td>
                                        <td><?= htmlspecialchars($enquiry['inquiry_type']) ?></td>
                                        <td title="<?= htmlspecialchars($enquiry['message']) ?>">
                                            <?= htmlspecialchars(substr($enquiry['message'], 0, 50)) . (strlen($enquiry['message']) > 50 ? '...' : '') ?>
                                        </td>
                                        <td>
                                            <span class="status-badge status-<?= strtolower($enquiry['status']) ?>">
                                                <?= htmlspecialchars($enquiry['status']) ?>
                                            </span>
                                        </td>
                                        <td class="actions">
                                            <?php if ($enquiry['status'] !== 'Replied'): ?>
                                                <a href="enquiries.php?mark_replied=<?= $enquiry['id'] ?>" class="btn-icon" title="Mark as Replied">
                                                    <i class="fas fa-check"></i>
                                                </a>
                                            <?php endif; ?>
                                            <a href="enquiries.php?delete=<?= $enquiry['id'] ?>" class="btn-icon delete-btn" title="Delete" onclick="return confirm('Are you sure?')">
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
