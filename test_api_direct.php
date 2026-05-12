<?php
// test_api_direct.php - Test API endpoint directly
echo "<h2>Testing API Endpoint Directly</h2>";

// Test with the correct Laravel URL
$baseUrl = "http://localhost/pms";
$tehsilId = 80; // We know this has employees

echo "<h3>Testing Patwaris API</h3>";
$url1 = "$baseUrl/api/partal-employees?tehsil_id=$tehsilId&type=patwari";
echo "URL: $url1<br>";

$context = stream_context_create([
    'http' => [
        'method' => 'GET',
        'header' => 'Accept: application/json'
    ]
]);

$response1 = file_get_contents($url1, false, $context);
if ($response1 !== false) {
    $data1 = json_decode($response1, true);
    echo "<pre>" . json_encode($data1, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "</pre>";
} else {
    echo "❌ Failed to fetch<br>";
}

echo "<h3>Testing All Employees API</h3>";
$url2 = "$baseUrl/api/partal-employees?tehsil_id=$tehsilId&type=all";
echo "URL: $url2<br>";

$response2 = file_get_contents($url2, false, $context);
if ($response2 !== false) {
    $data2 = json_decode($response2, true);
    echo "<pre>" . json_encode($data2, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "</pre>";
} else {
    echo "❌ Failed to fetch<br>";
}

echo "<br><a href='/pms/'>Back to Application</a>";
?>