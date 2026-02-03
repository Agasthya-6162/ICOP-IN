<?php
require_once 'config.php';
requireAdminLogin();

$message = '';
$error = '';

// Handle Add Syllabus
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_syllabus'])) {
    $course = sanitizeInput($_POST['course']);
    $semester = sanitizeInput($_POST['semester']);
    $subject = sanitizeInput($_POST['subject_name']);
    
    // Check for duplicates
    $db = getDB();
    $checkStmt = $db->prepare("SELECT COUNT(*) FROM syllabus WHERE course_name = ? AND semester = ? AND subject_name = ?");
    $checkStmt->execute([$course, $semester, $subject]);
    
    if ($checkStmt->fetchColumn() > 0) {
        $error = "This syllabus already exists.";
    } else {
        // Handle File Upload
        if (isset($_FILES['syllabus_file']) && $_FILES['syllabus_file']['error'] === UPLOAD_ERR_OK) {
            $uploadResult = handleAdminFileUpload($_FILES['syllabus_file'], SYLLABUS_DIR, ['pdf']);
            
            if ($uploadResult['success']) {
                try {
                    $stmt = $db->prepare("INSERT INTO syllabus (course_name, semester, subject_name, file_path, is_active) VALUES (?, ?, ?, ?, 1)");
                    $stmt->execute([$course, $semester, $subject, $uploadResult['relative_path']]);
                    
                    header('Location: syllabus.php?msg=added');
                    exit;
                } catch (PDOException $e) {
                    $error = "Database error: " . $e->getMessage();
                    // Clean up file if DB insert fails
                    deleteFile($uploadResult['path']);
                }
            } else {
                $error = $uploadResult['message'];
            }
        } else {
            $error = "Please select a valid PDF file.";
        }
    }
}

// Handle Delete
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    try {
        $db = getDB();
        // Get file path first
        $stmt = $db->prepare("SELECT file_path FROM syllabus WHERE id = ?");
        $stmt->execute([$id]);
        $file = $stmt->fetchColumn();
        
        if ($file) {
            $stmt = $db->prepare("DELETE FROM syllabus WHERE id = ?");
            if ($stmt->execute([$id])) {
                // Delete physical file
                deleteFile(dirname(__DIR__) . '/' . $file);
                
                header('Location: syllabus.php?msg=deleted');
                exit;
            }
        }
    } catch (PDOException $e) {
        $error = "Error deleting record: " . $e->getMessage();
    }
}

// Handle Toggle Status
if (isset($_GET['toggle'])) {
    $id = (int)$_GET['toggle'];
    try {
        $db = getDB();
        $stmt = $db->prepare("UPDATE syllabus SET is_active = NOT is_active WHERE id = ?");
        $stmt->execute([$id]);
        
        header('Location: syllabus.php?msg=toggled');
        exit;
    } catch (PDOException $e) {
        $error = "Error updating status: " . $e->getMessage();
    }
}

// Display messages
if (isset($_GET['msg'])) {
    if ($_GET['msg'] === 'added') $message = "Syllabus added successfully!";
    if ($_GET['msg'] === 'deleted') $message = "Syllabus deleted successfully!";
    if ($_GET['msg'] === 'toggled') $message = "Status updated successfully!";
}

// Fetch Syllabus
$syllabusItems = [];
try {
    $db = getDB();
    $stmt = $db->query("SELECT * FROM syllabus ORDER BY created_at DESC");
    $syllabusItems = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = "Error fetching data: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Syllabus - ICOP Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="assets/admin.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>
    
    <div class="dashboard-container">
        <?php include 'includes/sidebar.php'; ?>
        
        <main class="main-content">
            <div class="page-header">
                <h1><i class="fas fa-book"></i> Manage Syllabus</h1>
            </div>
            
            <?php if ($message): ?>
                <div class="alert alert-success"><i class="fas fa-check-circle"></i> <?= $message ?></div>
            <?php endif; ?>
            
            <?php if ($error): ?>
                <div class="alert alert-error"><i class="fas fa-exclamation-circle"></i> <?= $error ?></div>
            <?php endif; ?>
            
            <!-- Add New Syllabus Form -->
            <div class="content-card">
                <div class="card-header">
                    <h3><i class="fas fa-plus"></i> Add New Syllabus</h3>
                </div>
                <div class="card-body">
                    <form method="POST" enctype="multipart/form-data" class="admin-form">
                        <div class="form-row">
                            <div class="form-group">
                                <label>Course</label>
                                <select name="course" required class="form-control">
                                    <option value="">Select Course</option>
                                    <option value="B.Pharm">B.Pharm</option>
                                    <option value="M.Pharm">M.Pharm</option>
                                    <option value="D.Pharm">D.Pharm</option>
                                    <option value="Pharm.D">Pharm.D</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Semester/Year</label>
                                <input type="text" name="semester" placeholder="e.g. Semester 1" required class="form-control">
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label>Subject Name</label>
                                <input type="text" name="subject_name" placeholder="Subject Name" required class="form-control">
                            </div>
                            <div class="form-group">
                                <label>Syllabus PDF</label>
                                <input type="file" name="syllabus_file" accept=".pdf" required class="form-control">
                            </div>
                        </div>
                        <button type="submit" name="add_syllabus" class="btn-primary">
                            <i class="fas fa-upload"></i> Upload Syllabus
                        </button>
                    </form>
                </div>
            </div>
            
            <!-- Syllabus List -->
            <div class="content-card">
                <div class="card-header">
                    <h3><i class="fas fa-list"></i> Syllabus List</h3>
                </div>
                <div class="table-responsive">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Course</th>
                                <th>Semester</th>
                                <th>Subject</th>
                                <th>File</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($syllabusItems)): ?>
                                <tr>
                                    <td colspan="7" class="text-center">No syllabus records found.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($syllabusItems as $item): ?>
                                    <tr>
                                        <td><?= date('d M Y', strtotime($item['created_at'])) ?></td>
                                        <td><?= htmlspecialchars($item['course_name']) ?></td>
                                        <td><?= htmlspecialchars($item['semester']) ?></td>
                                        <td><?= htmlspecialchars($item['subject_name']) ?></td>
                                        <td>
                                            <a href="../<?= htmlspecialchars($item['file_path']) ?>" target="_blank" class="text-primary">
                                                <i class="fas fa-file-pdf"></i> View
                                            </a>
                                        </td>
                                        <td>
                                            <a href="?toggle=<?= $item['id'] ?>" class="status-badge <?= $item['is_active'] ? 'status-active' : 'status-inactive' ?>">
                                                <?= $item['is_active'] ? 'Active' : 'Inactive' ?>
                                            </a>
                                        </td>
                                        <td>
                                            <a href="?delete=<?= $item['id'] ?>" class="btn-icon btn-delete" onclick="return confirm('Are you sure you want to delete this syllabus?');">
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
