<?php
// test_url_helper.php - Test Laravel URL helper
echo "<h2>Testing Laravel URL Helper</h2>";

try {
    require_once __DIR__ . '/vendor/autoload.php';
    $app = require_once __DIR__ . '/bootstrap/app.php';
    $kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

    // Test URL helper
    $url = url('api/partal-employees');
    echo "Generated URL: $url<br>";
    echo "Expected URL: http://localhost/pms/api/partal-employees<br>";

    if ($url === 'http://localhost/pms/api/partal-employees') {
        echo "✅ URL helper working correctly!<br>";
    } else {
        echo "❌ URL helper not working as expected<br>";
    }

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "<br>";
}
?>