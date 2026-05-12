<?php
// Simple test to verify Laravel setup
echo "<h2>Laravel Setup Test</h2>";

// Test 1: Check if vendor autoloader works
try {
    require_once __DIR__ . '/vendor/autoload.php';
    echo "✓ Vendor autoloader loaded successfully<br>";
} catch (Exception $e) {
    echo "❌ Vendor autoloader failed: " . $e->getMessage() . "<br>";
}

// Test 2: Check if app can bootstrap
try {
    $app = require_once __DIR__ . '/bootstrap/app.php';
    echo "✓ App bootstrapped successfully<br>";
} catch (Exception $e) {
    echo "❌ App bootstrap failed: " . $e->getMessage() . "<br>";
}

// Test 3: Check database connection
try {
    $app = require_once __DIR__ . '/bootstrap/app.php';
    $kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
    Illuminate\Support\Facades\DB::connection()->getPdo();
    echo "✓ Database connection successful<br>";
} catch (Exception $e) {
    echo "❌ Database connection failed: " . $e->getMessage() . "<br>";
}

echo "<br><a href='/pms/'>Go to Laravel Application</a>";
?>