<?php
// clear_cache.php - Clear Laravel caches with proper facade initialization
echo "<h2>Clearing Laravel Caches</h2>";

try {
    // Properly bootstrap Laravel with facade support
    require_once __DIR__ . '/bootstrap_laravel.php';
    $app = bootstrapLaravel();

    echo "✓ Laravel application fully bootstrapped<br>";

    // Clear caches using Laravel facades (now properly initialized)
    try {
        Illuminate\Support\Facades\Cache::flush();
        echo "✓ Application cache cleared<br>";
    } catch (Exception $e) {
        echo "⚠ Could not clear application cache: " . $e->getMessage() . "<br>";
    }

    // Clear config cache
    try {
        Illuminate\Support\Facades\Artisan::call('config:clear');
        echo "✓ Configuration cache cleared<br>";
    } catch (Exception $e) {
        echo "⚠ Could not clear config cache: " . $e->getMessage() . "<br>";
    }

    // Clear route cache
    try {
        Illuminate\Support\Facades\Artisan::call('route:clear');
        echo "✓ Route cache cleared<br>";
    } catch (Exception $e) {
        echo "⚠ Could not clear route cache: " . $e->getMessage() . "<br>";
    }

    // Clear view cache
    try {
        Illuminate\Support\Facades\Artisan::call('view:clear');
        echo "✓ View cache cleared<br>";
    } catch (Exception $e) {
        echo "⚠ Could not clear view cache: " . $e->getMessage() . "<br>";
    }

    echo "<br><strong>All cache clearing operations completed successfully!</strong><br>";
    echo "<a href='../'>Back to Application</a>";

} catch (Exception $e) {
    echo "❌ Fatal error: " . $e->getMessage() . "<br>";
    echo "File: " . $e->getFile() . ":" . $e->getLine() . "<br>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}
?>