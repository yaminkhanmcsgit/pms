<?php
// test_vendor.php - Test if vendor directory is working correctly
echo "Testing vendor directory...<br>";

try {
    // Test if autoloader works
    require_once __DIR__ . '/vendor/autoload.php';
    echo "✓ Autoloader loaded successfully<br>";

    // Test Laravel framework loading
    $app = require_once __DIR__ . '/bootstrap/app.php';
    echo "✓ Laravel app bootstrapped successfully<br>";

    // Test database connection
    $kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
    echo "✓ Kernel created successfully<br>";

    echo "<br><strong>All tests passed! Vendor directory is working correctly.</strong>";

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "<br>";
    echo "File: " . $e->getFile() . ":" . $e->getLine() . "<br>";
}
?>