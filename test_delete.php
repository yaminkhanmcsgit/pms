<?php
// test_delete.php - Test the delete functionality
echo "<h2>Testing Delete Functionality</h2>";

// Check if there are any grievances to test with
require_once __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

try {
    $pdo = Illuminate\Support\Facades\DB::connection()->getPdo();

    // Get a sample grievance
    $stmt = $pdo->query("SELECT id FROM grievances LIMIT 1");
    $grievance = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($grievance) {
        echo "<p><strong>Test Grievance ID:</strong> {$grievance['id']}</p>";
        echo "<p>You can test the delete function with this ID.</p>";
        echo "<button onclick='deleteGrievance({$grievance['id']})'>Test Delete</button>";
    } else {
        echo "<p>No grievances found in database.</p>";
    }

    // Show delete route status
    echo "<h3>Delete Route Check</h3>";
    $routes = app('router')->getRoutes();
    $deleteRouteFound = false;

    foreach ($routes as $route) {
        if ($route->getName() === 'grievances.destroy') {
            $deleteRouteFound = true;
            echo "<p>✅ Delete route found: " . $route->methods()[0] . " " . $route->uri() . "</p>";
            break;
        }
    }

    if (!$deleteRouteFound) {
        echo "<p>❌ Delete route not found!</p>";
    }

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "<br>";
}

// Include the delete JavaScript function for testing
echo "
<script>
function deleteGrievance(grievanceId) {
    console.log('Test delete for ID:', grievanceId);

    if (!confirm('Really delete grievance ' + grievanceId + '?')) {
        return;
    }

    // Use AJAX DELETE request
    const baseUrl = window.location.origin;
    const pathParts = window.location.pathname.split('/');
    const appPath = pathParts[1];
    const deleteUrl = baseUrl + '/' + appPath + '/grievances/' + grievanceId;

    console.log('Delete URL:', deleteUrl);

    const csrfToken = document.querySelector('meta[name=\"csrf-token\"]');
    if (!csrfToken) {
        alert('CSRF token not found!');
        return;
    }

    fetch(deleteUrl, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': csrfToken.getAttribute('content'),
            'X-Requested-With': 'XMLHttpRequest',
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        }
    })
    .then(response => {
        console.log('Response status:', response.status);
        return response.text().then(text => {
            console.log('Response text:', text);
            alert('Response: ' + text);
            location.reload();
        });
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error: ' + error.message);
    });
}
</script>
";
?>