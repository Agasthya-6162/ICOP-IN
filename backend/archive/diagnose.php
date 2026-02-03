<?php
/**
 * Error Diagnostic Page
 * Use this to find what's causing the 500 error
 */

// Turn on all error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

echo "<h1>🔍 Error Diagnostic Page</h1>";
echo "<style>
body { font-family: Arial, sans-serif; padding: 20px; background: #f5f5f5; }
.success { color: green; background: #d4edda; padding: 10px; margin: 10px 0; border-radius: 5px; }
.error { color: red; background: #f8d7da; padding: 10px; margin: 10px 0; border-radius: 5px; }
.info { color: #004085; background: #d1ecf1; padding: 10px; margin: 10px 0; border-radius: 5px; }
h2 { color: #333; margin-top: 20px; }
code { background: #e9ecef; padding: 2px 5px; border-radius: 3px; }
</style>";

// Test 1: PHP Version
echo "<h2>Test 1: PHP Version</h2>";
if (version_compare(PHP_VERSION, '7.4.0', '>=')) {
    echo "<div class='success'>✅ PHP Version: " . PHP_VERSION . " (Good)</div>";
} else {
    echo "<div class='error'>❌ PHP Version: " . PHP_VERSION . " (Needs 7.4+)</div>";
}

// Test 2: Required Extensions
echo "<h2>Test 2: Required PHP Extensions</h2>";
$required_extensions = ['pdo', 'pdo_mysql', 'mysqli', 'mbstring', 'json'];
foreach ($required_extensions as $ext) {
    if (extension_loaded($ext)) {
        echo "<div class='success'>✅ $ext extension loaded</div>";
    } else {
        echo "<div class='error'>❌ $ext extension NOT loaded</div>";
    }
}

// Test 3: Backend Config
echo "<h2>Test 3: Backend Configuration</h2>";
$backend_config = __DIR__ . '/backend/config.php';
if (file_exists($backend_config)) {
    echo "<div class='success'>✅ backend/config.php exists</div>";
    
    // Try to include it
    try {
        require_once $backend_config;
        echo "<div class='success'>✅ backend/config.php loaded successfully</div>";
        
        // Check if constants are defined
        $constants = ['DB_HOST', 'DB_NAME', 'DB_USER', 'DB_PASS', 'SITE_URL'];
        foreach ($constants as $const) {
            if (defined($const)) {
                echo "<div class='success'>✅ Constant $const defined</div>";
            } else {
                echo "<div class='error'>❌ Constant $const NOT defined</div>";
            }
        }
        
    } catch (Exception $e) {
        echo "<div class='error'>❌ Error loading backend/config.php: " . $e->getMessage() . "</div>";
    }
} else {
    echo "<div class='error'>❌ backend/config.php NOT found at: $backend_config</div>";
}

// Test 4: Database Connection
echo "<h2>Test 4: Database Connection</h2>";
try {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    if ($conn->connect_error) {
        echo "<div class='error'>❌ Database connection failed: " . $conn->connect_error . "</div>";
    } else {
        echo "<div class='success'>✅ Database connected successfully!</div>";
        $conn->close();
    }
} catch (Exception $e) {
    echo "<div class='error'>❌ Database error: " . $e->getMessage() . "</div>";
    echo "<div class='info'>💡 Make sure MySQL is running in XAMPP and database 'icop_website' exists</div>";
}

// Test 5: Admin Config
echo "<h2>Test 5: Admin Configuration</h2>";
$admin_config = __DIR__ . '/admin/config.php';
if (file_exists($admin_config)) {
    echo "<div class='success'>✅ admin/config.php exists</div>";
} else {
    echo "<div class='error'>❌ admin/config.php NOT found</div>";
}

// Test 6: File Permissions
echo "<h2>Test 6: Upload Directories</h2>";
$upload_dirs = [
    __DIR__ . '/uploads',
    __DIR__ . '/uploads/banners',
    __DIR__ . '/uploads/gallery',
    __DIR__ . '/uploads/notices',
    __DIR__ . '/uploads/results'
];

foreach ($upload_dirs as $dir) {
    if (is_dir($dir)) {
        if (is_writable($dir)) {
            echo "<div class='success'>✅ $dir - Writable</div>";
        } else {
            echo "<div class='error'>❌ $dir - NOT writable</div>";
        }
    } else {
        echo "<div class='info'>ℹ️ $dir - Will be created automatically</div>";
    }
}

// Test 7: Session
echo "<h2>Test 7: Session Support</h2>";
if (session_status() === PHP_SESSION_NONE) {
    if (@session_start()) {
        echo "<div class='success'>✅ Session started successfully</div>";
    } else {
        echo "<div class='error'>❌ Cannot start session</div>";
    }
} else {
    echo "<div class='success'>✅ Session already active</div>";
}

echo "<br><hr><br>";
echo "<h2>🎯 Suggested Actions:</h2>";
echo "<div class='info'>";
echo "<ol>";
echo "<li>If all tests passed: <a href='index.html'>Visit Homepage</a> or <a href='admin/'>Admin Panel</a></li>";
echo "<li>If database connection failed: <a href='backend/db_setup.php'>Setup Database</a></li>";
echo "<li>If PHP extensions missing: Enable them in php.ini (XAMPP Control → Config → PHP)</li>";
echo "<li>Check Apache error log: C:\\xampp\\apache\\logs\\error.log</li>";
echo "</ol>";
echo "</div>";

echo "<br><div class='info'>";
echo "<strong>Quick Links:</strong><br>";
echo "• <a href='test.php'>Simple PHP Test</a><br>";
echo "• <a href='backend/db_test.php'>Database Connection Test</a><br>";
echo "• <a href='backend/db_setup.php'>Database Setup</a><br>";
echo "• <a href='admin/'>Admin Login</a><br>";
echo "</div>";
?>
