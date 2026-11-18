<?php

namespace App\Http\Controllers;

use App\Models\InventoryProduct;
use App\Models\InventoryTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class InventoryController extends Controller
{
    /**
     * Categories from the Lab 5 hardware theme, plus the Lab 4 academic
     * categories so existing data stays compatible.
     */
    private const CATEGORIES = [
        'Lumber', 'Hardware', 'Tools', 'Electrical', 'Plumbing', 'Paint',
        'Sensors', 'Textbooks', 'Lab Equipment',
    ];

    /* ------------------------------------------------------------------ */
    /* Shared helpers                                                      */
    /* ------------------------------------------------------------------ */

    private function productRules(?int $productId = null): array
    {
        $skuRule = Rule::unique('inventory_products', 'sku');

        if ($productId) {
            $skuRule->ignore($productId);
        }

        return [
            'name'          => ['required', 'string', 'max:255'],
            'sku'           => ['required', 'string', 'max:255', $skuRule],
            'description'   => ['nullable', 'string', 'max:1000'],
            'category'      => ['nullable', 'string', 'max:255', Rule::in(self::CATEGORIES)],
            'quantity'      => ['required', 'integer', 'min:0'],
            'reorder_level' => ['required', 'integer', 'min:0'],
            'unit_price'    => ['required', 'numeric', 'min:0'],
            'supplier'      => ['nullable', 'string', 'max:255'],
        ];
    }

    private function productMessages(): array
    {
        return [
            'name.required'          => 'Product name is required.',
            'sku.required'           => 'SKU is required.',
            'sku.unique'             => 'This SKU is already used by another product.',
            'category.in'            => 'Please choose a valid category.',
            'quantity.min'           => 'Quantity cannot be negative.',
            'reorder_level.min'      => 'Reorder level cannot be negative.',
            'unit_price.numeric'     => 'Unit price must be a number.',
            'unit_price.min'         => 'Unit price cannot be negative.',
        ];
    }

    /**
     * Apply Lab 4 search/filters using Laravel database queries (server side).
     */
    private function applyFilters($query, Request $request)
    {
        if ($request->filled('search')) {
            $search = trim($request->string('search')->toString());

            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%");
            });
        }

        if ($request->filled('category') && $request->input('category') !== 'all') {
            $query->where('category', $request->input('category'));
        }

        if ($request->filled('status') && $request->input('status') !== 'all') {
            switch ($request->input('status')) {
                case 'Low Stock':
                    $query->where('quantity', '>', 0)
                          ->whereColumn('quantity', '<=', 'reorder_level');
                    break;

                case 'Out of Stock':
                    $query->where('quantity', 0);
                    break;

                case 'In Stock':
                    $query->where('quantity', '>', 0)
                          ->whereColumn('quantity', '>', 'reorder_level');
                    break;
            }
        }

        if ($request->filled('min_price')) {
            $query->where('unit_price', '>=', (float) $request->input('min_price'));
        }

        if ($request->filled('max_price')) {
            $query->where('unit_price', '<=', (float) $request->input('max_price'));
        }

        return $query;
    }

    /**
     * Convert a model into the exact JSON shape used by the Lab 4 JavaScript.
     */
    private function toLab4Item(InventoryProduct $product): array
    {
        return [
            'id'           => $product->id,
            'sku'          => $product->sku,
            'name'         => $product->name,
            'description'  => $product->description,
            'category'     => $product->category,
            'stock'        => (int) $product->quantity,
            'reorderLevel' => (int) $product->reorder_level,
            'price'        => (float) $product->unit_price,
            'supplier'     => $product->supplier,
            'status'       => $product->status_label,
            'lastUpdated'  => $product->updated_at?->format('Y-m-d') ?? '',
        ];
    }

    /* ------------------------------------------------------------------ */
    /* Pages                                                               */
    /* ------------------------------------------------------------------ */

    public function index(Request $request)
    {
        $query = InventoryProduct::query();
        $this->applyFilters($query, $request);

        $products = $query->latest()->paginate(10)->withQueryString();

        // The transaction dropdown must list EVERY product, not just the
        // current filtered/paginated page.
        $allProducts = InventoryProduct::orderBy('name')->get();

        $lowStockProducts = InventoryProduct::lowStock()->get();

        $transactions = InventoryTransaction::with(['inventoryProduct', 'user'])
            ->recent()
            ->take(10)
            ->get();

        $categories = InventoryProduct::query()
            ->whereNotNull('category')
            ->distinct()
            ->orderBy('category')
            ->pluck('category');

        return view('inventory.index', compact(
            'products',
            'allProducts',
            'lowStockProducts',
            'transactions',
            'categories'
        ));
    }

    /**
     * Dedicated page for recording a stock movement.
     */
    public function createTransaction()
    {
        return view('inventory.transaction-create', [
            'allProducts' => InventoryProduct::orderBy('name')->get(),
        ]);
    }

    public function create()
    {
        return view('inventory.create', ['categories' => self::CATEGORIES]);
    }

    public function show(InventoryProduct $product)
    {
        $transactions = $product->transactions()
            ->with('user')
            ->recent()
            ->get();

        return view('inventory.show', compact('product', 'transactions'));
    }

    public function editProduct(InventoryProduct $product)
    {
        return view('inventory.edit', [
            'product'    => $product,
            'categories' => self::CATEGORIES,
        ]);
    }

    /* ------------------------------------------------------------------ */
    /* Product CRUD                                                        */
    /* ------------------------------------------------------------------ */

    public function storeProduct(Request $request)
    {
        $validated = $request->validate(
            $this->productRules(),
            $this->productMessages()
        );

        InventoryProduct::create($validated);

        return redirect()->route('inventory.index')
            ->with('success', 'Inventory product added successfully.');
    }

    public function updateProduct(Request $request, InventoryProduct $product)
    {
        $validated = $request->validate(
            $this->productRules($product->id),
            $this->productMessages()
        );

        $product->update($validated);

        return redirect()->route('inventory.index')
            ->with('success', 'Inventory product updated successfully.');
    }

    public function destroyProduct(InventoryProduct $product)
    {
        // Keep transaction history intact: only unused products can be deleted.
        if ($product->transactions()->exists()) {
            return redirect()->route('inventory.index')
                ->with('error', 'This product has stock transactions and cannot be deleted. Keep its transaction history.');
        }

        $product->delete();

        return redirect()->route('inventory.index')
            ->with('success', 'Inventory product deleted successfully.');
    }

    /* ------------------------------------------------------------------ */
    /* Stock movements (stock is updated by InventoryTransactionObserver)   */
    /* ------------------------------------------------------------------ */

    public function storeTransaction(Request $request)
    {
        $validated = $request->validate([
            'inventory_product_id' => 'required|exists:inventory_products,id',
            'type'                 => 'required|in:stock_in,stock_out',
            'quantity'             => 'required|integer|min:1',
            'reference_number'     => 'nullable|string|max:255',
            'notes'                => 'nullable|string',
        ], [
            'quantity.min' => 'Transaction quantity must be at least 1.',
        ]);

        $validated['user_id'] = auth()->id();

        DB::transaction(function () use ($validated) {
            $product = InventoryProduct::whereKey($validated['inventory_product_id'])
                ->lockForUpdate()
                ->firstOrFail();

            if (
                $validated['type'] === 'stock_out' &&
                $product->quantity < $validated['quantity']
            ) {
                throw ValidationException::withMessages([
                    'quantity' => 'Not enough stock available.',
                ]);
            }

            // The observer handles the quantity increase/decrease.
            InventoryTransaction::create($validated);
        });

        return redirect()->route('inventory.index')
            ->with('success', 'Inventory transaction recorded.');
    }

    /* ------------------------------------------------------------------ */
    /* JSON endpoints for the Lab 4 frontend                               */
    /* ------------------------------------------------------------------ */

    public function apiItems(Request $request)
    {
        $query = InventoryProduct::query();
        $this->applyFilters($query, $request);

        $filtered = $query->latest()->get();
        $all      = InventoryProduct::latest()->get();

        $statistics = [
            'totalItems'      => $all->count(),
            'totalValue'      => number_format(
                $all->sum(fn ($product) => $product->quantity * $product->unit_price),
                2,
                '.',
                ''
            ),
            'lowStockCount'   => $all->filter(
                fn ($product) => $product->quantity > 0 &&
                                 $product->quantity <= $product->reorder_level
            )->count(),
            'outOfStockCount' => $all->filter(
                fn ($product) => (int) $product->quantity === 0
            )->count(),
        ];

        return response()->json([
            'success'      => true,
            'items'        => $filtered->map(fn ($p) => $this->toLab4Item($p))->values(),
            'all_items'    => $all->map(fn ($p) => $this->toLab4Item($p))->values(),
            'stats'        => $statistics,
            'categories'   => self::CATEGORIES,
            'transactions' => InventoryTransaction::with(['inventoryProduct', 'user'])
                ->recent()
                ->take(10)
                ->get()
                ->map(fn ($transaction) => [
                    'id'         => $transaction->id,
                    'product'    => $transaction->inventoryProduct?->name,
                    'type'       => $transaction->type,
                    'quantity'   => $transaction->quantity,
                    'reference'  => $transaction->reference_number,
                    'user'       => $transaction->user?->name,
                    'date'       => $transaction->created_at?->format('Y-m-d H:i'),
                ]),
        ]);
    }

    public function stats()
    {
        $all = InventoryProduct::all();

        return response()->json([
            'total_products'     => $all->count(),
            'total_stock'        => $all->sum('quantity'),
            'low_stock_count'    => $all->filter(
                fn ($product) => $product->quantity > 0 &&
                                 $product->quantity <= $product->reorder_level
            )->count(),
            'out_of_stock_count' => $all->filter(
                fn ($product) => (int) $product->quantity === 0
            )->count(),
            'inventory_value'    => round(
                $all->sum(fn ($product) => $product->quantity * $product->unit_price),
                2
            ),
            'recent_transactions' => InventoryTransaction::count(),
        ]);
    }

    public function lowStock()
    {
        return response()->json(InventoryProduct::lowStock()->get());
    }

    public function recentTransactions()
    {
        return response()->json(
            InventoryTransaction::with('inventoryProduct')
                ->recent()
                ->take(10)
                ->get()
        );
    }

    /* ------------------------------------------------------------------ */
    /* CSV export (server side, respects the current filters)              */
    /* ------------------------------------------------------------------ */

    public function exportCsv(Request $request)
    {
        $query = InventoryProduct::query();
        $this->applyFilters($query, $request);

        $products = $query->latest()->get();

        $handle = fopen('php://temp', 'w+');

        fputcsv($handle, [
            'ID', 'SKU', 'Product Name', 'Category', 'Description',
            'Stock', 'Reorder Level', 'Unit Price', 'Supplier',
            'Total Value', 'Status', 'Last Updated',
        ]);

        foreach ($products as $product) {
            fputcsv($handle, [
                $product->id,
                $product->sku,
                $product->name,
                $product->category,
                $product->description,
                $product->quantity,
                $product->reorder_level,
                $product->unit_price,
                $product->supplier,
                $product->total_value,
                $product->status_label,
                $product->updated_at?->format('Y-m-d'),
            ]);
        }

        rewind($handle);
        $content = stream_get_contents($handle);
        fclose($handle);

        return response($content, 200, [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="Inventory_Report_'
                . now()->format('Y-m-d') . '.csv"',
        ]);
    }
}
