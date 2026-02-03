<?php
/**
 * Student Feedback Submission Handler
 */

require_once '../config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendJSON(['success' => false, 'message' => 'Invalid request method'], 405);
}

try {
    $db = Database::getInstance()->getConnection();
    
    // Sanitize input
    $category = sanitize($_POST['category'] ?? 'General');
    $subject = sanitize($_POST['subject'] ?? '');
    $message = sanitize($_POST['message'] ?? '');
    $contactInfo = sanitize($_POST['contactInfo'] ?? '');
    $isAnonymous = empty($contactInfo) ? 1 : 0;
    
    // Validate
    if (empty($message)) {
        sendJSON(['success' => false, 'message' => 'Please provide your feedback message'], 400);
    }
    
    // Insert into database
    $stmt = $db->prepare("
        INSERT INTO student_feedback (category, subject, message, is_anonymous, contact_info, status) 
        VALUES (?, ?, ?, ?, ?, 'New')
    ");
    
    $stmt->execute([$category, $subject, $message, $isAnonymous, $contactInfo]);
    
    sendJSON([
        'success' => true,
        'message' => 'Thank you for your feedback. Your voice matters!'
    ]);
    
} catch(PDOException $e) {
    sendJSON([
        'success' => false,
        'message' => DEBUG_MODE ? $e->getMessage() : 'Error submitting feedback'
    ], 500);
}
?>
