<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\InventoryDashboardController;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware(['auth'])->group(function () {
    // Lab 4 inventory dashboard with real database data
    Route::get('/dashboard', [InventoryDashboardController::class, 'index'])
        ->name('dashboard');

    // Original Student Portal dashboard is preserved here
    Route::get('/student-portal', [DashboardController::class, 'index'])
        ->name('student-portal');

    // Existing product CRUD routes
    Route::resource('products', ProductController::class);

   
    // Inventory page
    Route::get('/inventory', [InventoryController::class, 'index'])
        ->name('inventory.index');

    // Add inventory product
    Route::get('/inventory/products/create', [InventoryController::class, 'create'])
        ->name('inventory.products.create');

    Route::post('/inventory/products', [InventoryController::class, 'storeProduct'])
        ->name('inventory.products.store');

    // View a single inventory product
    Route::get('/inventory/products/{product}', [InventoryController::class, 'show'])
        ->name('inventory.products.show');

    // Edit inventory product
    Route::get('/inventory/products/{product}/edit', [InventoryController::class, 'editProduct'])
        ->name('inventory.products.edit');

    // Update inventory product
    Route::put('/inventory/products/{product}', [InventoryController::class, 'updateProduct'])
        ->name('inventory.products.update');

    // Delete inventory product
    Route::delete('/inventory/products/{product}', [InventoryController::class, 'destroyProduct'])
        ->name('inventory.products.destroy');

    // Stock transactions
    Route::get('/inventory/transactions/create', [InventoryController::class, 'createTransaction'])
        ->name('inventory.transactions.create');

    Route::post('/inventory/transactions', [InventoryController::class, 'storeTransaction'])
        ->name('inventory.transactions.store');

    // Lab 4 dashboard data + CSV export
    Route::get('/inventory/api/items', [InventoryController::class, 'apiItems'])
        ->name('inventory.api.items');

    Route::get('/inventory/export-csv', [InventoryController::class, 'exportCsv'])
        ->name('inventory.export-csv');

    // Inventory API endpoints
    Route::get('/inventory/stats', [InventoryController::class, 'stats'])
        ->name('inventory.stats');

    Route::get('/inventory/low-stock', [InventoryController::class, 'lowStock'])
        ->name('inventory.low-stock');

    Route::get('/inventory/recent-transactions', [InventoryController::class, 'recentTransactions'])
        ->name('inventory.recent-transactions');

});

require __DIR__.'/auth.php';
