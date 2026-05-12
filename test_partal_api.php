<?php
// test_partal_api.php - Test the new Partal API endpoint
echo "<h2>Testing Partal Employees API</h2>";

// Test parameters
$tehsil_id = 1; // You can change this to test different tehsils

echo "<h3>Testing Patwaris (ahalkar_type = 1)</h3>";
$url1 = "http://localhost/pms/api/partal-employees?tehsil_id=$tehsil_id&type=patwari";
echo "URL: $url1<br>";
$response1 = file_get_contents($url1);
echo "<pre>" . $response1 . "</pre>";

echo "<h3>Testing All Employees</h3>";
$url2 = "http://localhost/pms/api/partal-employees?tehsil_id=$tehsil_id&type=all";
echo "URL: $url2<br>";
$response2 = file_get_contents($url2);
echo "<pre>" . $response2 . "</pre>";

echo "<br><a href='/pms/'>Back to Application</a>";
?>