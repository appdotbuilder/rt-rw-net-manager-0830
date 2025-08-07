<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/health-check', function () {
    return response()->json([
        'status' => 'ok',
        'timestamp' => now()->toISOString(),
    ]);
})->name('health-check');

Route::get('/', function () {
    return Inertia::render('welcome');
})->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', [\App\Http\Controllers\DashboardController::class, 'index'])->name('dashboard');
    
    // Package Management
    Route::resource('paket', \App\Http\Controllers\PaketController::class);
    
    // Customer Management
    Route::resource('customers', \App\Http\Controllers\CustomerController::class);
    
    // Billing Management
    Route::resource('tagihan', \App\Http\Controllers\TagihanController::class);
    Route::resource('pembayaran', \App\Http\Controllers\PembayaranController::class);
    
    // Reports
    Route::resource('reports', \App\Http\Controllers\ReportController::class)->only(['index', 'show']);
    
    // Mikrotik Management
    Route::resource('mikrotik', \App\Http\Controllers\MikrotikController::class)->only(['index', 'store', 'show', 'update', 'destroy']);
    
    // Billing Automation
    Route::controller(\App\Http\Controllers\AutomationController::class)->prefix('automation')->name('automation.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::post('/', 'store')->name('store');
    });
});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
