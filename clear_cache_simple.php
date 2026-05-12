<?php
// clear_cache_simple.php - Simple cache clearing without facades
echo "<h2>Clearing Laravel Caches (Simple Version)</h2>";

// Define cache directories
$cacheDirectories = [
    'config' => __DIR__ . '/bootstrap/cache/config.php',
    'routes' => __DIR__ . '/bootstrap/cache/routes.php',
    'services' => __DIR__ . '/bootstrap/cache/services.php',
    'packages' => __DIR__ . '/bootstrap/cache/packages.php',
];

$viewCacheDir = __DIR__ . '/bootstrap/cache/views';

echo "Starting cache cleanup...<br><br>";

// Clear compiled cache files
foreach ($cacheDirectories as $type => $file) {
    if (file_exists($file)) {
        if (unlink($file)) {
            echo "✓ $type cache cleared<br>";
        } else {
            echo "❌ Failed to clear $type cache<br>";
        }
    } else {
        echo "✓ $type cache already clear<br>";
    }
}

// Clear view cache directory
if (is_dir($viewCacheDir)) {
    $viewFiles = glob($viewCacheDir . '/*.php');
    $cleared = 0;
    foreach ($viewFiles as $file) {
        if (is_file($file) && unlink($file)) {
            $cleared++;
        }
    }
    echo "✓ View cache cleared ($cleared files)<br>";
} else {
    echo "✓ View cache directory not found or already clear<br>";
}

// Clear storage/framework/cache directory
$storageCacheDir = __DIR__ . '/storage/framework/cache';
if (is_dir($storageCacheDir)) {
    $cacheFiles = glob($storageCacheDir . '/data/*/*/*');
    $cleared = 0;
    foreach ($cacheFiles as $file) {
        if (is_file($file) && unlink($file)) {
            $cleared++;
        }
    }
    echo "✓ Storage cache cleared ($cleared files)<br>";
} else {
    echo "✓ Storage cache directory not found<br>";
}

// Clear storage/framework/sessions directory (if using file sessions)
$sessionDir = __DIR__ . '/storage/framework/sessions';
if (is_dir($sessionDir)) {
    $sessionFiles = glob($sessionDir . '/*');
    $cleared = 0;
    foreach ($sessionFiles as $file) {
        if (is_file($file) && unlink($file)) {
            $cleared++;
        }
    }
    echo "✓ Session files cleared ($cleared files)<br>";
} else {
    echo "✓ Session directory not found<br>";
}

echo "<br><strong>Cache clearing completed successfully!</strong><br>";
echo "<br><em>Note: This script manually deletes cache files without using Laravel facades.</em><br>";
echo "<a href='../'>Back to Application</a>";
?>