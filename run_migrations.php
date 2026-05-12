<?php
// run_migrations.php - Properly run Laravel migrations on cPanel
echo "<h2>Running Laravel Migrations</h2>";

try {
    // Bootstrap Laravel application
    require_once __DIR__ . '/vendor/autoload.php';
    $app = require_once __DIR__ . '/bootstrap/app.php';
    $kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

    echo "✓ Laravel application bootstrapped<br>";

    // Test database connection
    try {
        Illuminate\Support\Facades\DB::connection()->getPdo();
        echo "✓ Database connection successful<br>";
    } catch (Exception $e) {
        echo "❌ Database connection failed: " . $e->getMessage() . "<br>";
        echo "Please check your database credentials in .env file<br>";
        exit;
    }

    // Run specific sessions migration
    $migrationPath = __DIR__ . '/database/migrations/2026_05_07_070436_create_sessions_table.php';

    if (file_exists($migrationPath)) {
        echo "✓ Sessions migration file found<br>";

        exec("php artisan migrate --path=database/migrations/2026_05_07_070436_create_sessions_table.php 2>&1", $output, $exitCode);

        if ($exitCode === 0) {
            echo "✓ Sessions migration completed successfully!<br>";
            echo "<pre>" . implode("\n", $output) . "</pre>";
        } else {
            echo "❌ Migration failed with exit code: $exitCode<br>";
            echo "<pre>" . implode("\n", $output) . "</pre>";
        }
    } else {
        echo "❌ Migration file not found: $migrationPath<br>";
        echo "Please ensure the migration file exists<br>";
    }

    // Optional: Run all pending migrations
    echo "<br><h3>Running All Pending Migrations</h3>";
    exec("php artisan migrate 2>&1", $output, $exitCode);

    if ($exitCode === 0) {
        echo "✓ All migrations completed successfully!<br>";
        echo "<pre>" . implode("\n", $output) . "</pre>";
    } else {
        echo "⚠ Some migrations may have failed (exit code: $exitCode)<br>";
        echo "<pre>" . implode("\n", $output) . "</pre>";
    }

    echo "<br><strong>Migration operations completed!</strong><br>";
    echo "<a href='../'>Back to Application</a>";

} catch (Exception $e) {
    echo "❌ Fatal error: " . $e->getMessage() . "<br>";
    echo "File: " . $e->getFile() . ":" . $e->getLine() . "<br>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}
?>