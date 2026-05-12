<?php
// run_migrations.php - Run Laravel migrations without exec()
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

    // Get migration repository
    $repository = $app->make('migrator');
    $files = $repository->getMigrationFiles(__DIR__ . '/database/migrations');

    echo "Found " . count($files) . " migration files<br>";

    // Run specific sessions migration if it exists
    $sessionsMigration = '2026_05_07_070436_create_sessions_table';
    if (isset($files[$sessionsMigration])) {
        echo "Running sessions migration...<br>";
        try {
            $repository->run([__DIR__ . '/database/migrations/' . $files[$sessionsMigration]]);
            echo "✓ Sessions migration completed successfully!<br>";
        } catch (Exception $e) {
            echo "❌ Sessions migration failed: " . $e->getMessage() . "<br>";
        }
    } else {
        echo "⚠ Sessions migration not found<br>";
    }

    // Check migration status
    echo "<br><h3>Migration Status</h3>";
    $ran = $repository->getRepository()->getRan();
    echo "Ran migrations: " . count($ran) . "<br>";
    if (!empty($ran)) {
        echo "<ul>";
        foreach ($ran as $migration) {
            echo "<li>$migration</li>";
        }
        echo "</ul>";
    }

    // Get pending migrations
    $pending = $repository->getPendingMigrations($files, $ran);
    if (!empty($pending)) {
        echo "<br>Pending migrations: " . count($pending) . "<br>";
        echo "<ul>";
        foreach ($pending as $migration) {
            echo "<li>$migration</li>";
        }
        echo "</ul>";
    } else {
        echo "<br>✓ All migrations are up to date!<br>";
    }

    echo "<br><strong>Migration check completed!</strong><br>";
    echo "<a href='../'>Back to Application</a>";

} catch (Exception $e) {
    echo "❌ Fatal error: " . $e->getMessage() . "<br>";
    echo "File: " . $e->getFile() . ":" . $e->getLine() . "<br>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}
?>