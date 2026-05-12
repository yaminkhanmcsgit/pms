<?php
// create_sessions_simple.php - Create sessions table without Laravel facades
echo "<h2>Creating Sessions Table (Simple Version)</h2>";

// Database configuration from .env file
function loadEnv($path) {
    if (!file_exists($path)) {
        return false;
    }

    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) {
            continue;
        }

        list($name, $value) = explode('=', $line, 2);
        $name = trim($name);
        $value = trim($value);

        if (!array_key_exists($name, $_SERVER) && !array_key_exists($name, $_ENV)) {
            putenv(sprintf('%s=%s', $name, $value));
            $_ENV[$name] = $value;
            $_SERVER[$name] = $value;
        }
    }
    return true;
}

// Load environment variables
$envPath = __DIR__ . '/.env';
if (!loadEnv($envPath)) {
    echo "❌ Could not load .env file<br>";
    exit;
}

echo "✓ Environment loaded<br>";

// Database connection parameters
$host = getenv('DB_HOST') ?: 'localhost';
$database = getenv('DB_DATABASE') ?: '';
$username = getenv('DB_USERNAME') ?: '';
$password = getenv('DB_PASSWORD') ?: '';

if (empty($database) || empty($username)) {
    echo "❌ Database configuration missing in .env file<br>";
    exit;
}

echo "✓ Database config found<br>";

// Connect to database
try {
    $dsn = "mysql:host=$host;dbname=$database;charset=utf8mb4";
    $pdo = new PDO($dsn, $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);

    echo "✓ Database connected successfully<br>";

    // Check if sessions table exists
    $result = $pdo->query("SHOW TABLES LIKE 'sessions'");
    $tableExists = $result->rowCount() > 0;

    if ($tableExists) {
        echo "✓ Sessions table already exists<br>";
    } else {
        echo "Creating sessions table...<br>";

        // Create the sessions table
        $sql = "CREATE TABLE `sessions` (
            `id` varchar(255) NOT NULL,
            `operator_id` bigint(20) unsigned DEFAULT NULL,
            `ip_address` varchar(45) DEFAULT NULL,
            `user_agent` text,
            `payload` text NOT NULL,
            `last_activity` int(11) NOT NULL,
            PRIMARY KEY (`id`),
            KEY `sessions_operator_id_index` (`operator_id`),
            KEY `sessions_last_activity_index` (`last_activity`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

        $pdo->exec($sql);
        echo "✓ Sessions table created successfully!<br>";
    }

    echo "<br><strong>Session table setup completed!</strong><br>";

} catch (PDOException $e) {
    echo "❌ Database error: " . $e->getMessage() . "<br>";
    exit;
}

echo "<a href='../'>Back to Application</a>";
?>