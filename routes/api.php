<?php
// Get all districts
Route::get('/districts', function () {
    $districts = DB::table('districts')
        ->orderBy('districtNameUrdu')
        ->get(['districtId as zila_id', 'districtNameUrdu as zilaNameUrdu']);
    return response()->json($districts);
});


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
