<?php
// test_laravel_working.php - Test if Laravel is working properly
echo "<h2>Testing Laravel Application</h2>";

try {
    require_once __DIR__ . '/vendor/autoload.php';
    $app = require_once __DIR__ . '/bootstrap/app.php';
    $kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

    echo "✓ Laravel application bootstrapped successfully<br>";

    // Test database connection
    $pdo = Illuminate\Support\Facades\DB::connection()->getPdo();
    echo "✓ Database connection successful<br>";

    // Test basic facades
    $cache = Illuminate\Support\Facades\Cache::store();
    echo "✓ Cache facade working<br>";

    echo "<br><strong>✅ Laravel is working properly!</strong><br>";
    echo "<a href='/pms/'>Go to Application</a><br>";
    echo "<a href='clear_cache.php'>Clear Caches</a><br>";
    echo "<a href='create_sessions_table.php'>Create Sessions Table</a><br>";

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "<br>";
    echo "File: " . $e->getFile() . ":" . $e->getLine() . "<br>";
}
?>