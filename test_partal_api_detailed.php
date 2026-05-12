<?php
// test_partal_api_detailed.php - Detailed test of Partal API
echo "<h2>Detailed Partal API Test</h2>";

// Test the API endpoint directly
function testAPI($url) {
    echo "<h3>Testing: $url</h3>";
    $context = stream_context_create([
        'http' => [
            'method' => 'GET',
            'header' => 'Accept: application/json'
        ]
    ]);

    $response = file_get_contents($url, false, $context);
    if ($response === false) {
        echo "❌ Failed to fetch URL<br>";
        return;
    }

    $data = json_decode($response, true);
    echo "Response: <pre>" . json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "</pre><br>";
}

// Test different tehsil IDs
for ($tehsilId = 1; $tehsilId <= 3; $tehsilId++) {
    testAPI("http://localhost/pms/api/partal-employees?tehsil_id=$tehsilId&type=patwari");
    testAPI("http://localhost/pms/api/partal-employees?tehsil_id=$tehsilId&type=all");
}

echo "<br><a href='/pms/'>Back to Application</a>";
?>