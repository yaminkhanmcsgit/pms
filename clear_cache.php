<?php
// clear_cache.php - Clear Laravel caches without exec()
echo "<h2>Clearing Laravel Caches</h2>";

try {
    // Bootstrap Laravel application
    require_once __DIR__ . '/vendor/autoload.php';
    $app = require_once __DIR__ . '/bootstrap/app.php';
    $kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

    echo "✓ Laravel application bootstrapped<br>";

    // Clear caches using Laravel's Cache facade
    try {
        Illuminate\Support\Facades\Cache::flush();
        echo "✓ Application cache cleared<br>";
    } catch (Exception $e) {
        echo "⚠ Could not clear application cache: " . $e->getMessage() . "<br>";
    }

    // Clear config cache by removing the file
    $configCachePath = __DIR__ . '/bootstrap/cache/config.php';
    if (file_exists($configCachePath)) {
        unlink($configCachePath);
        echo "✓ Configuration cache cleared<br>";
    } else {
        echo "✓ Configuration cache already clear<br>";
    }

    // Clear route cache
    $routeCachePath = __DIR__ . '/bootstrap/cache/routes.php';
    if (file_exists($routeCachePath)) {
        unlink($routeCachePath);
        echo "✓ Route cache cleared<br>";
    } else {
        echo "✓ Route cache already clear<br>";
    }

    // Clear view cache
    $viewCachePath = __DIR__ . '/bootstrap/cache/views';
    if (is_dir($viewCachePath)) {
        $files = glob($viewCachePath . '/*');
        foreach ($files as $file) {
            if (is_file($file)) {
                unlink($file);
            }
        }
        echo "✓ View cache cleared<br>";
    } else {
        echo "✓ View cache already clear<br>";
    }

    echo "<br><strong>Cache clearing completed successfully!</strong><br>";
    echo "<a href='../'>Back to Application</a>";

} catch (Exception $e) {
    echo "❌ Fatal error: " . $e->getMessage() . "<br>";
    echo "File: " . $e->getFile() . ":" . $e->getLine() . "<br>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}
?>