<?php
/**
 * Banners API Endpoint
 * Returns all active banners for homepage slider
 */

require_once '../config.php';

header('Content-Type: application/json');

try {
    $db = Database::getInstance()->getConnection();
    
    // Get all active banners ordered by display_order
    $stmt = $db->prepare("
        SELECT id, title, description, image_path, link_url, display_order 
        FROM banners 
        WHERE is_active = 1 
        ORDER BY display_order ASC, created_at DESC
    ");
    
    $stmt->execute();
    $banners = $stmt->fetchAll();
    
    // Add full image URLs
    foreach ($banners as &$banner) {
        $banner['image_url'] = SITE_URL . '/' . $banner['image_path'];
    }
    
    sendJSON([
        'success' => true,
        'count' => count($banners),
        'data' => $banners
    ]);
    
} catch(PDOException $e) {
    sendJSON([
        'success' => false,
        'message' => DEBUG_MODE ? $e->getMessage() : 'Error fetching banners'
    ], 500);
}
?>
