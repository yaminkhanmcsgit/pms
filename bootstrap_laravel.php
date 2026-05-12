<?php
// bootstrap_laravel.php - Properly bootstrap Laravel for facade usage
function bootstrapLaravel() {
    // Load the autoloader
    require_once __DIR__ . '/vendor/autoload.php';

    // Create the application
    $app = require_once __DIR__ . '/bootstrap/app.php';

    // Create and resolve the HTTP kernel
    $kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

    // Create a fake request to bootstrap all service providers
    $request = Illuminate\Http\Request::create('/_bootstrap', 'GET');

    // Handle the request to initialize everything
    try {
        $response = $kernel->handle($request);
        $kernel->terminate($request, $response);
    } catch (Exception $e) {
        // Ignore routing errors for our fake request
        if (!str_contains($e->getMessage(), 'Route [_bootstrap] not defined')) {
            throw $e;
        }
    }

    // Return the app instance
    return $app;
}
?>