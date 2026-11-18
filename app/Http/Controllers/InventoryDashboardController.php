<?php

namespace App\Http\Controllers;

use App\Models\InventoryProduct;
use App\Models\InventoryTransaction;

/**
 * Dashboard for the Lab 4 inventory interface, using real MySQL data.
 *
 * The original Student Portal dashboard is preserved at /student-portal
 * (DashboardController@index) and at /products (ProductController@index).
 */
class InventoryDashboardController extends Controller
{
    public function index()
    {
        $products = InventoryProduct::latest()->get();

        $totalProducts   = $products->count();
        $totalQuantity   = (int) $products->sum('quantity');
        $lowStockCount   = $products->filter(
            fn ($product) => $product->quantity > 0 &&
                             $product->quantity <= $product->reorder_level
        )->count();
        $outOfStockCount = $products->filter(
            fn ($product) => (int) $product->quantity === 0
        )->count();
        $inventoryValue  = $products->sum(
            fn ($product) => $product->quantity * $product->unit_price
        );

        $recentProducts = $products->take(5);

        $recentTransactions = InventoryTransaction::with(['inventoryProduct', 'user'])
            ->recent()
            ->take(5)
            ->get();

        return view('dashboard-inventory', compact(
            'totalProducts',
            'totalQuantity',
            'lowStockCount',
            'outOfStockCount',
            'inventoryValue',
            'recentProducts',
            'recentTransactions'
        ));
    }
}
