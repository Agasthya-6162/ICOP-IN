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
        $title = sanitize($_POST['title']);
        $description = sanitize($_POST['description']);
        $link_url = sanitize($_POST['link_url']);
        $display_order = (int)$_POST['display_order'];
        
        // Check for duplicates
        $checkStmt = $db->prepare("SELECT COUNT(*) FROM banners WHERE title = ? AND display_order = ?");
        $checkStmt->execute([$title, $display_order]);
        if ($checkStmt->fetchColumn() > 0) {
            $error = 'A banner with this title and order already exists.';
        } else {
            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $upload = handleAdminFileUpload($_FILES['image'], BANNER_DIR, ['jpg', 'jpeg', 'png', 'gif']);
                
                if ($upload['success']) {
                    try {
                        $stmt = $db->prepare('INSERT INTO banners (title, description, image_path, link_url, display_order, is_active) VALUES (?, ?, ?, ?, ?, 1)');
                        $stmt->execute([$title, $description, $upload['relative_path'], $link_url, $display_order]);
                        
                        // PRG
                        header('Location: banners.php?msg=added');
                        exit;
                    } catch (PDOException $e) {
                        $error = 'Database Error: ' . $e->getMessage();
                        deleteFile($upload['path']);
                    }
                } else {
                    $error = 'Upload Error: ' . $upload['message'];
                }
            } else {
                $error = 'Please select an image file.';
            }
        }
    }
}

// Handle Delete/Toggle via GET to be consistent with other pages
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    try {
        // Get file path first
        $stmt = $db->prepare("SELECT image_path FROM banners WHERE id = ?");
        $stmt->execute([$id]);
        $file = $stmt->fetchColumn();
        
        if ($file) {
            deleteFile(dirname(__DIR__) . '/' . $file);
        }
        
        $stmt = $db->prepare('DELETE FROM banners WHERE id = ?');
        $stmt->execute([$id]);
        
        header('Location: banners.php?msg=deleted');
        exit;
    } catch (PDOException $e) {
        $error = 'Error: ' . $e->getMessage();
    }
}

if (isset($_GET['toggle'])) {
    $id = (int)$_GET['toggle'];
    try {
        $stmt = $db->prepare('UPDATE banners SET is_active = NOT is_active WHERE id = ?');
        $stmt->execute([$id]);
        
        header('Location: banners.php?msg=toggled');
        exit;
    } catch (PDOException $e) {
        $error = 'Error: ' . $e->getMessage();
    }
}

// Display messages from redirect
if (isset($_GET['msg'])) {
    if ($_GET['msg'] === 'added') $message = 'Banner added successfully!';
    if ($_GET['msg'] === 'deleted') $message = 'Banner deleted successfully!';
    if ($_GET['msg'] === 'toggled') $message = 'Banner status updated!';
}

// Get all banners
$banners = [];
try {
    $stmt = $db->query('SELECT * FROM banners ORDER BY display_order ASC, created_at DESC');
    $banners = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = 'Error fetching banners: ' . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Banners - ICOP Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="assets/admin.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>
    
    <div class="dashboard-container">
        <?php include 'includes/sidebar.php'; ?>
        
        <main class="main-content">
            <div class="page-header">
                <h1><i class="fas fa-images"></i> Manage Banners</h1>
                <p>Add, edit, or remove homepage slider banners</p>
            </div>
            
            <?php if ($message): ?>
                <div class="alert alert-success"><i class="fas fa-check-circle"></i> <?= $message ?></div>
            <?php endif; ?>
            
            <?php if ($error): ?>
                <div class="alert alert-error"><i class="fas fa-exclamation-circle"></i> <?= $error ?></div>
            <?php endif; ?>
            
            <!-- Add Banner Form -->
            <div class="content-card">
                <div class="card-header">
                    <h3><i class="fas fa-plus-circle"></i> Add New Banner</h3>
                </div>
                <div class="card-body">
                    <form method="POST" enctype="multipart/form-data" class="admin-form">
                        <input type="hidden" name="action" value="add">
                        <div class="form-row">
                            <div class="form-group">
                                <label for="title">Banner Title *</label>
                                <input type="text" id="title" name="title" required class="form-control">
                            </div>
                            <div class="form-group">
                                <label for="display_order">Display Order</label>
                                <input type="number" id="display_order" name="display_order" value="0" class="form-control">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="description">Description</label>
                            <textarea id="description" name="description" rows="3" class="form-control"></textarea>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="link_url">Link URL (Optional)</label>
                                <input type="text" id="link_url" name="link_url" placeholder="e.g. about.php" class="form-control">
                            </div>
                            <div class="form-group">
                                <label for="image">Banner Image *</label>
                                <input type="file" id="image" name="image" accept="image/*" required class="form-control">
                            </div>
                        </div>
                        <button type="submit" class="btn-primary">
                            <i class="fas fa-save"></i> Save Banner
                        </button>
                    </form>
                </div>
            </div>
            
            <!-- Banners List -->
            <div class="content-card">
                <div class="card-header">
                    <h3><i class="fas fa-list"></i> Current Banners</h3>
                </div>
                <div class="table-responsive">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Order</th>
                                <th>Image</th>
                                <th>Title</th>
                                <th>Link</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($banners)): ?>
                                <tr>
                                    <td colspan="6" class="text-center">No banners found.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($banners as $banner): ?>
                                    <tr>
                                        <td><?= $banner['display_order'] ?></td>
                                        <td>
                                            <img src="../<?= htmlspecialchars($banner['image_path']) ?>" alt="Banner" style="height: 50px; border-radius: 4px;">
                                        </td>
                                        <td>
                                            <strong><?= htmlspecialchars($banner['title']) ?></strong><br>
                                            <small><?= htmlspecialchars(substr($banner['description'], 0, 50)) ?>...</small>
                                        </td>
                                        <td><?= htmlspecialchars($banner['link_url']) ?></td>
                                        <td>
                                            <a href="?toggle=<?= $banner['id'] ?>" class="status-badge <?= $banner['is_active'] ? 'status-active' : 'status-inactive' ?>">
                                                <?= $banner['is_active'] ? 'Active' : 'Inactive' ?>
                                            </a>
                                        </td>
                                        <td>
                                            <a href="?delete=<?= $banner['id'] ?>" class="btn-icon btn-delete" onclick="return confirm('Are you sure you want to delete this banner?');">
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
