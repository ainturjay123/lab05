<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Product Details — {{ $product->name }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="{{ asset('css/lab4-style.css') }}" rel="stylesheet">
</head>
<body>
<div class="container py-5">
    <div class="card shadow-sm mx-auto" style="max-width: 900px;">
        <div class="card-body p-4">

            <div class="d-flex flex-wrap justify-content-between align-items-start gap-2 mb-4">
                <div>
                    <h2 class="fw-bold text-primary-custom mb-1">{{ $product->name }}</h2>
                    <p class="text-muted small mb-0">SKU: <strong>{{ $product->sku }}</strong></p>
                </div>

                <div>
                    @if ($product->isOutOfStock())
                        <span class="badge bg-danger">Out of Stock</span>
                    @elseif ($product->isLowStock())
                        <span class="badge bg-warning text-dark">Low Stock</span>
                    @else
                        <span class="badge bg-success">In Stock</span>
                    @endif
                </div>
            </div>

            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            <div class="row g-3 mb-4">
                <div class="col-md-4">
                    <div class="stat-card h-100">
                        <div class="card-body">
                            <h6 class="card-title">Quantity</h6>
                            <p class="card-text">{{ $product->quantity }}</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="stat-card h-100">
                        <div class="card-body">
                            <h6 class="card-title">Unit Price</h6>
                            <p class="card-text">${{ number_format((float) $product->unit_price, 2) }}</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="stat-card h-100">
                        <div class="card-body">
                            <h6 class="card-title">Total Value</h6>
                            <p class="card-text">${{ number_format($product->total_value, 2) }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <table class="table table-bordered">
                <tbody>
                    <tr><th style="width: 200px;">Category</th><td>{{ $product->category ?? 'N/A' }}</td></tr>
                    <tr><th>Reorder Level</th><td>{{ $product->reorder_level }}</td></tr>
                    <tr><th>Supplier</th><td>{{ $product->supplier ?? 'N/A' }}</td></tr>
                    <tr><th>Description</th><td>{{ $product->description ?? 'N/A' }}</td></tr>
                    <tr><th>Last Updated</th><td>{{ $product->updated_at?->format('Y-m-d H:i') }}</td></tr>
                </tbody>
            </table>

            <h5 class="fw-bold mt-4 mb-3">Stock Transactions</h5>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Type</th>
                            <th>Quantity</th>
                            <th>Reference</th>
                            <th>User</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($transactions as $transaction)
                            <tr>
                                <td>{{ $transaction->created_at?->format('Y-m-d H:i') }}</td>
                                <td>
                                    <span class="badge {{ $transaction->type === 'stock_in' ? 'bg-success' : 'bg-danger' }}">
                                        {{ $transaction->type === 'stock_in' ? 'Stock In' : 'Stock Out' }}
                                    </span>
                                </td>
                                <td>{{ $transaction->quantity }}</td>
                                <td>{{ $transaction->reference_number ?? '—' }}</td>
                                <td>{{ $transaction->user?->name ?? '—' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-3">No transactions recorded yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4 d-flex gap-2">
                <a href="{{ route('inventory.products.edit', $product->id) }}" class="btn btn-primary-custom">Edit Product</a>
                <a href="{{ route('inventory.index') }}" class="btn btn-secondary">Back to Inventory</a>
            </div>

        </div>
    </div>
</div>
</body>
</html>
