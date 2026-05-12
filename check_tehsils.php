<?php
// check_tehsils.php - Check available tehsil IDs
echo "<h2>Available Tehsil IDs</h2>";

try {
    $pdo = new PDO(
        "mysql:host=localhost;dbname=pms;charset=utf8mb4",
        "root",
        "",
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );

    $stmt = $pdo->query("SELECT tehsilId, tehsilNameUrdu FROM tehsils ORDER BY tehsilId LIMIT 20");
    $tehsils = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (count($tehsils) > 0) {
        echo "<table border='1'><tr><th>ID</th><th>Name</th></tr>";
        foreach ($tehsils as $tehsil) {
            echo "<tr><td>{$tehsil['tehsilId']}</td><td>{$tehsil['tehsilNameUrdu']}</td></tr>";
        }
        echo "</table><br>";
    } else {
        echo "No tehsils found.<br>";
    }

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "<br>";
}
?>