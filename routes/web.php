<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ResultController;
use App\Http\Controllers\SpeedtestController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ApiTokenController;

// Guest routes
Route::middleware('guest')->group(function () {
    Route::get('/', function () {
        return Inertia::render('login', [
            'canResetPassword' => true,
            'status' => session('status'),
        ]);
    });

    Route::get('/login', function () {
        return Inertia::render('login', [
            'canResetPassword' => true,
            'status' => session('status'),
        ]);
    })->name('login');
});

// Authenticated routes
Route::middleware(['auth'])->group(function () {
    // Dashboard
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Results management
    Route::delete('results/delete', [ResultController::class, 'destroyMany'])->name('results.destroyMany');
    Route::resource('results', ResultController::class);
    Route::post('results/{id}/comments', [ResultController::class, 'updateComments'])->name('results.comments');
    
    // Speedtest operations
    Route::prefix('speedtest')->name('speedtest.')->group(function () {
        Route::post('run', [SpeedtestController::class, 'runManual'])->name('run');
        Route::get('servers', [SpeedtestController::class, 'getServers'])->name('servers');
    });
    
    // Dashboard API endpoints
    Route::prefix('api')->name('api.')->group(function () {
        Route::get('dashboard/results', [DashboardController::class, 'resultsData'])->name('dashboard.results');
        Route::get('results/stats', [DashboardController::class, 'stats'])->name('results.stats');
    });
    
    // Admin-only routes
    Route::middleware('admin')->group(function () {
        Route::resource('users', UserController::class);
        Route::delete('api-tokens/delete', [ApiTokenController::class, 'destroyMany'])->name('api-tokens.destroyMany');
        Route::resource('api-tokens', ApiTokenController::class);
    });
});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
