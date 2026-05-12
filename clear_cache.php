<?php
// clear_cache.php - Properly clear Laravel caches on cPanel
echo "<h2>Clearing Laravel Caches</h2>";

try {
    // Bootstrap Laravel application
    require_once __DIR__ . '/vendor/autoload.php';
    $app = require_once __DIR__ . '/bootstrap/app.php';
    $kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

    echo "✓ Laravel application bootstrapped<br>";

    // Clear various caches
    $commands = [
        'cache:clear' => 'Application cache',
        'config:clear' => 'Configuration cache',
        'route:clear' => 'Route cache',
        'view:clear' => 'View cache'
    ];

    foreach ($commands as $command => $description) {
        try {
            exec("php artisan $command 2>&1", $output, $exitCode);
            if ($exitCode === 0) {
                echo "✓ $description cleared successfully<br>";
            } else {
                echo "⚠ Warning: $description may not have been cleared (exit code: $exitCode)<br>";
                if (!empty($output)) {
                    echo "<pre>" . implode("\n", $output) . "</pre>";
                }
            }
        } catch (Exception $e) {
            echo "⚠ Error clearing $description: " . $e->getMessage() . "<br>";
        }
    }

    echo "<br><strong>All cache clearing operations completed!</strong><br>";
    echo "<a href='../'>Back to Application</a>";

} catch (Exception $e) {
    echo "❌ Fatal error: " . $e->getMessage() . "<br>";
    echo "File: " . $e->getFile() . ":" . $e->getLine() . "<br>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}
?>