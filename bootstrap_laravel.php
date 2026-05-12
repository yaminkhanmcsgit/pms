<?php
// bootstrap_laravel.php - Properly bootstrap Laravel for facade usage
function bootstrapLaravel() {
    // Load the autoloader
    require_once __DIR__ . '/vendor/autoload.php';

    // Create the application
    $app = require_once __DIR__ . '/bootstrap/app.php';

    // Create and resolve the HTTP kernel
    $kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

    // Create a minimal request to bootstrap service providers only
    $request = Illuminate\Http\Request::create('/_internal/bootstrap', 'GET', [], [], [], [
        'REQUEST_METHOD' => 'GET',
        'REQUEST_URI' => '/_internal/bootstrap',
        'SERVER_NAME' => 'localhost',
        'SERVER_PORT' => 80,
        'HTTP_HOST' => 'localhost',
        'SCRIPT_NAME' => '/index.php',
        'PHP_SELF' => '/index.php',
    ]);

    // Handle the request to initialize service providers and facades
    try {
        $response = $kernel->handle($request);
        $kernel->terminate($request, $response);
    } catch (Exception $e) {
        // Ignore routing errors for our internal bootstrap request
        if (!str_contains($e->getMessage(), 'Route [_internal/bootstrap] not defined')) {
            // For other errors, try a different approach - just initialize without routing
            try {
                // Manually bootstrap the essential service providers
                $app->make(Illuminate\Session\SessionManager::class);
                $app->make(Illuminate\Cache\CacheManager::class);
                $app->make(Illuminate\Database\DatabaseManager::class);
            } catch (Exception $innerException) {
                // If manual bootstrap fails, re-throw the original error
                throw $e;
            }
        }
    }

    // Return the app instance
    return $app;
}
?>