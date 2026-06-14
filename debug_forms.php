<?php
require 'connection.php';

echo "=== FORM UPLOAD DEBUG ===\n\n";

// Check database
echo "DATABASE FORMS:\n";
$r = mysqli_query($link, 'SELECT * FROM form_downloads ORDER BY id DESC');
if ($r && mysqli_num_rows($r) > 0) {
    while ($row = mysqli_fetch_assoc($r)) {
        echo "  ID: " . $row['id'] . " | Title: " . $row['title'] . "\n";
        echo "    Path: " . $row['file_path'] . "\n";
        echo "    Active: " . ($row['is_active'] ? 'Yes' : 'No') . "\n";
        
        if (strpos($row['file_path'], 'http') === 0) {
            echo "    Type: URL\n";
        } else {
            $exists = is_file($row['file_path']);
            echo "    Type: Local File\n";
            echo "    File exists: " . ($exists ? 'YES ✓' : 'NO ✗') . "\n";
        }
        echo "\n";
    }
} else {
    echo "  No forms in database\n";
}

// Check file system
echo "\nFILES IN /forms/ FOLDER:\n";
$files = glob('forms/*');
if (!empty($files)) {
    foreach ($files as $file) {
        $size = filesize($file);
        echo "  ✓ " . basename($file) . " (" . round($size/1024, 2) . " KB)\n";
    }
} else {
    echo "  No files in forms folder\n";
}

echo "\nFORMS FOLDER PERMISSIONS:\n";
echo "  Path: " . realpath('forms') . "\n";
echo "  Writable: " . (is_writable('forms') ? 'YES ✓' : 'NO ✗') . "\n";

echo "\n=== END DEBUG ===\n";
?>
