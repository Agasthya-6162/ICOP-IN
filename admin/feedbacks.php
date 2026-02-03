<?php
require_once 'config.php';
requireAdminLogin();

// Handle Delete
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    try {
        $db = getDB();
        $stmt = $db->prepare("DELETE FROM student_feedback WHERE id = ?");
        $stmt->execute([$id]);
        header('Location: feedbacks.php?msg=deleted');
        exit;
    } catch (PDOException $e) {
        $error = "Error deleting feedback: " . $e->getMessage();
    }
}

// Get feedback
$feedbacks = [];
try {
    $db = getDB();
    $stmt = $db->query("SELECT * FROM student_feedback ORDER BY created_at DESC");
    $feedbacks = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = "Error fetching feedback: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Feedback - ICOP Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="assets/admin.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>
    
    <div class="dashboard-container">
        <?php include 'includes/sidebar.php'; ?>
        
        <main class="main-content">
            <div class="page-header">
                <h1><i class="fas fa-comment-dots"></i> Student Feedback</h1>
            </div>
            
            <?php if (isset($_GET['msg']) && $_GET['msg'] == 'deleted'): ?>
                <div class="alert alert-success">Feedback deleted successfully!</div>
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
                                <th>Category</th>
                                <th>Subject</th>
                                <th>Message</th>
                                <th>Contact</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($feedbacks)): ?>
                                <tr>
                                    <td colspan="6" style="text-align: center; padding: 20px;">No feedback found</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($feedbacks as $item): ?>
                                    <tr>
                                        <td><?= date('d M Y', strtotime($item['created_at'])) ?></td>
                                        <td><?= htmlspecialchars($item['category']) ?></td>
                                        <td><?= htmlspecialchars($item['subject']) ?></td>
                                        <td><?= nl2br(htmlspecialchars($item['message'])) ?></td>
                                        <td>
                                            <?php if ($item['is_anonymous']): ?>
                                                <span class="badge badge-secondary">Anonymous</span>
                                            <?php else: ?>
                                                <?= htmlspecialchars($item['contact_info']) ?>
                                            <?php endif; ?>
                                        </td>
                                        <td class="actions">
                                            <a href="feedbacks.php?delete=<?= $item['id'] ?>" class="btn-icon delete-btn" title="Delete" onclick="return confirm('Are you sure?')">
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
