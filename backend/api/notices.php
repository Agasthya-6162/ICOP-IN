<?php
/**
 * Notices API Endpoint
 * Returns active notices, auto-filters expired ones
 */

require_once dirname(__DIR__) . '/config.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

try {
    $db = Database::getInstance()->getConnection();
    
    // Get category filter if provided
    $category = isset($_GET['category']) ? sanitize($_GET['category']) : null;
    $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 50;
    
    $today = date('Y-m-d');
    
    if ($category) {
        $stmt = $db->prepare("
            SELECT id, title, content, category, attachment_path, 
                   is_important, publish_date, expiry_date, views 
            FROM notices 
            WHERE is_active = 1 
            AND publish_date <= ? 
            AND (expiry_date IS NULL OR expiry_date >= ?)
            AND category = ?
            ORDER BY is_important DESC, publish_date DESC
            LIMIT ?
        ");
        $stmt->execute([$today, $today, $category, $limit]);
    } else {
        $stmt = $db->prepare("
            SELECT id, title, content, category, attachment_path, 
                   is_important, publish_date, expiry_date, views 
            FROM notices 
            WHERE is_active = 1 
            AND publish_date <= ? 
            AND (expiry_date IS NULL OR expiry_date >= ?)
            ORDER BY is_important DESC, publish_date DESC
            LIMIT ?
        ");
        $stmt->execute([$today, $today, $limit]);
    }
    
    $notices = $stmt->fetchAll();
    
    // Add full URLs for attachments
    foreach ($notices as &$notice) {
        if ($notice['attachment_path']) {
            $notice['attachment_url'] = SITE_URL . '/' . $notice['attachment_path'];
        }
        $notice['publish_date_formatted'] = date('d M Y', strtotime($notice['publish_date']));
    }
    
    sendJSON([
        'success' => true,
        'count' => count($notices),
        'category' => $category,
        'data' => $notices
    ]);
    
} catch(PDOException $e) {
    sendJSON([
        'success' => false,
        'message' => DEBUG_MODE ? $e->getMessage() : 'Error fetching notices'
    ], 500);
}
?>
