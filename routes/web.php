<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Transaction\AssessmentController;
use App\Http\Controllers\Transaction\MonitoringController;
use App\Http\Controllers\Master\DepartmentController;
use App\Http\Controllers\Master\PositionController;
use App\Http\Controllers\Master\EmployeeController;
use App\Http\Controllers\Master\PeriodController;
use App\Http\Controllers\Master\AssessmentCategoryController;
use App\Http\Controllers\Master\AssessmentIndicatorController;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard.index');
    })->name('dashboard');

    Route::prefix('master')->name('master.')->group(function () {
        Route::get('/', function () {
            return view('master.index');
        })->name('index');
        
        Route::resource('departments', DepartmentController::class);
        Route::resource('positions', PositionController::class);
        Route::resource('employees', EmployeeController::class);
        Route::resource('periods', PeriodController::class);
        Route::resource('assessment-categories', AssessmentCategoryController::class);
        Route::resource('assessment-indicators', AssessmentIndicatorController::class);
    });

    Route::prefix('transaction')->name('transaction.')->group(function () {
        Route::get('assessments/dashboard', [AssessmentController::class, 'index'])->name('assessments.index');
        Route::get('assessments/create', [AssessmentController::class, 'create'])->name('assessments.create');
        Route::post('assessments', [AssessmentController::class, 'store'])->name('assessments.store');
        Route::get('assessments/{assessment}', [AssessmentController::class, 'show'])->name('assessments.show');

        Route::get('monitoring', [MonitoringController::class, 'index'])->name('monitoring.index');

        // Calculation routes
        Route::get('calculations', [\App\Http\Controllers\Transaction\CalculationController::class, 'index'])->name('calculations.index');
        Route::post('calculations/all', [\App\Http\Controllers\Transaction\CalculationController::class, 'calculateAll'])->name('calculations.calculateAll');
        Route::post('calculations/{employee}', [\App\Http\Controllers\Transaction\CalculationController::class, 'calculate'])->name('calculations.calculate');
        Route::get('calculations/{employee}', [\App\Http\Controllers\Transaction\CalculationController::class, 'show'])->name('calculations.show');
    });

    Route::get('/assessment', function () {
        return view('assessment.index');
    })->name('assessment.index');

    Route::get('/report', function () {
        return view('report.index');
    })->name('report.index');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
