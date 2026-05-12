<?php
// test_partal_api_fixed.php - Test with correct tehsil IDs
echo "<h2>Partal API Test with Correct Tehsil IDs</h2>";

// Test with actual tehsil IDs that have employees
$testTehsils = [72, 74, 80]; // These have employees

foreach ($testTehsils as $tehsilId) {
    echo "<h3>Tehsil ID: $tehsilId</h3>";

    // Test patwaris
    $url1 = "http://localhost/pms/api/partal-employees?tehsil_id=$tehsilId&type=patwari";
    echo "<strong>Patwaris:</strong> $url1<br>";
    $response1 = @file_get_contents($url1);
    if ($response1 !== false) {
        $data1 = json_decode($response1, true);
        echo "Found: " . count($data1) . " patwaris<br>";
        if (count($data1) > 0) {
            echo "<ul>";
            foreach ($data1 as $emp) {
                echo "<li>{$emp['nam']}</li>";
            }
            echo "</ul>";
        }
    } else {
        echo "❌ Failed to fetch<br>";
    }

    // Test all employees
    $url2 = "http://localhost/pms/api/partal-employees?tehsil_id=$tehsilId&type=all";
    echo "<strong>All Employees:</strong> $url2<br>";
    $response2 = @file_get_contents($url2);
    if ($response2 !== false) {
        $data2 = json_decode($response2, true);
        echo "Found: " . count($data2) . " employees<br>";
        if (count($data2) > 0) {
            echo "<ul>";
            foreach ($data2 as $emp) {
                echo "<li>{$emp['nam']}</li>";
            }
            echo "</ul>";
        }
    } else {
        echo "❌ Failed to fetch<br>";
    }

    echo "<hr>";
}

echo "<br><a href='/pms/'>Back to Application</a>";
?>