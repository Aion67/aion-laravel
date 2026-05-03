<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\MedicationController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\StockMovementController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard', [
        'cards' => [
            ['label' => 'Customers', 'value' => '0'],
            ['label' => 'Medications', 'value' => '0'],
            ['label' => 'Low Stock', 'value' => '0'],
            ['label' => "Today's Sales", 'value' => '0.00'],
        ],
    ]);
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::middleware('role:admin,pharmacist')->group(function () {
        Route::resource('customers', CustomerController::class)->except(['show']);
        Route::resource('medications', MedicationController::class)->except(['show']);
        Route::get('inventory', [InventoryController::class, 'index'])->name('inventory.index');
        Route::get('inventory/adjust', [InventoryController::class, 'createAdjustment'])->name('inventory.adjust.create');
        Route::post('inventory/adjust', [InventoryController::class, 'storeAdjustment'])->name('inventory.adjust.store');
        Route::get('stock-movements', [StockMovementController::class, 'index'])->name('stock-movements.index');
    });

    Route::get('/prescriptions', function () {
        return view('modules.index', [
            'title' => 'Prescriptions',
            'description' => 'Prepare and review prescription records workflow.',
        ]);
    })->name('prescriptions.index');

    Route::get('/sales', function () {
        return view('modules.index', [
            'title' => 'Sales',
            'description' => 'Manage point-of-sale transaction flow.',
        ]);
    })->name('sales.index');

});

Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::resource('users', UserController::class)->except(['show']);
});

require __DIR__.'/auth.php';
