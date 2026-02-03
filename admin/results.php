<?php
require_once 'config.php';
requireAdminLogin();

$db = getDB();
$message = '';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'add') {
        $title = sanitize($_POST['title']);
        $program = sanitize($_POST['program']);
        $semester = sanitize($_POST['semester']);
        $exam_type = sanitize($_POST['exam_type']);
        $publish_date = sanitize($_POST['publish_date']);
        
        // Check for duplicates
        $checkStmt = $db->prepare("SELECT COUNT(*) FROM results WHERE title = ? AND program = ? AND semester = ?");
        $checkStmt->execute([$title, $program, $semester]);
        if ($checkStmt->fetchColumn() > 0) {
            $message = '<div class="alert alert-warning">This result record already exists.</div>';
        } else {
            $result_file = null;
            if (isset($_FILES['result_file']) && $_FILES['result_file']['error'] === UPLOAD_ERR_OK) {
                $upload = handleAdminFileUpload($_FILES['result_file'], RESULT_DIR, ['pdf']);
                if ($upload['success']) {
                    $result_file = $upload['relative_path'];
                } else {
                    $message = '<div class="alert alert-danger">Upload Error: ' . $upload['message'] . '</div>';
                }
            }
            
            if (empty($message)) {
                try {
                    $stmt = $db->prepare('INSERT INTO results (title, program, semester, exam_type, result_file, publish_date) VALUES (?, ?, ?, ?, ?, ?)');
                    $stmt->execute([$title, $program, $semester, $exam_type, $result_file, $publish_date]);
                    
                    header('Location: results.php?msg=added');
                    exit;
                } catch (PDOException $e) {
                    if ($result_file) {
                        deleteFile(dirname(__DIR__) . '/' . $result_file);
                    }
                    $message = '<div class="alert alert-danger">Database Error: ' . htmlspecialchars($e->getMessage()) . '</div>';
                }
            }
        }
    } elseif ($action === 'delete') {
        $id = (int)$_POST['id'];
        try {
            // Get file path
            $stmt = $db->prepare('SELECT result_file FROM results WHERE id = ?');
            $stmt->execute([$id]);
            $file = $stmt->fetchColumn();

            // Delete record
            $stmt = $db->prepare('DELETE FROM results WHERE id = ?');
            $stmt->execute([$id]);

            // Delete physical file
            if ($file) {
                deleteFile(dirname(__DIR__) . '/' . $file);
            }
            
            header('Location: results.php?msg=deleted');
            exit;
        } catch (PDOException $e) {
            $message = '<div class="alert alert-danger">Error: ' . htmlspecialchars($e->getMessage()) . '</div>';
        }
    }
}

if (isset($_GET['msg'])) {
    if ($_GET['msg'] === 'added') $message = '<div class="alert alert-success">Result published successfully!</div>';
    if ($_GET['msg'] === 'deleted') $message = '<div class="alert alert-success">Result deleted successfully!</div>';
}

// Get all results
$results = [];
try {
    $stmt = $db->query('SELECT * FROM results ORDER BY publish_date DESC');
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $message = '<div class="alert alert-danger">Error fetching results: ' . htmlspecialchars($e->getMessage()) . '</div>';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Results - ICOP Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="assets/admin.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>
    
    <div class="dashboard-container">
        <?php include 'includes/sidebar.php'; ?>
        
        <main class="main-content">
            <div class="page-header">
                <h1><i class="fas fa-graduation-cap"></i> Manage Results</h1>
                <p>Publish and manage examination results</p>
            </div>
            
            <?= $message ?>
            
            <!-- Add Result Form -->
            <div class="content-card">
                <div class="card-header">
                    <h2><i class="fas fa-plus-circle"></i> Publish New Result</h2>
                </div>
                <div class="card-body">
                    <form method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="action" value="add">
                        <div class="form-group">
                            <label for="title">Result Title *</label>
                            <input type="text" id="title" name="title" placeholder="e.g., B.Pharm Semester VIII Results - Summer 2025" required class="form-control">
                        </div>
                        <div class="grid-3">
                            <div class="form-group">
                                <label for="program">Program *</label>
                                <select id="program" name="program" required class="form-control">
                                    <option value="">Select Program</option>
                                    <option value="B.Pharm">B.Pharm</option>
                                    <option value="D.Pharm">D.Pharm</option>
                                    <option value="M.Pharm">M.Pharm</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="semester">Semester/Year *</label>
                                <input type="text" id="semester" name="semester" placeholder="e.g., Semester VIII" required class="form-control">
                            </div>
                            <div class="form-group">
                                <label for="exam_type">Exam Type *</label>
                                <select id="exam_type" name="exam_type" required class="form-control">
                                    <option value="">Select Type</option>
                                    <option value="Regular">Regular</option>
                                    <option value="Supplementary">Supplementary</option>
                                    <option value="ATKT">ATKT</option>
                                </select>
                            </div>
                        </div>
                        <div class="grid-2">
                            <div class="form-group">
                                <label for="result_file">Result PDF File *</label>
                                <input type="file" id="result_file" name="result_file" accept=".pdf" required class="form-control">
                            </div>
                            <div class="form-group">
                                <label for="publish_date">Publish Date *</label>
                                <input type="date" id="publish_date" name="publish_date" value="<?= date('Y-m-d') ?>" required class="form-control">
                            </div>
                        </div>
                        <button type="submit" class="btn-primary">
                            <i class="fas fa-upload"></i> Publish Result
                        </button>
                    </form>
                </div>
            </div>
            
            <!-- Results List -->
            <div class="content-card">
                <div class="card-header">
                    <h2><i class="fas fa-list"></i> Published Results</h2>
                </div>
                <div class="table-responsive">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Title</th>
                                <th>Program</th>
                                <th>Semester</th>
                                <th>Exam Type</th>
                                <th>Published On</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($results)): ?>
                                <tr>
                                    <td colspan="6" style="text-align: center; padding: 40px;">
                                        <i class="fas fa-file-alt" style="font-size: 48px; color: #ddd;"></i>
                                        <p style="margin-top: 15px; color: #999;">No results published yet. Add your first result above!</p>
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($results as $result): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($result['title']) ?></td>
                                        <td>
                                            <span class="badge" style="background: #667eea; color: white;">
                                                <?= htmlspecialchars($result['program']) ?>
                                            </span>
                                        </td>
                                        <td><?= htmlspecialchars($result['semester']) ?></td>
                                        <td>
                                            <span class="badge badge-success">
                                                <?= htmlspecialchars($result['exam_type']) ?>
                                            </span>
                                        </td>
                                        <td><?= date('M d, Y', strtotime($result['publish_date'])) ?></td>
                                        <td>
                                            <?php if ($result['result_file']): ?>
                                                <a href="../<?= htmlspecialchars($result['result_file']) ?>" 
                                                   class="btn-icon" 
                                                   title="View PDF"
                                                   target="_blank">
                                                    <i class="fas fa-file-pdf"></i>
                                                </a>
                                            <?php endif; ?>
                                            <form method="POST" style="display: inline;">
                                                <input type="hidden" name="action" value="delete">
                                                <input type="hidden" name="id" value="<?= $result['id'] ?>">
                                                <button type="submit" class="btn-icon delete-btn" onclick="return confirm('Delete this result?')" title="Delete">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
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
