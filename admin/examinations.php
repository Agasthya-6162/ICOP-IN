<?php
require_once 'config.php';
requireAdminLogin();

$message = '';
$error = '';

// Handle Add Examination
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_exam'])) {
    $title = sanitizeInput($_POST['title']);
    $exam_date = sanitizeInput($_POST['exam_date']);
    $description = sanitizeInput($_POST['description']);
    
    $filePath = null;
    
    // Handle Optional File Upload
    if (isset($_FILES['exam_file']) && $_FILES['exam_file']['error'] === UPLOAD_ERR_OK) {
        $uploadResult = handleAdminFileUpload($_FILES['exam_file'], EXAM_DIR, ['pdf', 'jpg', 'jpeg', 'png']);
        if ($uploadResult['success']) {
            $filePath = $uploadResult['relative_path'];
        } else {
            $error = $uploadResult['message'];
        }
    }
    
    if (empty($error)) {
        try {
            $db = getDB();
            $stmt = $db->prepare("INSERT INTO examinations (title, exam_date, description, file_path, is_active) VALUES (?, ?, ?, ?, 1)");
            $stmt->execute([$title, $exam_date, $description, $filePath]);
            $message = "Examination schedule added successfully!";
        } catch (PDOException $e) {
            $error = "Database error: " . $e->getMessage();
            if ($filePath) deleteFile(dirname(__DIR__) . '/' . $filePath);
        }
    }
}

// Handle Delete
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    try {
        $db = getDB();
        // Get file path first
        $stmt = $db->prepare("SELECT file_path FROM examinations WHERE id = ?");
        $stmt->execute([$id]);
        $file = $stmt->fetchColumn();
        
        if ($file) {
            deleteFile(dirname(__DIR__) . '/' . $file);
        }
        
        $stmt = $db->prepare("DELETE FROM examinations WHERE id = ?");
        $stmt->execute([$id]);
        $message = "Examination deleted successfully!";
    } catch (PDOException $e) {
        $error = "Error deleting record: " . $e->getMessage();
    }
}

// Handle Toggle Status
if (isset($_GET['toggle'])) {
    $id = (int)$_GET['toggle'];
    try {
        $db = getDB();
        $stmt = $db->prepare("UPDATE examinations SET is_active = NOT is_active WHERE id = ?");
        $stmt->execute([$id]);
        $message = "Status updated successfully!";
    } catch (PDOException $e) {
        $error = "Error updating status: " . $e->getMessage();
    }
}

// Fetch Examinations
$exams = [];
try {
    $db = getDB();
    $stmt = $db->query("SELECT * FROM examinations ORDER BY exam_date DESC");
    $exams = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = "Error fetching data: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Examinations - ICOP Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="assets/admin.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>
    
    <div class="dashboard-container">
        <?php include 'includes/sidebar.php'; ?>
        
        <main class="main-content">
            <div class="page-header">
                <h1><i class="fas fa-clock"></i> Manage Examinations</h1>
            </div>
            
            <?php if ($message): ?>
                <div class="alert alert-success"><i class="fas fa-check-circle"></i> <?= $message ?></div>
            <?php endif; ?>
            
            <?php if ($error): ?>
                <div class="alert alert-error"><i class="fas fa-exclamation-circle"></i> <?= $error ?></div>
            <?php endif; ?>
            
            <!-- Add New Exam Form -->
            <div class="content-card">
                <div class="card-header">
                    <h3><i class="fas fa-plus"></i> Add New Examination</h3>
                </div>
                <div class="card-body">
                    <form method="POST" enctype="multipart/form-data" class="admin-form">
                        <div class="form-row">
                            <div class="form-group">
                                <label>Exam Title</label>
                                <input type="text" name="title" placeholder="e.g. B.Pharm Sem 1 Final Exam" required class="form-control">
                            </div>
                            <div class="form-group">
                                <label>Exam Date (Start)</label>
                                <input type="date" name="exam_date" required class="form-control">
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Description / Notes</label>
                            <textarea name="description" rows="3" class="form-control" placeholder="Additional details..."></textarea>
                        </div>
                        <div class="form-group">
                            <label>Schedule File (PDF/Image) - Optional</label>
                            <input type="file" name="exam_file" accept=".pdf,.jpg,.jpeg,.png" class="form-control">
                        </div>
                        <button type="submit" name="add_exam" class="btn-primary">
                            <i class="fas fa-save"></i> Save Examination
                        </button>
                    </form>
                </div>
            </div>
            
            <!-- Exam List -->
            <div class="content-card">
                <div class="card-header">
                    <h3><i class="fas fa-list"></i> Examination List</h3>
                </div>
                <div class="table-responsive">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Exam Date</th>
                                <th>Title</th>
                                <th>Description</th>
                                <th>File</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($exams)): ?>
                                <tr>
                                    <td colspan="6" class="text-center">No examinations found.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($exams as $exam): ?>
                                    <tr>
                                        <td><?= date('d M Y', strtotime($exam['exam_date'])) ?></td>
                                        <td><?= htmlspecialchars($exam['title']) ?></td>
                                        <td><?= htmlspecialchars(substr($exam['description'], 0, 50)) ?>...</td>
                                        <td>
                                            <?php if ($exam['file_path']): ?>
                                                <a href="../<?= htmlspecialchars($exam['file_path']) ?>" target="_blank" class="text-primary">
                                                    <i class="fas fa-download"></i> View
                                                </a>
                                            <?php else: ?>
                                                <span class="text-muted">-</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <a href="?toggle=<?= $exam['id'] ?>" class="status-badge <?= $exam['is_active'] ? 'status-active' : 'status-inactive' ?>">
                                                <?= $exam['is_active'] ? 'Active' : 'Inactive' ?>
                                            </a>
                                        </td>
                                        <td>
                                            <a href="?delete=<?= $exam['id'] ?>" class="btn-icon btn-delete" onclick="return confirm('Are you sure?');">
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
