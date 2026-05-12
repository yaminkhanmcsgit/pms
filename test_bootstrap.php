<?php
// test_bootstrap.php - Test the Laravel bootstrap
echo "<h2>Testing Laravel Bootstrap</h2>";

try {
    require_once __DIR__ . '/bootstrap_laravel.php';
    $app = bootstrapLaravel();

    echo "✓ Laravel bootstrapped successfully<br>";

    // Test database connection (simple)
    $db = Illuminate\Support\Facades\DB::connection();
    echo "✓ Database facade initialized<br>";

    // Test cache facade (simple)
    $cache = Illuminate\Support\Facades\Cache::store();
    echo "✓ Cache facade initialized<br>";

    echo "<br><strong>Laravel bootstrap test completed successfully!</strong><br>";
    echo "<br><em>Note: This confirms facades are properly initialized for cache and database operations.</em><br>";

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "<br>";
    echo "File: " . $e->getFile() . ":" . $e->getLine() . "<br>";
}
?>