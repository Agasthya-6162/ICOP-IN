<?php
require_once 'config.php';
requireAdminLogin();

$db = getDB();
$message = '';
$error = '';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'add') {
        $content = sanitize($_POST['content']);
        $link_url = sanitize($_POST['link_url']);
        $display_order = (int)$_POST['display_order'];
        
        if (!empty($content)) {
            // Check for duplicates
            $checkStmt = $db->prepare("SELECT COUNT(*) FROM latest_updates WHERE content = ?");
            $checkStmt->execute([$content]);
            if ($checkStmt->fetchColumn() > 0) {
                $error = 'This update already exists.';
            } else {
                try {
                    $stmt = $db->prepare('INSERT INTO latest_updates (content, link_url, display_order, is_active) VALUES (?, ?, ?, 1)');
                    $stmt->execute([$content, $link_url, $display_order]);
                    
                    header('Location: updates.php?msg=added');
                    exit;
                } catch (PDOException $e) {
                    $error = 'Database Error: ' . $e->getMessage();
                }
            }
        } else {
            $error = 'Content is required.';
        }
    }
}

// Handle Delete/Toggle
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    try {
        $stmt = $db->prepare('DELETE FROM latest_updates WHERE id = ?');
        $stmt->execute([$id]);
        
        header('Location: updates.php?msg=deleted');
        exit;
    } catch (PDOException $e) {
        $error = 'Error: ' . $e->getMessage();
    }
}

if (isset($_GET['toggle'])) {
    $id = (int)$_GET['toggle'];
    try {
        $stmt = $db->prepare('UPDATE latest_updates SET is_active = NOT is_active WHERE id = ?');
        $stmt->execute([$id]);
        
        header('Location: updates.php?msg=toggled');
        exit;
    } catch (PDOException $e) {
        $error = 'Error: ' . $e->getMessage();
    }
}

// Display messages
if (isset($_GET['msg'])) {
    if ($_GET['msg'] === 'added') $message = 'Update added successfully!';
    if ($_GET['msg'] === 'deleted') $message = 'Update deleted successfully!';
    if ($_GET['msg'] === 'toggled') $message = 'Status updated!';
}

// Get all updates
$updates = [];
try {
    $stmt = $db->query('SELECT * FROM latest_updates ORDER BY display_order ASC, created_at DESC');
    $updates = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = 'Error fetching updates: ' . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Updates - ICOP Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="assets/admin.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>
    
    <div class="dashboard-container">
        <?php include 'includes/sidebar.php'; ?>
        
        <main class="main-content">
            <div class="page-header">
                <h1><i class="fas fa-bullhorn"></i> Manage Latest Updates</h1>
                <p>Manage the scrolling ticker text on the homepage</p>
            </div>
            
            <?php if ($message): ?>
                <div class="alert alert-success"><i class="fas fa-check-circle"></i> <?= $message ?></div>
            <?php endif; ?>
            
            <?php if ($error): ?>
                <div class="alert alert-error"><i class="fas fa-exclamation-circle"></i> <?= $error ?></div>
            <?php endif; ?>
            
            <!-- Add Update Form -->
            <div class="content-card">
                <div class="card-header">
                    <h3><i class="fas fa-plus-circle"></i> Add New Update</h3>
                </div>
                <div class="card-body">
                    <form method="POST" class="admin-form">
                        <input type="hidden" name="action" value="add">
                        <div class="form-group">
                            <label for="content">Update Text *</label>
                            <input type="text" id="content" name="content" required class="form-control" placeholder="e.g., Admissions Open for 2026-27">
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="link_url">Link URL (Optional)</label>
                                <input type="text" id="link_url" name="link_url" placeholder="e.g., admissions.php" class="form-control">
                            </div>
                            <div class="form-group">
                                <label for="display_order">Display Order</label>
                                <input type="number" id="display_order" name="display_order" value="0" class="form-control">
                            </div>
                        </div>
                        <button type="submit" class="btn-primary">
                            <i class="fas fa-save"></i> Save Update
                        </button>
                    </form>
                </div>
            </div>
            
            <!-- Updates List -->
            <div class="content-card">
                <div class="card-header">
                    <h3><i class="fas fa-list"></i> Current Updates</h3>
                </div>
                <div class="table-responsive">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Order</th>
                                <th>Content</th>
                                <th>Link</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($updates)): ?>
                                <tr>
                                    <td colspan="5" class="text-center">No updates found.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($updates as $update): ?>
                                    <tr>
                                        <td><?= $update['display_order'] ?></td>
                                        <td>
                                            <strong><?= htmlspecialchars($update['content']) ?></strong>
                                        </td>
                                        <td><?= htmlspecialchars($update['link_url'] ?? '') ?></td>
                                        <td>
                                            <a href="?toggle=<?= $update['id'] ?>" class="status-badge <?= $update['is_active'] ? 'status-active' : 'status-inactive' ?>">
                                                <?= $update['is_active'] ? 'Active' : 'Inactive' ?>
                                            </a>
                                        </td>
                                        <td>
                                            <a href="?delete=<?= $update['id'] ?>" class="btn-icon btn-delete" onclick="return confirm('Are you sure you want to delete this update?');">
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
</body>
</html>