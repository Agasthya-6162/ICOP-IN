<?php
/**
 * Database Setup Script for ICOP Website
 * Run this file once to create the database and all tables
 */

// Database credentials
$host = 'localhost';
$username = 'root';
$password = '';
$database = 'icop_website';

// Step 1: Connect to MySQL (without database)
try {
    $conn = new mysqli($host, $username, $password);
    
    if ($conn->connect_error) {
        die("❌ Connection failed: " . $conn->connect_error);
    }
    
    echo "✅ Connected to MySQL successfully!<br><br>";
    
    // Step 2: Create database if it doesn't exist
    $sql = "CREATE DATABASE IF NOT EXISTS $database CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci";
    if ($conn->query($sql) === TRUE) {
        echo "✅ Database '$database' created or already exists<br><br>";
    } else {
        die("❌ Error creating database: " . $conn->error);
    }
    
    // Step 3: Select the database
    $conn->select_db($database);
    
    // Step 4: Read and execute SQL file
    $sqlFile = __DIR__ . '/database/icop_database.sql';
    
    if (!file_exists($sqlFile)) {
        die("❌ SQL file not found: $sqlFile");
    }
    
    $sql = file_get_contents($sqlFile);
    
    // Remove CREATE DATABASE and USE statements as we've already done that
    $sql = preg_replace('/CREATE DATABASE.*?;\s*/i', '', $sql);
    $sql = preg_replace('/USE.*?;\s*/i', '', $sql);
    
    // Split into individual queries
    $queries = array_filter(array_map('trim', explode(';', $sql)));
    
    echo "📋 Executing SQL queries...<br><br>";
    
    $successCount = 0;
    $errorCount = 0;
    
    foreach ($queries as $query) {
        if (empty($query) || strpos($query, '--') === 0) {
            continue;
        }
        
        if ($conn->query($query) === TRUE) {
            $successCount++;
        } else {
            $errorCount++;
            echo "⚠️  Query error: " . $conn->error . "<br>";
        }
    }
    
    echo "<br>✅ Executed $successCount queries successfully<br>";
    if ($errorCount > 0) {
        echo "⚠️  $errorCount queries had errors (might be expected for duplicates)<br>";
    }
    
    echo "<br><hr><br>";
    echo "<h2>🎉 Database Setup Complete!</h2>";
    echo "<h3>Database Information:</h3>";
    echo "<ul>";
    echo "<li><strong>Database Name:</strong> $database</li>";
    echo "<li><strong>Username:</strong> $username</li>";
    echo "<li><strong>Password:</strong> " . (empty($password) ? '(empty)' : '***') . "</li>";
    echo "</ul>";
    
    echo "<h3>Admin Login Credentials:</h3>";
    echo "<ul>";
    echo "<li><strong>URL:</strong> <a href='../admin/'>admin/index.php</a></li>";
    echo "<li><strong>Username:</strong> admin</li>";
    echo "<li><strong>Password:</strong> admin123</li>";
    echo "</ul>";
    
    echo "<h3>⚠️ Important:</h3>";
    echo "<ul>";
    echo "<li>Change the default admin password after first login!</li>";
    echo "<li>Delete or secure this setup file (db_setup.php) after running</li>";
    echo "</ul>";
    
    echo "<br><a href='../admin/' style='display:inline-block; padding:10px 20px; background:#667eea; color:white; text-decoration:none; border-radius:5px;'>Go to Admin Panel</a>";
    
    $conn->close();
    
} catch (Exception $e) {
    die("❌ Error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Database Setup - ICOP</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
            background: #f5f5f5;
        }
        h2 { color: #333; }
        h3 { color: #555; margin-top: 20px; }
        ul { background: white; padding: 20px; border-radius: 5px; }
        li { margin: 10px 0; }
    </style>
</head>
<body>
</body>
</html>
