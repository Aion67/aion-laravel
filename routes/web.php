<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\MedicationController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\StockMovementController;
use App\Http\Controllers\PrescriptionController;
use App\Http\Controllers\SaleController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::middleware('can:access-pharmacy-operations')->group(function () {
        Route::resource('customers', CustomerController::class)->except(['show']);
        Route::resource('medications', MedicationController::class)->except(['show']);
        Route::get('inventory', [InventoryController::class, 'index'])->name('inventory.index');
        Route::resource('prescriptions', PrescriptionController::class)->only(['index', 'create', 'store', 'show']);
        Route::patch('prescriptions/{prescription}/status', [PrescriptionController::class, 'updateStatus'])->name('prescriptions.status.update');
    });

    Route::middleware('can:adjust-inventory')->group(function () {
        Route::get('inventory/adjust', [InventoryController::class, 'createAdjustment'])->name('inventory.adjust.create');
        Route::post('inventory/adjust', [InventoryController::class, 'storeAdjustment'])->name('inventory.adjust.store');
    });

    Route::middleware('can:view-stock-movements')->group(function () {
        Route::get('stock-movements', [StockMovementController::class, 'index'])->name('stock-movements.index');
    });

    Route::middleware('can:manage-sales')->group(function () {
        Route::resource('sales', SaleController::class)->only(['index', 'create', 'store', 'show']);
    });

    Route::middleware('can:view-reports')->group(function () {
        Route::get('reports/sales', [ReportController::class, 'sales'])->name('reports.sales');
        Route::get('reports/stock', [ReportController::class, 'stock'])->name('reports.stock');
    });

    Route::middleware('can:manage-users')->group(function () {
        Route::resource('users', UserController::class)->except(['show']);
    });
});

require __DIR__.'/auth.php';
