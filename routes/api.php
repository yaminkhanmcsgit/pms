<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\GrievanceController;
use App\Http\Controllers\PartalController;
use App\Http\Controllers\EmployeeController;
Route::post('/login', [AuthController::class, 'apiLogin']);
Route::post('/logout', [AuthController::class, 'logout']);
Route::post('/forgot-password', [AuthController::class, 'sendResetLink']);

// Get settings for logo and footer
Route::get('/settings', function () {
    $setting = DB::table('settings')->first();
    return response()->json($setting);
});

// Get dashboard counts
Route::middleware('operator')->get('/dashboard-counts', function () {
    $role_id = session('role_id');
    $counts = [
        'operators' => DB::table('operators')->count(),
        'employees' => DB::table('employees')->count(),
        'completion_process' => DB::table('completion_process')->count(),
        'partal' => DB::table('partal')->count(),
        'grievances' => DB::table('grievances')->count(),
    ];
    return response()->json($counts);
});

// Grievances
Route::middleware('operator')->get('/grievances', [GrievanceController::class, 'apiIndex']);

// Partal
Route::middleware('operator')->get('/partal', [PartalController::class, 'apiIndex']);

// Employees
Route::middleware('operator')->get('/employees', [EmployeeController::class, 'apiIndex']);

// Chart Data APIs
Route::middleware('operator')->group(function () {
    Route::get('/chart-data/grievances', [HomeController::class, 'getGrievancesData']);
    Route::get('/chart-data/partal', [HomeController::class, 'getPartalData']);
    Route::get('/chart-data/completion-process', [HomeController::class, 'getCompletionProcessData']);
    Route::get('/chart-data/partal-sums', [HomeController::class, 'getPartalSums']);
    Route::get('/chart-data/completion-process-sums', [HomeController::class, 'getCompletionProcessSums']);
    Route::get('/chart-data/grievances-by-type', [HomeController::class, 'getGrievancesByType']);
});


// Get current user info
Route::middleware('operator')->get('/user', function () {
    return response()->json([
        'id' => session('operator_id'),
        'name' => session('operator_name'),
        'role_id' => session('role_id'),
        'zila_id' => session('zila_id'),
        'tehsil_id' => session('tehsil_id')
    ]);
});

// Get all districts
Route::get('/districts', function () {
    $districts = DB::table('districts')
        ->orderBy('districtNameUrdu')
        ->get(['districtId as zila_id', 'districtNameUrdu as zilaNameUrdu']);
    return response()->json($districts);
});

// Get tehsils for a district
Route::get('/tehsils', function (Request $request) {
    $district_id = $request->query('district_id');
    $tehsils = DB::table('tehsils')
        ->where('districtId', $district_id)
        ->orderBy('tehsilNameUrdu')
        ->get(['tehsilId as tehsil_id', 'tehsilNameUrdu as tehsilNameUrdu']);
    return response()->json($tehsils);
});

// Get mozas for a tehsil
Route::get('/mozas', function (Request $request) {
    $tehsil_id = $request->query('tehsil_id');
    $mozas = DB::table('mozas')
        ->where('tehsilId', $tehsil_id)
        ->orderBy('mozaNameUrdu')
        ->get(['mozaId as moza_id', 'mozaNameUrdu as mozaNameUrdu']);
    return response()->json($mozas);
});

// Get completion process types
Route::get('/completion-process-types', function () {
    $types = DB::table('completion_process_types')
        ->orderBy('order_by', 'asc')
        ->get(['id', 'title_ur', 'field_name']);
    return response()->json($types);
});

// Get employees
Route::get('/employees', function (Request $request) {
    $query = DB::table('employees')
        ->orderBy('nam');

    $role_id = $request->query('role_id');
    if ($role_id > 1) {
        $zila_id = $request->query('zila_id');
        $tehsil_id = $request->query('tehsil_id');
        $query->where('zila_id', $zila_id)
              ->where('tehsil_id', $tehsil_id);
    }

    $employees = $query->get(['id', 'nam']);
    return response()->json($employees);
});

// Get employees for Partal form (filtered by tehsil and type)
Route::get('/partal-employees', function (Request $request) {
    $tehsil_id = $request->query('tehsil_id');
    $type = $request->query('type'); // 'patwari' or 'all'

    $query = DB::table('employees')
        ->leftJoin('employee_type', 'employees.ahalkar_type', '=', 'employee_type.ahalkar_type_id')
        ->where('employees.tehsil_id', $tehsil_id)
        ->orderBy('employees.nam');

    if ($type === 'patwari') {
        $query->where('employees.ahalkar_type', '1'); // patwari (check as string since VARCHAR)
    }

    $employees = $query->select('employees.id', 'employees.nam')->get();
    return response()->json($employees);
});


