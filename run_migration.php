<?php
require_once __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

echo "<h2>Running Sessions Migration</h2>";
try {
    $migrationPath = __DIR__ . '/database/migrations/2026_05_07_070436_create_sessions_table.php';
    if (file_exists($migrationPath)) {
        echo "✓ Migration file found<br>";
        exec("php artisan migrate --path=database/migrations/2026_05_07_070436_create_sessions_table.php 2>&1", $output, $exitCode);
        if ($exitCode === 0) {
            echo "✓ Migration completed successfully!<br>";
            echo "<pre>" . implode("\n", $output) . "</pre>";
        } else {
            echo "❌ Migration failed with exit code: $exitCode<br>";
            echo "<pre>" . implode("\n", $output) . "</pre>";
        }
    } else {
        echo "❌ Migration file not found<br>";
    }
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "<br>";
}
echo "<br><a href='../'>Back to Application</a>";
?>