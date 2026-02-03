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
        $category = sanitize($_POST['category']);
        $display_order = (int)$_POST['display_order'];
        
        // Check for duplicates
        $checkStmt = $db->prepare("SELECT COUNT(*) FROM gallery WHERE title = ? AND category = ?");
        $checkStmt->execute([$title, $category]);
        if ($checkStmt->fetchColumn() > 0) {
            $error = 'An image with this title already exists in this category.';
        } else {
            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $upload = handleAdminFileUpload($_FILES['image'], GALLERY_DIR, ['jpg', 'jpeg', 'png', 'gif']);
                
                if ($upload['success']) {
                    try {
                        $stmt = $db->prepare('INSERT INTO gallery (title, description, image_path, category, display_order, is_active) VALUES (?, ?, ?, ?, ?, 1)');
                        $stmt->execute([$title, $description, $upload['relative_path'], $category, $display_order]);
                        
                        // Post-Redirect-Get pattern
                        header('Location: gallery.php?msg=added');
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

// Handle Delete/Toggle
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    try {
        // Get file path first
        $stmt = $db->prepare("SELECT image_path FROM gallery WHERE id = ?");
        $stmt->execute([$id]);
        $file = $stmt->fetchColumn();
        
        if ($file) {
            // Construct full path properly
            $fullPath = dirname(__DIR__) . '/' . $file;
            deleteFile($fullPath);
        }
        
        $stmt = $db->prepare('DELETE FROM gallery WHERE id = ?');
        $stmt->execute([$id]);
        
        header('Location: gallery.php?msg=deleted');
        exit;
    } catch (PDOException $e) {
        $error = 'Error: ' . $e->getMessage();
    }
}

if (isset($_GET['toggle'])) {
    $id = (int)$_GET['toggle'];
    try {
        $stmt = $db->prepare('UPDATE gallery SET is_active = NOT is_active WHERE id = ?');
        $stmt->execute([$id]);
        
        header('Location: gallery.php?msg=toggled');
        exit;
    } catch (PDOException $e) {
        $error = 'Error: ' . $e->getMessage();
    }
}

// Display messages from redirect
if (isset($_GET['msg'])) {
    if ($_GET['msg'] === 'added') $message = 'Image added to gallery successfully!';
    if ($_GET['msg'] === 'deleted') $message = 'Gallery image deleted successfully!';
    if ($_GET['msg'] === 'toggled') $message = 'Gallery status updated!';
}

// Get all gallery images
$gallery = [];
try {
    $stmt = $db->query('SELECT * FROM gallery ORDER BY display_order ASC, created_at DESC');
    $gallery = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = 'Error fetching gallery: ' . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Gallery - ICOP Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="assets/admin.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>
    
    <div class="dashboard-container">
        <?php include 'includes/sidebar.php'; ?>
        
        <main class="main-content">
            <div class="page-header">
                <h1><i class="fas fa-photo-video"></i> Manage Gallery</h1>
                <p>Upload and manage college photo gallery</p>
            </div>
            
            <?php if ($message): ?>
                <div class="alert alert-success"><i class="fas fa-check-circle"></i> <?= $message ?></div>
            <?php endif; ?>
            
            <?php if ($error): ?>
                <div class="alert alert-error"><i class="fas fa-exclamation-circle"></i> <?= $error ?></div>
            <?php endif; ?>
            
            <!-- Add Image Form -->
            <div class="content-card">
                <div class="card-header">
                    <h3><i class="fas fa-plus-circle"></i> Add New Image</h3>
                </div>
                <div class="card-body">
                    <form method="POST" enctype="multipart/form-data" class="admin-form">
                        <input type="hidden" name="action" value="add">
                        <div class="form-row">
                            <div class="form-group">
                                <label for="title">Image Title *</label>
                                <input type="text" id="title" name="title" required class="form-control">
                            </div>
                            <div class="form-group">
                                <label for="category">Category</label>
                                <select id="category" name="category" class="form-control">
                                    <option value="Campus">Campus</option>
                                    <option value="Events">Events</option>
                                    <option value="Labs & Facilities">Labs & Facilities</option>
                                    <option value="Activities">Activities</option>
                                    <option value="Other">Other</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="description">Description</label>
                            <textarea id="description" name="description" rows="3" class="form-control"></textarea>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="display_order">Display Order</label>
                                <input type="number" id="display_order" name="display_order" value="0" class="form-control">
                            </div>
                            <div class="form-group">
                                <label for="image">Image File *</label>
                                <input type="file" id="image" name="image" accept="image/*" required class="form-control">
                            </div>
                        </div>
                        <button type="submit" class="btn-primary">
                            <i class="fas fa-save"></i> Add to Gallery
                        </button>
                    </form>
                </div>
            </div>
            
            <!-- Gallery List -->
            <div class="content-card">
                <div class="card-header">
                    <h3><i class="fas fa-list"></i> Current Gallery Images</h3>
                </div>
                <div class="table-responsive">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Order</th>
                                <th>Image</th>
                                <th>Title</th>
                                <th>Category</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($gallery)): ?>
                                <tr>
                                    <td colspan="6" class="text-center">No images found.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($gallery as $item): ?>
                                    <tr>
                                        <td><?= $item['display_order'] ?></td>
                                        <td>
                                            <img src="../<?= htmlspecialchars($item['image_path']) ?>" alt="Gallery" style="height: 50px; border-radius: 4px;">
                                        </td>
                                        <td>
                                            <strong><?= htmlspecialchars($item['title']) ?></strong><br>
                                            <small><?= htmlspecialchars(substr($item['description'], 0, 50)) ?>...</small>
                                        </td>
                                        <td><span class="badge badge-info"><?= htmlspecialchars($item['category']) ?></span></td>
                                        <td>
                                            <a href="?toggle=<?= $item['id'] ?>" class="status-badge <?= $item['is_active'] ? 'status-active' : 'status-inactive' ?>">
                                                <?= $item['is_active'] ? 'Active' : 'Inactive' ?>
                                            </a>
                                        </td>
                                        <td>
                                            <a href="?delete=<?= $item['id'] ?>" class="btn-icon btn-delete" onclick="return confirm('Are you sure you want to delete this image?');">
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
