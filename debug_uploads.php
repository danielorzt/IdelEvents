<?php
// This script helps verify upload directory permissions and test image uploads

// Check if session is started
session_start();

// Set error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Image Upload Debugging Tool</h1>";

// Check uploads directory
$uploadsDir = __DIR__ . '/uploads';
$eventosDir = $uploadsDir . '/eventos';
$currentYearDir = $eventosDir . '/' . date('Y');
$currentMonthDir = $currentYearDir . '/' . date('m');

echo "<h2>Directory Structure Check</h2>";
echo "<ul>";

// Check main uploads directory
if (file_exists($uploadsDir)) {
    echo "<li>✅ Main uploads directory exists: $uploadsDir</li>";
    echo "<li>Permissions: " . substr(sprintf('%o', fileperms($uploadsDir)), -4) . "</li>";

    // Check if writable
    if (is_writable($uploadsDir)) {
        echo "<li>✅ Main uploads directory is writable</li>";
    } else {
        echo "<li>❌ Main uploads directory is NOT writable</li>";
    }
} else {
    echo "<li>❌ Main uploads directory does not exist: $uploadsDir</li>";

    // Try to create it
    if (mkdir($uploadsDir, 0755, true)) {
        echo "<li>✅ Created main uploads directory</li>";
    } else {
        echo "<li>❌ Failed to create main uploads directory</li>";
    }
}

// Check eventos directory
if (file_exists($eventosDir)) {
    echo "<li>✅ Eventos directory exists: $eventosDir</li>";
} else {
    echo "<li>❌ Eventos directory does not exist: $eventosDir</li>";

    // Try to create it
    if (mkdir($eventosDir, 0755, true)) {
        echo "<li>✅ Created eventos directory</li>";
    } else {
        echo "<li>❌ Failed to create eventos directory</li>";
    }
}

// Check year directory
if (file_exists($currentYearDir)) {
    echo "<li>✅ Current year directory exists: $currentYearDir</li>";
} else {
    echo "<li>❌ Current year directory does not exist: $currentYearDir</li>";

    // Try to create it
    if (mkdir($currentYearDir, 0755, true)) {
        echo "<li>✅ Created current year directory</li>";
    } else {
        echo "<li>❌ Failed to create current year directory</li>";
    }
}

// Check month directory
if (file_exists($currentMonthDir)) {
    echo "<li>✅ Current month directory exists: $currentMonthDir</li>";
} else {
    echo "<li>❌ Current month directory does not exist: $currentMonthDir</li>";

    // Try to create it
    if (mkdir($currentMonthDir, 0755, true)) {
        echo "<li>✅ Created current month directory</li>";
    } else {
        echo "<li>❌ Failed to create current month directory</li>";
    }
}

echo "</ul>";

// Show upload form for testing
echo "<h2>Test Image Upload</h2>";
echo "<form action='debug_uploads.php' method='POST' enctype='multipart/form-data'>";
echo "<input type='file' name='test_image' accept='image/*'>";
echo "<button type='submit'>Test Upload</button>";
echo "</form>";

// Process test upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['test_image']) && $_FILES['test_image']['error'] === UPLOAD_ERR_OK) {
    $file = $_FILES['test_image'];

    echo "<h3>Upload Results</h3>";
    echo "<ul>";
    echo "<li>File name: " . htmlspecialchars($file['name']) . "</li>";
    echo "<li>File type: " . htmlspecialchars($file['type']) . "</li>";
    echo "<li>File size: " . htmlspecialchars($file['size']) . " bytes</li>";
    echo "<li>Temporary path: " . htmlspecialchars($file['tmp_name']) . "</li>";

    // Generate target path
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = uniqid('test_', true) . '.' . $extension;
    $targetPath = $currentMonthDir . '/' . $filename;

    // Try to move the file
    if (move_uploaded_file($file['tmp_name'], $targetPath)) {
        echo "<li>✅ Upload successful!</li>";
        echo "<li>Saved to: " . htmlspecialchars($targetPath) . "</li>";
        echo "<li>Relative path: uploads/eventos/" . date('Y') . "/" . date('m') . "/" . $filename . "</li>";
        echo "<li><img src='uploads/eventos/" . date('Y') . "/" . date('m') . "/" . $filename . "' style='max-width: 300px;'></li>";
    } else {
        echo "<li>❌ Upload failed!</li>";
        echo "<li>Error: Unable to move uploaded file to target location</li>";
    }

    echo "</ul>";
}

// Display PHP configuration
echo "<h2>PHP Configuration</h2>";
echo "<ul>";
echo "<li>post_max_size: " . ini_get('post_max_size') . "</li>";
echo "<li>upload_max_filesize: " . ini_get('upload_max_filesize') . "</li>";
echo "<li>max_file_uploads: " . ini_get('max_file_uploads') . "</li>";
echo "<li>memory_limit: " . ini_get('memory_limit') . "</li>";
echo "<li>max_execution_time: " . ini_get('max_execution_time') . " seconds</li>";
echo "</ul>";

// Display error log if available
$errorLog = error_get_last();
if ($errorLog) {
    echo "<h2>Last PHP Error</h2>";
    echo "<pre>";
    print_r($errorLog);
    echo "</pre>";
}

echo "<p><a href='views/admin/eventos.php'>Return to Events Management</a></p>";
?>