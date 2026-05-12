<?php
// create_sessions_table.php - Manually create sessions table
echo "<h2>Creating Sessions Table</h2>";

try {
    // Bootstrap Laravel application
    require_once __DIR__ . '/vendor/autoload.php';
    $app = require_once __DIR__ . '/bootstrap/app.php';
    $kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

    echo "✓ Laravel application bootstrapped<br>";

    // Test database connection
    try {
        $pdo = Illuminate\Support\Facades\DB::connection()->getPdo();
        echo "✓ Database connection successful<br>";
    } catch (Exception $e) {
        echo "❌ Database connection failed: " . $e->getMessage() . "<br>";
        echo "Please check your database credentials in .env file<br>";
        exit;
    }

    // Check if sessions table already exists
    $tableExists = Illuminate\Support\Facades\Schema::hasTable('sessions');

    if ($tableExists) {
        echo "✓ Sessions table already exists<br>";
    } else {
        echo "Creating sessions table...<br>";

        // Create the sessions table manually
        Illuminate\Support\Facades\Schema::create('sessions', function ($table) {
            $table->string('id')->primary();
            $table->foreignId('operator_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->text('payload');
            $table->integer('last_activity')->index();
        });

        echo "✓ Sessions table created successfully!<br>";
    }

    echo "<br><strong>Session table setup completed!</strong><br>";
    echo "<a href='../'>Back to Application</a>";

} catch (Exception $e) {
    echo "❌ Fatal error: " . $e->getMessage() . "<br>";
    echo "File: " . $e->getFile() . ":" . $e->getLine() . "<br>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}
?>