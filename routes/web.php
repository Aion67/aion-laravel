<?php

use App\Http\Controllers\ProfileController;
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

    Route::get('/customers', function () {
        return view('modules.index', [
            'title' => 'Customers',
            'description' => 'Manage patient details and customer records.',
        ]);
    })->name('customers.index');

    Route::get('/medications', function () {
        return view('modules.index', [
            'title' => 'Medications',
            'description' => 'Maintain the medication catalog and pricing basics.',
        ]);
    })->name('medications.index');

    Route::get('/inventory', function () {
        return view('modules.index', [
            'title' => 'Inventory',
            'description' => 'Track stock snapshots and adjustment readiness.',
        ]);
    })->name('inventory.index');

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

    Route::get('/stock-movements', function () {
        return view('modules.index', [
            'title' => 'Stock Movements',
            'description' => 'Review stock movement audit events.',
        ]);
    })->name('stock-movements.index');
});

Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/users', function () {
        return view('modules.index', [
            'title' => 'Users',
            'description' => 'Admin-only staff and role management area.',
        ]);
    })->name('users.index');
});

require __DIR__.'/auth.php';
