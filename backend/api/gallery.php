<?php
/**
 * Gallery API Endpoint
 * Returns gallery images, optionally filtered by category
 */

require_once '../config.php';

header('Content-Type: application/json');

try {
    $db = Database::getInstance()->getConnection();
    
    // Get category filter if provided
    $category = isset($_GET['category']) ? sanitize($_GET['category']) : null;
    
    if ($category) {
        $stmt = $db->prepare("
            SELECT id, title, description, image_path, category, display_order 
            FROM gallery 
            WHERE is_active = 1 AND category = ?
            ORDER BY display_order ASC, created_at DESC
        ");
        $stmt->execute([$category]);
    } else {
        $stmt = $db->prepare("
            SELECT id, title, description, image_path, category, display_order 
            FROM gallery 
            WHERE is_active = 1 
            ORDER BY display_order ASC, created_at DESC
        ");
        $stmt->execute();
    }
    
    $images = $stmt->fetchAll();
    
    // Add full image URLs
    foreach ($images as &$image) {
        $image['image_url'] = SITE_URL . '/' . $image['image_path'];
    }
    
    sendJSON([
        'success' => true,
        'count' => count($images),
        'category' => $category,
        'data' => $images
    ]);
    
} catch(PDOException $e) {
    sendJSON([
        'success' => false,
        'message' => DEBUG_MODE ? $e->getMessage() : 'Error fetching gallery'
    ], 500);
}
?>
