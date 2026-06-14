<?php
require 'connection.php';

echo "=== FORMS DATABASE CHECK ===\n\n";

$r = mysqli_query($link, 'SELECT * FROM form_downloads');
if (!$r) {
    echo "Query failed: " . mysqli_error($link);
    exit;
}

$count = mysqli_num_rows($r);
echo "Total forms in database: $count\n\n";

if ($count == 0) {
    echo "No forms found. Add one via Admin > Content Manager > Forms Download\n";
} else {
    while ($row = mysqli_fetch_assoc($r)) {
        echo "Form ID: " . $row['id'] . "\n";
        echo "  Title: " . $row['title'] . "\n";
        echo "  Path: " . $row['file_path'] . "\n";
        echo "  Active: " . ($row['is_active'] ? 'Yes' : 'No') . "\n";
        
        // Check if file exists
        if (strpos($row['file_path'], 'http') === 0) {
            echo "  Type: External URL\n";
            echo "  Valid: Yes (URL)\n";
        } else {
            $file_exists = is_file($row['file_path']);
            echo "  Type: Local File\n";
            echo "  File exists: " . ($file_exists ? 'Yes' : 'No') . "\n";
            if (!$file_exists) {
                echo "  Expected path: " . realpath('.') . '/' . $row['file_path'] . "\n";
            }
        }
        echo "\n";
    }
}

echo "=== END CHECK ===\n";
?>
