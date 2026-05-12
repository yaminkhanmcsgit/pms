<?php
// complete_cleanup.php - Complete cleanup for cPanel deployment
echo "<h2>Complete Laravel Cleanup for cPanel</h2>";

// Step 1: Delete all cache files manually
$cacheFiles = [
    __DIR__ . '/bootstrap/cache/config.php',
    __DIR__ . '/bootstrap/cache/routes.php',
    __DIR__ . '/bootstrap/cache/services.php',
    __DIR__ . '/bootstrap/cache/packages.php',
];

echo "<h3>Step 1: Clearing Cache Files</h3>";
foreach ($cacheFiles as $file) {
    if (file_exists($file)) {
        if (unlink($file)) {
            echo "✓ Deleted: " . basename($file) . "<br>";
        } else {
            echo "❌ Failed to delete: " . basename($file) . "<br>";
        }
    } else {
        echo "✓ Already clear: " . basename($file) . "<br>";
    }
}

// Step 2: Clear view cache
$viewCacheDir = __DIR__ . '/bootstrap/cache/views';
if (is_dir($viewCacheDir)) {
    $viewFiles = glob($viewCacheDir . '/*.php');
    $cleared = 0;
    foreach ($viewFiles as $file) {
        if (is_file($file) && unlink($file)) {
            $cleared++;
        }
    }
    echo "✓ Cleared $cleared view cache files<br>";
} else {
    echo "✓ View cache directory not found<br>";
}

// Step 3: Clear storage caches
$storageDirs = [
    __DIR__ . '/storage/framework/cache',
    __DIR__ . '/storage/framework/sessions',
    __DIR__ . '/storage/framework/views',
];

echo "<h3>Step 2: Clearing Storage Cache</h3>";
foreach ($storageDirs as $dir) {
    if (is_dir($dir)) {
        $files = glob($dir . '/*/*/*'); // For cache
        $files = array_merge($files, glob($dir . '/*')); // For sessions/views
        $cleared = 0;
        foreach ($files as $file) {
            if (is_file($file) && unlink($file)) {
                $cleared++;
            }
        }
        echo "✓ Cleared $cleared files from " . basename($dir) . "<br>";
    } else {
        echo "✓ Directory not found: " . basename($dir) . "<br>";
    }
}

echo "<h3>Step 3: Next Steps</h3>";
echo "✅ Cache cleanup completed!<br><br>";
echo "<strong>NOW YOU MUST:</strong><br>";
echo "1. Delete the entire <code>vendor</code> folder from cPanel<br>";
echo "2. Upload the new <code>vendor_production.zip</code><br>";
echo "3. Extract it to recreate the <code>vendor</code> folder<br>";
echo "4. Delete <code>vendor_production.zip</code><br>";
echo "5. Visit your site again<br><br>";

echo "<em>The new vendor directory contains NO Ignition dependencies.</em><br>";
echo "<a href='../'>Test Application</a> (after completing steps above)";
?>