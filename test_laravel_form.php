<?php
// test_laravel_form.php - Test Laravel form submission
require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

// Simulate a request
$request = Illuminate\Http\Request::create('/test-form', 'POST', [
    'district_id' => '1',
    'tehsil_id' => '1',
    'moza_id' => '1',
    'applicant_name' => 'Test User',
    'father_name' => 'Test Father',
    'cnic' => '12345-1234567-1',
    'address' => '1234567890',
    'nature_of_grievance' => 'Test grievance',
    'grievance_description' => 'Test description',
    'application_date' => '2024-01-01',
    'grievance_type_id' => '1',
    'status_id' => '1',
]);

try {
    // Simulate session
    session(['operator_id' => 1]);

    // Test validation
    $request->validate([
        'district_id' => 'required|integer',
        'tehsil_id' => 'required|integer',
        'moza_id' => 'required|integer',
        'applicant_name' => 'required|string|max:255',
        'father_name' => 'required|string|max:255',
        'cnic' => 'required|string|max:20',
        'address' => 'required|string|max:255',
        'nature_of_grievance' => 'nullable|string|max:500',
        'grievance_description' => 'nullable|string',
        'application_date' => 'required|date',
        'grievance_type_id' => 'required|integer',
        'status_id' => 'required|integer',
    ]);

    echo "Validation passed!\n";

    // Test database insert (commented out to avoid actual insert)
    // Illuminate\Support\Facades\DB::table('grievances')->insert([...]);

    echo "Form processing test completed successfully!\n";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . ":" . $e->getLine() . "\n";
}
?>