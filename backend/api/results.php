<?php
/**
 * Results API Endpoint
 * Returns examination results, filterable by program
 */

require_once '../config.php';

header('Content-Type: application/json');

try {
    $db = Database::getInstance()->getConnection();
    
    // Get filters
    $program = isset($_GET['program']) ? sanitize($_GET['program']) : null;
    $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 50;
    
    if ($program) {
        $stmt = $db->prepare("
            SELECT id, title, program, semester, exam_type, 
                   result_file, publish_date, downloads 
            FROM results 
            WHERE is_active = 1 
            AND program = ?
            ORDER BY publish_date DESC
            LIMIT ?
        ");
        $stmt->execute([$program, $limit]);
    } else {
        $stmt = $db->prepare("
            SELECT id, title, program, semester, exam_type, 
                   result_file, publish_date, downloads 
            FROM results 
            WHERE is_active = 1 
            ORDER BY publish_date DESC
            LIMIT ?
        ");
        $stmt->execute([$limit]);
    }
    
    $results = $stmt->fetchAll();
    
    // Add full URLs and formatted dates
    foreach ($results as &$result) {
        $result['result_url'] = SITE_URL . '/' . $result['result_file'];
        $result['publish_date_formatted'] = date('d M Y', strtotime($result['publish_date']));
    }
    
    sendJSON([
        'success' => true,
        'count' => count($results),
        'program' => $program,
        'data' => $results
    ]);
    
} catch(PDOException $e) {
    sendJSON([
        'success' => false,
        'message' => DEBUG_MODE ? $e->getMessage() : 'Error fetching results'
    ], 500);
}
?>
