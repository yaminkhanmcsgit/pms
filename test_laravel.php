<?php
// test_laravel.php - Test Laravel components
require __DIR__ . '/vendor/autoload.php';

try {
    echo "1. Autoloader: OK\n";

    $app = require_once __DIR__ . '/bootstrap/app.php';
    echo "2. App bootstrap: OK\n";

    // Test database connection
    $kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
    Illuminate\Support\Facades\DB::connection()->getPdo();
    echo "3. Database connection: OK\n";

    // Test session configuration
    $sessionConfig = config('session.driver');
    echo "4. Session driver: $sessionConfig\n";

    echo "All tests passed!\n";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . ":" . $e->getLine() . "\n";
}
?>