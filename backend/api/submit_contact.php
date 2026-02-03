<?php
/**
 * Contact Form Submission Handler
 */

require_once '../config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendJSON(['success' => false, 'message' => 'Invalid request method'], 405);
}

try {
    $db = Database::getInstance()->getConnection();
    
    // Sanitize input
    $name = sanitize($_POST['name'] ?? '');
    $email = sanitize($_POST['email'] ?? '');
    $phone = sanitize($_POST['phone'] ?? '');
    $inquiryType = sanitize($_POST['inquiryType'] ?? 'General');
    $message = sanitize($_POST['message'] ?? '');
    
    // Validate
    if (empty($name) || empty($email) || empty($message)) {
        sendJSON(['success' => false, 'message' => 'Please fill all required fields'], 400);
    }
    
    // Insert into database
    $stmt = $db->prepare("
        INSERT INTO contact_submissions (name, email, phone, inquiry_type, message, status) 
        VALUES (?, ?, ?, ?, ?, 'New')
    ");
    
    $stmt->execute([$name, $email, $phone, $inquiryType, $message]);
    
    sendJSON([
        'success' => true,
        'message' => 'Thank you for contacting us. We will respond shortly.'
    ]);
    
} catch(PDOException $e) {
    sendJSON([
        'success' => false,
        'message' => DEBUG_MODE ? $e->getMessage() : 'Error submitting contact form'
    ], 500);
}
?>
