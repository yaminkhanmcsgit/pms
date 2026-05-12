<?php
// run_migrations.php - Run Laravel migrations with proper facade initialization
echo "<h2>Running Laravel Migrations</h2>";

try {
    // Properly bootstrap Laravel with facade support
    require_once __DIR__ . '/bootstrap_laravel.php';
    $app = bootstrapLaravel();

    echo "✓ Laravel application fully bootstrapped<br>";

    // Test database connection
    try {
        Illuminate\Support\Facades\DB::connection()->getPdo();
        echo "✓ Database connection successful<br>";
    } catch (Exception $e) {
        echo "❌ Database connection failed: " . $e->getMessage() . "<br>";
        echo "Please check your database credentials in .env file<br>";
        exit;
    }

    // Get migration repository and run pending migrations
    try {
        $migrator = $app->make('migrator');
        $migrator->run([$app->databasePath() . DIRECTORY_SEPARATOR . 'migrations']);

        echo "✓ Migrations executed successfully<br>";
    } catch (Exception $e) {
        echo "⚠ Migration error: " . $e->getMessage() . "<br>";
    }

    // Check migration status
    echo "<br><h3>Migration Status</h3>";
    try {
        $repository = $app->make('migrator')->getRepository();
        $ran = $repository->getRan();

        echo "Completed migrations: " . count($ran) . "<br>";
        if (!empty($ran)) {
            echo "<ul>";
            foreach (array_slice($ran, -5) as $migration) { // Show last 5
                echo "<li>$migration</li>";
            }
            if (count($ran) > 5) {
                echo "<li>... and " . (count($ran) - 5) . " more</li>";
            }
            echo "</ul>";
        }
    } catch (Exception $e) {
        echo "⚠ Could not check migration status: " . $e->getMessage() . "<br>";
    }

    echo "<br><strong>Migration operations completed!</strong><br>";
    echo "<a href='../'>Back to Application</a>";

} catch (Exception $e) {
    echo "❌ Fatal error: " . $e->getMessage() . "<br>";
    echo "File: " . $e->getFile() . ":" . $e->getLine() . "<br>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}
?>