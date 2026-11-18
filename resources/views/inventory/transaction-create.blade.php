<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Record Stock Transaction</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="{{ asset('css/lab4-style.css') }}" rel="stylesheet">
</head>
<body>
<div class="container py-5">
    <div class="card shadow-sm mx-auto" style="max-width: 760px;">
        <div class="card-body p-4">

            <h2 class="mb-1 fw-bold text-primary-custom">Record Stock Transaction</h2>
            <p class="text-muted small mb-4">
                Laboratory 5 — MVC Inventory Management System.
                Stock quantities are updated automatically when a transaction is saved.
            </p>

            @if ($errors->any())
                <div class="alert alert-danger">
                    <strong>Please check the following:</strong>
                    <ul class="mb-0 mt-2">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('inventory.transactions.store') }}" method="POST">
                @csrf

                <div class="row g-3">
                    <div class="col-12">
                        <label for="inventory_product_id" class="form-label">Product</label>
                        <select id="inventory_product_id" name="inventory_product_id"
                                class="form-select" required>
                            <option value="">Select a product</option>
                            @foreach ($allProducts as $product)
                                <option value="{{ $product->id }}"
                                    @selected(old('inventory_product_id') == $product->id)>
                                    {{ $product->name }} ({{ $product->sku }}) — Stock: {{ $product->quantity }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label for="type" class="form-label">Transaction Type</label>
                        <select id="type" name="type" class="form-select" required>
                            <option value="stock_in"
                                @selected(old('type', 'stock_in') === 'stock_in')>Stock In (add stock)</option>
                            <option value="stock_out"
                                @selected(old('type') === 'stock_out')>Stock Out (remove stock)</option>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label for="quantity" class="form-label">Quantity</label>
                        <input type="number" class="form-control" id="quantity" name="quantity"
                               min="1" value="{{ old('quantity') }}" required>
                    </div>

                    <div class="col-md-6">
                        <label for="reference_number" class="form-label">Reference Number</label>
                        <input type="text" class="form-control" id="reference_number"
                               name="reference_number" value="{{ old('reference_number') }}"
                               placeholder="e.g. PO-2026-001">
                    </div>

                    <div class="col-md-6">
                        <label for="notes" class="form-label">Notes</label>
                        <input type="text" class="form-control" id="notes" name="notes"
                               value="{{ old('notes') }}" placeholder="Optional note">
                    </div>
                </div>

                <div class="mt-4 d-flex gap-2">
                    <button type="submit" class="btn btn-success">Save Transaction</button>
                    <a href="{{ route('inventory.index') }}" class="btn btn-secondary">Cancel</a>
                </div>
            </form>

        </div>
    </div>
</div>
</body>
</html>
