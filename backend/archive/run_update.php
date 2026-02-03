<?php
require_once 'backend/config.php';

try {
    $db = Database::getInstance()->getConnection();
    $sql = file_get_contents('backend/database/update_schema.sql');
    
    // Split by semicolon to run individual queries (basic approach)
    $queries = explode(';', $sql);
    
    foreach ($queries as $query) {
        $query = trim($query);
        if (!empty($query)) {
            $db->exec($query);
            echo "Executed: " . substr($query, 0, 50) . "...\n";
        }
    }
    
    echo "Schema update completed successfully.";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
