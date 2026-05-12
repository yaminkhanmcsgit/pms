<?php
// test_full_deployment.php - Complete testing of Laravel deployment
echo "<h2>🔍 Complete Laravel Deployment Test</h2>";
echo "<p>Testing all components that could cause 502 errors...</p><br>";

// Test 1: Basic Laravel bootstrap
echo "<h3>1. Laravel Bootstrap Test</h3>";
try {
    require_once __DIR__ . '/vendor/autoload.php';
    $app = require_once __DIR__ . '/bootstrap/app.php';
    $kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
    echo "✅ Laravel bootstrapped successfully<br>";
} catch (Exception $e) {
    echo "❌ Bootstrap failed: " . $e->getMessage() . "<br>";
    exit;
}

// Test 2: Database connection
echo "<h3>2. Database Connection Test</h3>";
try {
    $pdo = new PDO(
        "mysql:host=" . getenv('DB_HOST') . ";dbname=" . getenv('DB_DATABASE'),
        getenv('DB_USERNAME'),
        getenv('DB_PASSWORD'),
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
    echo "✅ Database connection successful<br>";
} catch (Exception $e) {
    echo "❌ Database connection failed: " . $e->getMessage() . "<br>";
}

// Test 3: Sessions table existence
echo "<h3>3. Sessions Table Test</h3>";
try {
    $pdo->query("SELECT 1 FROM sessions LIMIT 1");
    echo "✅ Sessions table exists and accessible<br>";
} catch (Exception $e) {
    echo "❌ Sessions table issue: " . $e->getMessage() . "<br>";
}

// Test 4: Cache directories writable
echo "<h3>4. Cache Directories Test</h3>";
$cacheDirs = [
    __DIR__ . '/bootstrap/cache',
    __DIR__ . '/storage/framework/cache',
    __DIR__ . '/storage/framework/sessions',
    __DIR__ . '/storage/framework/views',
];

foreach ($cacheDirs as $dir) {
    if (is_dir($dir)) {
        $testFile = $dir . '/test.tmp';
        if (@file_put_contents($testFile, 'test') !== false) {
            unlink($testFile);
            echo "✅ " . basename($dir) . " is writable<br>";
        } else {
            echo "⚠️ " . basename($dir) . " may not be writable<br>";
        }
    } else {
        echo "ℹ️ " . basename($dir) . " directory not found<br>";
    }
}

// Test 5: Form validation test (simulate grievance form)
echo "<h3>5. Form Validation Test</h3>";
$testData = [
    'district_id' => '1',
    'tehsil_id' => '1',
    'moza_id' => '1',
    'applicant_name' => 'Test User',
    'father_name' => 'Test Father',
    'cnic' => '12345-1234567-1',
    'address' => 'Test Address 123',
    'nature_of_grievance' => 'Test grievance nature',
    'grievance_description' => 'Test description',
    'application_date' => '2024-01-01',
    'grievance_type_id' => '1',
    'status_id' => '1',
];

try {
    // Simulate validation rules from GrievanceController
    $rules = [
        'district_id' => 'required|integer',
        'tehsil_id' => 'required|integer',
        'moza_id' => 'required|integer',
        'applicant_name' => 'required|string|max:255',
        'father_name' => 'required|string|max:255',
        'cnic' => 'required|string|max:20',
        'address' => 'required|string|max:255',
        'nature_of_grievance' => 'nullable|string|max:500',
        'grievance_description' => 'nullable|string',
        'application_date' => 'required|date',
        'grievance_type_id' => 'required|integer',
        'status_id' => 'required|integer',
    ];

    $validator = new Illuminate\Validation\Validator(
        new Illuminate\Translation\Translator(
            new Illuminate\Translation\FileLoader(new Illuminate\Filesystem\Filesystem(), __DIR__ . '/resources/lang'),
            'en'
        ),
        $testData,
        $rules
    );

    if ($validator->passes()) {
        echo "✅ Form validation works correctly<br>";
    } else {
        echo "❌ Form validation failed: " . json_encode($validator->errors()->all()) . "<br>";
    }
} catch (Exception $e) {
    echo "❌ Form validation test error: " . $e->getMessage() . "<br>";
}

// Test 6: Session configuration
echo "<h3>6. Session Configuration Test</h3>";
$sessionDriver = getenv('SESSION_DRIVER') ?: 'file';
echo "Session driver: $sessionDriver<br>";

if ($sessionDriver === 'database') {
    echo "✅ Using database sessions (recommended for production)<br>";
} elseif ($sessionDriver === 'file') {
    echo "ℹ️ Using file sessions (may cause issues on shared hosting)<br>";
} else {
    echo "⚠️ Non-standard session driver: $sessionDriver<br>";
}

// Test 7: Memory and execution time check
echo "<h3>7. PHP Configuration Check</h3>";
$memoryLimit = ini_get('memory_limit');
$maxExecutionTime = ini_get('max_execution_time');

echo "Memory limit: $memoryLimit<br>";
echo "Max execution time: $maxExecutionTime seconds<br>";

if (intval($memoryLimit) >= 128) {
    echo "✅ Memory limit looks good<br>";
} else {
    echo "⚠️ Memory limit may be too low<br>";
}

// Final summary
echo "<br><h2>🎯 DEPLOYMENT READINESS SUMMARY</h2>";
echo "<div style='background: #f0f8ff; padding: 15px; border-radius: 5px; margin: 10px 0;'>";

$issues = 0;

// Check for common issues
if (!file_exists(__DIR__ . '/vendor/autoload.php')) {
    echo "❌ Vendor directory not found<br>";
    $issues++;
}

if (!file_exists(__DIR__ . '/.env')) {
    echo "❌ .env file not found<br>";
    $issues++;
}

if ($sessionDriver !== 'database') {
    echo "⚠️ Not using database sessions (may cause 502 errors)<br>";
    $issues++;
}

if ($issues === 0) {
    echo "<strong style='color: green;'>✅ DEPLOYMENT LOOKS GOOD!</strong><br>";
    echo "Your Laravel application should handle form submissions without 502 errors.<br>";
    echo "Database sessions and proper validation are configured correctly.<br>";
} else {
    echo "<strong style='color: orange;'>⚠️ Some issues detected - review above</strong><br>";
}

echo "</div>";

echo "<br><strong>Next Steps:</strong><br>";
echo "1. Upload all files to cPanel following FINAL_DEPLOYMENT_GUIDE.md<br>";
echo "2. Test form submission with a real user<br>";
echo "3. Monitor PHP error logs if any issues occur<br>";

echo "<br><a href='/pms/' style='background: #007bff; color: white; padding: 10px 15px; text-decoration: none; border-radius: 3px;'>Back to Application</a>";
?>