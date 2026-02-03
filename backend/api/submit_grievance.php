<?php
/**
 * Grievance Submission Handler
 */

require_once '../config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendJSON(['success' => false, 'message' => 'Invalid request method'], 405);
}

try {
    $db = Database::getInstance()->getConnection();
    
    // Sanitize input
    $category = sanitize($_POST['category'] ?? '');
    $subject = sanitize($_POST['subject'] ?? '');
    $message = sanitize($_POST['message'] ?? '');
    $contact = sanitize($_POST['contact'] ?? '');
    
    // Validate
    if (empty($category) || empty($subject) || empty($message)) {
        sendJSON(['success' => false, 'message' => 'Please fill all required fields'], 400);
    }
    
    // Create grievances table if not exists (usually should be in db_setup.php but adding check here for safety)
    $db->exec("CREATE TABLE IF NOT EXISTS grievances (
        id INT AUTO_INCREMENT PRIMARY KEY,
        category VARCHAR(50) NOT NULL,
        subject VARCHAR(255) NOT NULL,
        message TEXT NOT NULL,
        contact_info VARCHAR(100),
        status VARCHAR(20) DEFAULT 'New',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");

    // Insert into database
    $stmt = $db->prepare("
        INSERT INTO grievances (category, subject, message, contact_info, status) 
        VALUES (?, ?, ?, ?, 'New')
    ");
    
    $stmt->execute([$category, $subject, $message, $contact]);
    
    sendJSON([
        'success' => true,
        'message' => 'Your grievance has been submitted successfully. We will address it shortly.'
    ]);
    
} catch(PDOException $e) {
    error_log("Grievance Error: " . $e->getMessage());
    sendJSON([
        'success' => false,
        'message' => defined('DEBUG_MODE') && DEBUG_MODE ? $e->getMessage() : 'Error submitting grievance'
    ], 500);
}
?>
