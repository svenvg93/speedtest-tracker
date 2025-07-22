<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\DashboardController;

Route::get('/', function () {
    return Inertia::render('login', [
        'canResetPassword' => true,
        'status' => session('status'),
    ]);
})->middleware('guest');

Route::get('/login', function () {
    return Inertia::render('login', [
        'canResetPassword' => true,
        'status' => session('status'),
    ]);
})->name('login')->middleware('guest');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('results', [\App\Http\Controllers\ResultController::class, 'index'])->name('results.index');
    Route::delete('/results/{id}', [\App\Http\Controllers\ResultController::class, 'destroy'])->name('results.destroy');
    Route::delete('/results/delete', [\App\Http\Controllers\ResultController::class, 'destroyMany'])->name('results.destroyMany');
    Route::post('/results/{id}/comments', [\App\Http\Controllers\ResultController::class, 'updateComments'])->name('results.updateComments');
    Route::get('/api/dashboard/results', [\App\Http\Controllers\DashboardController::class, 'resultsData']);
    Route::get('/api/results/stats', [\App\Http\Controllers\DashboardController::class, 'stats']);
    
    // Manual speedtest
    Route::post('/speedtest/run', [\App\Http\Controllers\SpeedtestController::class, 'runManual'])->name('speedtest.run');
    Route::get('/speedtest/servers', [\App\Http\Controllers\SpeedtestController::class, 'getServers'])->name('speedtest.servers');
    
    // User management routes (admin only)
    Route::middleware('admin')->group(function () {
        Route::get('users', [\App\Http\Controllers\UserController::class, 'index'])->name('users.index');
        Route::post('users', [\App\Http\Controllers\UserController::class, 'store'])->name('users.store');
        Route::put('users/{id}', [\App\Http\Controllers\UserController::class, 'update'])->name('users.update');
        Route::delete('users/{id}', [\App\Http\Controllers\UserController::class, 'destroy'])->name('users.destroy');
        Route::delete('users/delete', [\App\Http\Controllers\UserController::class, 'destroyMany'])->name('users.destroyMany');
    });

    // API Token management routes (admin only)
    Route::middleware('admin')->group(function () {
        Route::get('api-tokens', [\App\Http\Controllers\ApiTokenController::class, 'index'])->name('api-tokens.index');
        Route::post('api-tokens', [\App\Http\Controllers\ApiTokenController::class, 'store'])->name('api-tokens.store');
        Route::put('api-tokens/{id}', [\App\Http\Controllers\ApiTokenController::class, 'update'])->name('api-tokens.update');
        Route::delete('api-tokens/{id}', [\App\Http\Controllers\ApiTokenController::class, 'destroy'])->name('api-tokens.destroy');
        Route::delete('api-tokens/delete', [\App\Http\Controllers\ApiTokenController::class, 'destroyMany'])->name('api-tokens.destroyMany');
    });
});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
