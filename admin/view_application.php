<?php
require_once 'config.php';
requireAdminLogin();

if (!isset($_GET['id'])) {
    header('Location: applications.php');
    exit;
}

$id = (int)$_GET['id'];
$app = null;

try {
    $db = getDB();
    $stmt = $db->prepare("SELECT * FROM admission_applications WHERE id = ?");
    $stmt->execute([$id]);
    $app = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$app) {
        die("Application not found!");
    }
    
    // Handle status update
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
        $newStatus = $_POST['status'];
        $allowedStatuses = ['Pending', 'Approved', 'Rejected'];
        
        if (in_array($newStatus, $allowedStatuses)) {
            $updateStmt = $db->prepare("UPDATE admission_applications SET status = ? WHERE id = ?");
            if ($updateStmt->execute([$newStatus, $id])) {
                $success = "Application status updated successfully.";
                $app['status'] = $newStatus; // Update local variable
            } else {
                $error = "Failed to update status.";
            }
        }
    }
} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Application Details - ICOP Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="assets/admin.css">
    <style>
        .detail-section { margin-bottom: 30px; border-bottom: 1px solid #eee; padding-bottom: 20px; }
        .detail-section h3 { color: #667eea; margin-bottom: 15px; border-left: 4px solid #667eea; padding-left: 10px; }
        .detail-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 20px; }
        .detail-item label { display: block; font-size: 0.85rem; color: #666; margin-bottom: 5px; }
        .detail-item div { font-weight: 500; color: #333; }
        .doc-link { display: inline-block; padding: 5px 10px; background: #f0f0f0; border-radius: 4px; text-decoration: none; color: #333; font-size: 0.9rem; }
        .doc-link:hover { background: #e0e0e0; }
        .photo-preview { width: 150px; height: 180px; object-fit: cover; border: 1px solid #ddd; padding: 5px; }
        
        .status-box {
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            margin-bottom: 30px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-left: 5px solid #667eea;
        }
        .current-status {
            font-size: 1.1rem;
            font-weight: 600;
        }
        .status-badge {
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 0.9rem;
            display: inline-block;
            margin-left: 10px;
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
                <h1>
                    <a href="applications.php" style="color: #666; margin-right: 10px;"><i class="fas fa-arrow-left"></i></a>
                    Application #<?= htmlspecialchars($app['application_no']) ?>
                </h1>
                <div class="header-actions">
                    <button class="btn-primary" onclick="window.print()"><i class="fas fa-print"></i> Print</button>
                    <a href="applications.php?delete=<?= $app['id'] ?>" class="btn-primary" style="background-color: #dc3545;" onclick="return confirm('Are you sure you want to delete this application? This action cannot be undone.')">
                        <i class="fas fa-trash"></i> Delete
                    </a>
                </div>
            </div>
            
            <?php if (isset($success)): ?>
                <div class="alert alert-success"><?= $success ?></div>
            <?php endif; ?>
            
            <?php if (isset($error)): ?>
                <div class="alert alert-error"><?= $error ?></div>
            <?php endif; ?>
            
            <div class="status-box">
                <div class="current-status">
                    Current Status: 
                    <?php
                    $statusClass = 'status-pending';
                    if ($app['status'] == 'Approved') $statusClass = 'status-approved';
                    if ($app['status'] == 'Rejected') $statusClass = 'status-rejected';
                    ?>
                    <span class="status-badge <?= $statusClass ?>"><?= htmlspecialchars($app['status']) ?></span>
                </div>
                
                <form method="POST" style="display: flex; gap: 10px; align-items: center;">
                    <select name="status" class="form-control" style="width: auto; padding: 8px;">
                        <option value="Pending" <?= $app['status'] == 'Pending' ? 'selected' : '' ?>>Pending</option>
                        <option value="Approved" <?= $app['status'] == 'Approved' ? 'selected' : '' ?>>Approved</option>
                        <option value="Rejected" <?= $app['status'] == 'Rejected' ? 'selected' : '' ?>>Rejected</option>
                    </select>
                    <button type="submit" name="update_status" class="btn-primary">Update Status</button>
                </form>
            </div>
            
            <div class="content-card">
                <div class="detail-section">
                    <h3>Personal Information</h3>
                    <div style="display: flex; gap: 30px; margin-bottom: 20px;">
                        <div>
                            <label>Photo</label>
                            <?php if ($app['photo_path']): ?>
                                <img src="../<?= htmlspecialchars($app['photo_path']) ?>" class="photo-preview" alt="Student Photo">
                            <?php else: ?>
                                <div class="photo-preview" style="display: flex; align-items: center; justify-content: center; background: #f9f9f9;">No Photo</div>
                            <?php endif; ?>
                        </div>
                        <div class="detail-grid" style="flex: 1;">
                            <div class="detail-item"><label>Full Name</label><div><?= htmlspecialchars($app['full_name']) ?></div></div>
                            <div class="detail-item"><label>Course Applied</label><div><?= htmlspecialchars($app['course']) ?></div></div>
                            <div class="detail-item"><label>Date of Birth</label><div><?= date('d M Y', strtotime($app['dob'])) ?></div></div>
                            <div class="detail-item"><label>Gender</label><div><?= htmlspecialchars($app['gender']) ?></div></div>
                            <div class="detail-item"><label>Nationality</label><div><?= htmlspecialchars($app['nationality']) ?></div></div>
                            <div class="detail-item"><label>Category</label><div><?= htmlspecialchars($app['category']) ?></div></div>
                            <div class="detail-item"><label>Aadhaar No</label><div><?= htmlspecialchars($app['aadhaar']) ?></div></div>
                            <div class="detail-item"><label>ABC ID</label><div><?= htmlspecialchars($app['abc_id']) ?></div></div>
                        </div>
                    </div>
                </div>
                
                <div class="detail-section">
                    <h3>Contact Details</h3>
                    <div class="detail-grid">
                        <div class="detail-item"><label>Mobile</label><div><?= htmlspecialchars($app['mobile']) ?></div></div>
                        <div class="detail-item"><label>Email</label><div><?= htmlspecialchars($app['email']) ?></div></div>
                        <div class="detail-item"><label>Address</label><div><?= nl2br(htmlspecialchars($app['address'])) ?></div></div>
                        <div class="detail-item"><label>City/District</label><div><?= htmlspecialchars($app['city']) ?>, <?= htmlspecialchars($app['district']) ?></div></div>
                        <div class="detail-item"><label>State/Pincode</label><div><?= htmlspecialchars($app['state']) ?> - <?= htmlspecialchars($app['pincode']) ?></div></div>
                    </div>
                </div>
                
                <div class="detail-section">
                    <h3>Parent/Guardian Details</h3>
                    <div class="detail-grid">
                        <div class="detail-item"><label>Father's Name</label><div><?= htmlspecialchars($app['father_name']) ?></div></div>
                        <div class="detail-item"><label>Father's Occupation</label><div><?= htmlspecialchars($app['father_occupation']) ?></div></div>
                        <div class="detail-item"><label>Father's Mobile</label><div><?= htmlspecialchars($app['father_mobile']) ?></div></div>
                        <div class="detail-item"><label>Mother's Name</label><div><?= htmlspecialchars($app['mother_name']) ?></div></div>
                        <div class="detail-item"><label>Mother's Occupation</label><div><?= htmlspecialchars($app['mother_occupation']) ?></div></div>
                        <div class="detail-item"><label>Guardian Name</label><div><?= htmlspecialchars($app['guardian_name']) ?></div></div>
                    </div>
                </div>
                
                <div class="detail-section">
                    <h3>Academic Details</h3>
                    <h4 style="margin: 10px 0; color: #555;">SSC (10th)</h4>
                    <div class="detail-grid">
                        <div class="detail-item"><label>Board</label><div><?= htmlspecialchars($app['ssc_board']) ?></div></div>
                        <div class="detail-item"><label>Year</label><div><?= htmlspecialchars($app['ssc_year']) ?></div></div>
                        <div class="detail-item"><label>Percentage</label><div><?= htmlspecialchars($app['ssc_percentage']) ?>%</div></div>
                        <div class="detail-item"><label>Seat No</label><div><?= htmlspecialchars($app['ssc_seat_no']) ?></div></div>
                    </div>
                    
                    <h4 style="margin: 20px 0 10px; color: #555;">HSC/Diploma (12th)</h4>
                    <div class="detail-grid">
                        <div class="detail-item"><label>Stream</label><div><?= htmlspecialchars($app['hsc_stream']) ?></div></div>
                        <div class="detail-item"><label>Year</label><div><?= htmlspecialchars($app['hsc_year']) ?></div></div>
                        <div class="detail-item"><label>Percentage</label><div><?= htmlspecialchars($app['hsc_percentage']) ?>%</div></div>
                        <div class="detail-item"><label>PCB Marks</label><div><?= htmlspecialchars($app['pcb_marks']) ?></div></div>
                    </div>
                    
                    <h4 style="margin: 20px 0 10px; color: #555;">Entrance Exam</h4>
                    <div class="detail-grid">
                        <div class="detail-item"><label>Exam Name</label><div><?= htmlspecialchars($app['entrance_exam']) ?></div></div>
                        <div class="detail-item"><label>Score</label><div><?= htmlspecialchars($app['entrance_score']) ?></div></div>
                        <div class="detail-item"><label>Roll No</label><div><?= htmlspecialchars($app['entrance_roll_no']) ?></div></div>
                        <div class="detail-item"><label>Rank</label><div><?= htmlspecialchars($app['entrance_rank']) ?></div></div>
                    </div>
                </div>
                
                <div class="detail-section" style="border-bottom: none;">
                    <h3>Documents</h3>
                    <div class="detail-grid">
                        <div class="detail-item">
                            <label>Signature</label>
                            <?php if ($app['signature_path']): ?>
                                <img src="../<?= htmlspecialchars($app['signature_path']) ?>" style="height: 60px; border: 1px solid #ddd;" alt="Signature">
                            <?php else: ?>
                                <span class="text-muted">Not uploaded</span>
                            <?php endif; ?>
                        </div>
                        <div class="detail-item">
                            <label>Aadhaar Card</label>
                            <?php if ($app['aadhaar_doc_path']): ?>
                                <a href="../<?= htmlspecialchars($app['aadhaar_doc_path']) ?>" target="_blank" class="doc-link"><i class="fas fa-file-pdf"></i> View Document</a>
                            <?php else: ?>
                                <span class="text-muted">Not uploaded</span>
                            <?php endif; ?>
                        </div>
                        <div class="detail-item">
                            <label>SSC Marksheet</label>
                            <?php if ($app['ssc_doc_path']): ?>
                                <a href="../<?= htmlspecialchars($app['ssc_doc_path']) ?>" target="_blank" class="doc-link"><i class="fas fa-file-pdf"></i> View Document</a>
                            <?php else: ?>
                                <span class="text-muted">Not uploaded</span>
                            <?php endif; ?>
                        </div>
                        <div class="detail-item">
                            <label>HSC Marksheet</label>
                            <?php if ($app['hsc_doc_path']): ?>
                                <a href="../<?= htmlspecialchars($app['hsc_doc_path']) ?>" target="_blank" class="doc-link"><i class="fas fa-file-pdf"></i> View Document</a>
                            <?php else: ?>
                                <span class="text-muted">Not uploaded</span>
                            <?php endif; ?>
                        </div>
                        <div class="detail-item">
                            <label>Leaving Certificate</label>
                            <?php if ($app['lc_doc_path']): ?>
                                <a href="../<?= htmlspecialchars($app['lc_doc_path']) ?>" target="_blank" class="doc-link"><i class="fas fa-file-pdf"></i> View Document</a>
                            <?php else: ?>
                                <span class="text-muted">Not uploaded</span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
    
    <script src="assets/admin.js"></script>
</body>
</html>
