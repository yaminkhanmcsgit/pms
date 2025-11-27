<?php 

use App\Http\Controllers\AuthController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\OperatorController;
use App\Http\Controllers\CompletionProcessController;
use App\Http\Controllers\PartalController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\GrievanceController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\NewsController;

// Auth routes
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::post('/change-password', [AuthController::class, 'changePassword'])->middleware('operator');

// Protected routes (require operator session)
Route::middleware(['operator'])->group(function () {
    Route::get('/', [HomeController::class, 'index'])->name('dashboard');
    // Dashboard
    Route::get('/dashboard', [HomeController::class, 'index'])->name('dashboard');

    // Employees
    Route::get('/employees', [EmployeeController::class, 'index'])->name('employees.index');
    Route::get('/employees/create', [EmployeeController::class, 'create'])->name('employees.create');
    Route::post('/employees', [EmployeeController::class, 'store'])->name('employees.store');
    Route::get('/employees/{id}/edit', [EmployeeController::class, 'edit'])->name('employees.edit');
    Route::put('/employees/{id}', [EmployeeController::class, 'update'])->name('employees.update');

    // Operators
    Route::get('/operators', [OperatorController::class, 'index'])->name('operators.index');
    Route::get('/operators/create', [OperatorController::class, 'create'])->name('operators.create');
    Route::post('/operators', [OperatorController::class, 'store'])->name('operators.store');
    Route::get('/operators/{id}/edit', [OperatorController::class, 'edit'])->name('operators.edit');
    Route::put('/operators/{id}', [OperatorController::class, 'update'])->name('operators.update');

    // Completion Process
    Route::get('/completion-process', [CompletionProcessController::class, 'index'])->name('completion_process.index');
    Route::post('/completion-process/datatable', [CompletionProcessController::class, 'datatable'])->name('completion_process.datatable');
    Route::get('/completion-process/create', [CompletionProcessController::class, 'create'])->name('completion_process.create');
    Route::post('/completion-process', [CompletionProcessController::class, 'store'])->name('completion_process.store');
    Route::get('/completion-process/{id}/edit', [CompletionProcessController::class, 'edit'])->name('completion_process.edit');
    Route::put('/completion-process/{id}', [CompletionProcessController::class, 'update'])->name('completion_process.update');
    Route::get('/completion-process/target-values', [CompletionProcessController::class, 'getTargetValues'])->name('completion_process.target_values');
    Route::post('/completion-process/store-target-value', [CompletionProcessController::class, 'storeTargetValue'])->name('completion_process.store_target_value');
    Route::get('/completion-process/get-target-value', [CompletionProcessController::class, 'getTargetValue'])->name('completion_process.get_target_value');

    // Partal
    Route::get('/partal', [PartalController::class, 'index'])->name('partal.index');
    Route::post('/partal/datatable', [PartalController::class, 'datatable'])->name('partal.datatable');
    Route::get('/partal/create', [PartalController::class, 'create'])->name('partal.create');
    Route::post('/partal', [PartalController::class, 'store'])->name('partal.store');
    Route::get('/partal/{id}/edit', [PartalController::class, 'edit'])->name('partal.edit');
    Route::put('/partal/{id}', [PartalController::class, 'update'])->name('partal.update');

    // Reports
    Route::get('/reports', [HomeController::class, 'reports'])->name('reports');
    Route::get('/reports/partal', [HomeController::class, 'getPartalReports'])->name('reports.partal');
    Route::get('/reports/completion-process', [HomeController::class, 'getCompletionProcessReports'])->name('reports.completion_process');
    Route::get('/reports/grievances', [HomeController::class, 'getGrievancesReports'])->name('reports.grievances');

    // Grievances
    Route::get('/grievances', [GrievanceController::class, 'index'])->name('grievances.index');
    Route::post('/grievances/datatable', [GrievanceController::class, 'datatable'])->name('grievances.datatable');
    Route::get('/grievances/create', [GrievanceController::class, 'create'])->name('grievances.create');
    Route::post('/grievances', [GrievanceController::class, 'store'])->name('grievances.store');
    Route::get('/grievances/{id}', [GrievanceController::class, 'show'])->name('grievances.show');
    Route::get('/grievances/{id}/edit', [GrievanceController::class, 'edit'])->name('grievances.edit');
    Route::put('/grievances/{id}', [GrievanceController::class, 'update'])->name('grievances.update');
    Route::post('/grievances/{id}/update-status', [GrievanceController::class, 'updateStatus'])->name('grievances.updateStatus');
    Route::post('/grievances/{id}/update-field', [GrievanceController::class, 'updateField'])->name('grievances.updateField');
    Route::post('/grievances/{id}/upload-signature', [GrievanceController::class, 'uploadSignature'])->name('grievances.uploadSignature');
    Route::delete('/grievances/{id}', [GrievanceController::class, 'destroy'])->name('grievances.destroy');
    Route::get('/grievance-types', [GrievanceController::class, 'getTypes'])->name('grievances.types');
    Route::get('/grievance-statuses', [GrievanceController::class, 'getStatuses'])->name('grievances.statuses');

    // Contact Us (Admin Only)
    Route::middleware(['operator'])->group(function () {
        Route::get('/contactus', [ContactController::class, 'index'])->name('contactus.index');
        Route::post('/contactus/datatable', [ContactController::class, 'datatable'])->name('contactus.datatable');
        Route::get('/contactus/create', [ContactController::class, 'create'])->name('contactus.create');
        Route::post('/contactus', [ContactController::class, 'store'])->name('contactus.store');
        Route::get('/contactus/{id}/edit', [ContactController::class, 'edit'])->name('contactus.edit');
        Route::put('/contactus/{id}', [ContactController::class, 'update'])->name('contactus.update');
        Route::delete('/contactus/{id}', [ContactController::class, 'destroy'])->name('contactus.destroy');
    });

    // Latest News (Admin Only)
    Route::middleware(['operator'])->group(function () {
        Route::get('/news', [NewsController::class, 'index'])->name('news.index');
        Route::post('/news/datatable', [NewsController::class, 'datatable'])->name('news.datatable');
        Route::get('/news/create', [NewsController::class, 'create'])->name('news.create');
        Route::post('/news', [NewsController::class, 'store'])->name('news.store');
        Route::get('/news/{id}', [NewsController::class, 'show'])->name('news.show');
        Route::get('/news/{id}/edit', [NewsController::class, 'edit'])->name('news.edit');
        Route::put('/news/{id}', [NewsController::class, 'update'])->name('news.update');
        Route::delete('/news/{id}', [NewsController::class, 'destroy'])->name('news.destroy');
    });

    // Settings
    Route::get('/settings', [SettingController::class, 'edit'])->name('settings.edit');
    Route::post('/settings', [SettingController::class, 'update'])->name('settings.update');
});
