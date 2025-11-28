<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;

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
        ->orderBy('id')
        ->get(['id', 'title_ur']);
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
