<?php
require_once 'config.php';
requireAdminLogin();

$db = getDB();
$message = '';

// Determine mode: 'news', 'event', or 'all' (default)
$type = isset($_GET['type']) ? $_GET['type'] : 'all';

// Set page properties based on type
$pageTitle = 'Manage Notices';
$pageIcon = 'fa-bell';
$categoryFilter = ''; // SQL filter

switch ($type) {
    case 'news':
        $pageTitle = 'Manage News';
        $pageIcon = 'fa-newspaper';
        $categoryFilter = " AND category != 'Event'";
        break;
    case 'event':
        $pageTitle = 'Manage Events';
        $pageIcon = 'fa-calendar-alt';
        $categoryFilter = " AND category = 'Event'";
        break;
}

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'add') {
        $title = sanitize($_POST['title']);
        $content = sanitize($_POST['content']);
        $category = sanitize($_POST['category']);
        $is_important = isset($_POST['is_important']) ? 1 : 0;
        $publish_date = sanitize($_POST['publish_date']);
        $expiry_date = sanitize($_POST['expiry_date']) ?: null;
        
        // Force category if in event mode
        if ($type === 'event') {
            $category = 'Event';
        }
        
        // Check for duplicates
        $checkStmt = $db->prepare("SELECT COUNT(*) FROM notices WHERE title = ? AND category = ? AND publish_date = ?");
        $checkStmt->execute([$title, $category, $publish_date]);
        if ($checkStmt->fetchColumn() > 0) {
            $message = '<div class="alert alert-warning">A similar notice already exists.</div>';
        } else {
            $attachment_path = null;
            if (isset($_FILES['attachment']) && $_FILES['attachment']['error'] === UPLOAD_ERR_OK) {
                $upload = handleAdminFileUpload($_FILES['attachment'], NOTICE_DIR, ['pdf', 'doc', 'docx', 'jpg', 'jpeg', 'png']);
                if ($upload['success']) {
                    $attachment_path = $upload['relative_path'];
                } else {
                    $message = '<div class="alert alert-danger">Upload Error: ' . $upload['message'] . '</div>';
                }
            }
            
            if (empty($message)) {
                try {
                    $stmt = $db->prepare('INSERT INTO notices (title, content, category, attachment_path, is_important, publish_date, expiry_date) VALUES (?, ?, ?, ?, ?, ?, ?)');
                    $stmt->execute([$title, $content, $category, $attachment_path, $is_important, $publish_date, $expiry_date]);
                    
                    // PRG Pattern
                    header("Location: notices.php?type=$type&msg=added");
                    exit;
                } catch (PDOException $e) {
                    if ($attachment_path) {
                        deleteFile(dirname(__DIR__) . '/' . $attachment_path);
                    }
                    $message = '<div class="alert alert-danger">Error: ' . htmlspecialchars($e->getMessage()) . '</div>';
                }
            }
        }
    } elseif ($action === 'delete') {
        $id = (int)$_POST['id'];
        try {
            // Get file path first
            $stmt = $db->prepare('SELECT attachment_path FROM notices WHERE id = ?');
            $stmt->execute([$id]);
            $file = $stmt->fetchColumn();

            // Delete record
            $stmt = $db->prepare('DELETE FROM notices WHERE id = ?');
            $stmt->execute([$id]);
            
            // Delete physical file
            if ($file) {
                deleteFile(dirname(__DIR__) . '/' . $file);
            }
            
            header("Location: notices.php?type=$type&msg=deleted");
            exit;
        } catch (PDOException $e) {
            $message = '<div class="alert alert-danger">Error: ' . htmlspecialchars($e->getMessage()) . '</div>';
        }
    }
}

// Display messages from redirect
if (isset($_GET['msg'])) {
    if ($_GET['msg'] === 'added') $message = '<div class="alert alert-success">Published successfully!</div>';
    if ($_GET['msg'] === 'deleted') $message = '<div class="alert alert-success">Deleted successfully!</div>';
}

// Get notices based on filter
$notices = [];
try {
    $sql = "SELECT * FROM notices WHERE 1=1 $categoryFilter ORDER BY is_important DESC, publish_date DESC";
    $stmt = $db->query($sql);
    $notices = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $message = '<div class="alert alert-danger">Error fetching notices: ' . htmlspecialchars($e->getMessage()) . '</div>';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?> - ICOP Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="assets/admin.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>
    
    <div class="dashboard-container">
        <?php include 'includes/sidebar.php'; ?>
        
        <main class="main-content">
            <div class="page-header">
                <h1><i class="fas <?= $pageIcon ?>"></i> <?= $pageTitle ?></h1>
                <p>
                    <?php if ($type === 'event'): ?>
                        Manage upcoming events and schedules
                    <?php elseif ($type === 'news'): ?>
                        Manage latest news and announcements
                    <?php else: ?>
                        Post and manage all announcements
                    <?php endif; ?>
                </p>
            </div>
            
            <?= $message ?>
            
            <!-- Add Notice Form -->
            <div class="content-card">
                <div class="card-header">
                    <h2><i class="fas fa-plus-circle"></i> Add New <?= ($type === 'event') ? 'Event' : (($type === 'news') ? 'News Item' : 'Notice') ?></h2>
                </div>
                <div class="card-body">
                    <form method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="action" value="add">
                        <div class="form-group">
                            <label for="title">Title *</label>
                            <input type="text" id="title" name="title" required class="form-control">
                        </div>
                        <div class="form-group">
                            <label for="content">Content *</label>
                            <textarea id="content" name="content" rows="6" required class="form-control"></textarea>
                        </div>
                        <div class="grid-3">
                            <div class="form-group">
                                <label for="category">Category</label>
                                <?php if ($type === 'event'): ?>
                                    <input type="text" class="form-control" value="Event" disabled>
                                    <input type="hidden" name="category" value="Event">
                                <?php else: ?>
                                    <select id="category" name="category" class="form-control">
                                        <option value="General">General</option>
                                        <option value="Academic">Academic</option>
                                        <option value="Examination">Examination</option>
                                        <option value="Admission">Admission</option>
                                        <!-- Hide Event option if in News mode -->
                                        <?php if ($type !== 'news'): ?>
                                            <option value="Event">Event</option>
                                        <?php endif; ?>
                                    </select>
                                <?php endif; ?>
                            </div>
                            <div class="form-group">
                                <label for="publish_date">Publish Date *</label>
                                <input type="date" id="publish_date" name="publish_date" value="<?= date('Y-m-d') ?>" required class="form-control">
                            </div>
                            <div class="form-group">
                                <label for="expiry_date">Expiry Date (Optional)</label>
                                <input type="date" id="expiry_date" name="expiry_date" class="form-control">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="attachment">Attachment (Optional)</label>
                            <input type="file" id="attachment" name="attachment" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png" class="form-control">
                        </div>
                        <div class="form-group">
                            <label style="display: flex; align-items: center; gap: 10px; cursor: pointer;">
                                <input type="checkbox" name="is_important" value="1">
                                <span>Mark as Important/Urgent</span>
                            </label>
                        </div>
                        <button type="submit" class="btn-primary">
                            <i class="fas fa-save"></i> Publish
                        </button>
                    </form>
                </div>
            </div>
            
            <!-- List -->
            <div class="content-card">
                <div class="card-header">
                    <h2><i class="fas fa-list"></i> Current <?= ($type === 'event') ? 'Events' : 'Items' ?></h2>
                </div>
                <div class="table-responsive">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Title</th>
                                <th>Category</th>
                                <th>Content Preview</th>
                                <th>Publish Date</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($notices)): ?>
                                <tr>
                                    <td colspan="6" style="text-align: center; padding: 40px;">
                                        <i class="fas fa-folder-open" style="font-size: 48px; color: #ddd;"></i>
                                        <p style="margin-top: 15px; color: #999;">No records found.</p>
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($notices as $notice): ?>
                                    <tr>
                                        <td>
                                            <?= htmlspecialchars($notice['title']) ?>
                                            <?php if ($notice['is_important']): ?>
                                                <span class="badge" style="background: #ff6b35; color: white; margin-left: 8px;">
                                                    <i class="fas fa-exclamation"></i> Important
                                                </span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <span class="badge" style="background: #e8f4f8; color: #0066cc;">
                                                <?= htmlspecialchars($notice['category']) ?>
                                            </span>
                                        </td>
                                        <td><?= htmlspecialchars(substr(strip_tags($notice['content']), 0, 60)) ?>...</td>
                                        <td><?= date('M d, Y', strtotime($notice['publish_date'])) ?></td>
                                        <td>
                                            <?php if ($notice['expiry_date'] && strtotime($notice['expiry_date']) < time()): ?>
                                                <span class="badge badge-danger">Expired</span>
                                            <?php else: ?>
                                                <span class="badge badge-success">Active</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if ($notice['attachment_path']): ?>
                                                <a href="../<?= htmlspecialchars($notice['attachment_path']) ?>" 
                                                   class="btn-icon" 
                                                   title="View Attachment"
                                                   target="_blank">
                                                    <i class="fas fa-paperclip"></i>
                                                </a>
                                            <?php endif; ?>
                                            <form method="POST" style="display: inline;">
                                                <input type="hidden" name="action" value="delete">
                                                <input type="hidden" name="id" value="<?= $notice['id'] ?>">
                                                <button type="submit" class="btn-icon delete-btn" onclick="return confirm('Delete this item?')" title="Delete">
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