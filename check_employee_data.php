<?php
// check_employee_data.php - Check actual employee data
require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

echo "<h2>Employee Data Check</h2>";

try {
    $pdo = Illuminate\Support\Facades\DB::connection()->getPdo();

    // Check employee types
    echo "<h3>Employee Types</h3>";
    $stmt = $pdo->query("SELECT * FROM employee_type");
    $types = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if (count($types) > 0) {
        echo "<table border='1'><tr><th>ID</th><th>Title</th></tr>";
        foreach ($types as $type) {
            echo "<tr><td>{$type['ahalkar_type_id']}</td><td>{$type['ahalkar_title']}</td></tr>";
        }
        echo "</table><br>";
    }

    // Check sample employees
    echo "<h3>Sample Employees</h3>";
    $stmt = $pdo->query("SELECT id, nam, tehsil_id, ahalkar_type FROM employees LIMIT 10");
    $employees = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if (count($employees) > 0) {
        echo "<table border='1'><tr><th>ID</th><th>Name</th><th>Tehsil ID</th><th>Ahalkar Type</th></tr>";
        foreach ($employees as $emp) {
            echo "<tr><td>{$emp['id']}</td><td>{$emp['nam']}</td><td>{$emp['tehsil_id']}</td><td>{$emp['ahalkar_type']}</td></tr>";
        }
        echo "</table><br>";
    } else {
        echo "No employees found.<br>";
    }

    // Check employees by tehsil
    echo "<h3>Employees by Tehsil ID</h3>";
    for ($i = 1; $i <= 5; $i++) {
        $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM employees WHERE tehsil_id = ?");
        $stmt->execute([$i]);
        $count = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
        echo "Tehsil $i: $count employees<br>";
    }

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "<br>";
}
?>