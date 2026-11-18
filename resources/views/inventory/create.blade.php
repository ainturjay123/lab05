<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Add Inventory Product</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="{{ asset('css/lab4-style.css') }}" rel="stylesheet">
</head>
<body>
<div class="container py-5">
    <div class="card shadow-sm mx-auto" style="max-width: 760px;">
        <div class="card-body p-4">
            <h2 class="mb-1 fw-bold text-primary-custom">Add Inventory Product</h2>
            <p class="text-muted small mb-4">Laboratory 5 — MVC Inventory Management System</p>

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

            <form action="{{ route('inventory.products.store') }}" method="POST">
                @csrf

                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="name" class="form-label">Product Name</label>
                        <input class="form-control" id="name" name="name"
                               value="{{ old('name') }}" required>
                    </div>

                    <div class="col-md-6">
                        <label for="sku" class="form-label">SKU (unique)</label>
                        <input class="form-control" id="sku" name="sku"
                               value="{{ old('sku') }}" required>
                    </div>

                    <div class="col-md-6">
                        <label for="category" class="form-label">Category</label>
                        <select class="form-select" id="category" name="category">
                            <option value="">Choose a category</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category }}"
                                    @selected(old('category') === $category)>{{ $category }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label for="supplier" class="form-label">Supplier</label>
                        <input class="form-control" id="supplier" name="supplier"
                               value="{{ old('supplier') }}">
                    </div>

                    <div class="col-12">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" id="description" name="description"
                                  rows="3" maxlength="1000">{{ old('description') }}</textarea>
                    </div>

                    <div class="col-md-4">
                        <label for="quantity" class="form-label">Quantity</label>
                        <input type="number" class="form-control" id="quantity" name="quantity"
                               min="0" value="{{ old('quantity', 0) }}" required>
                    </div>

                    <div class="col-md-4">
                        <label for="reorder_level" class="form-label">Reorder Level</label>
                        <input type="number" class="form-control" id="reorder_level" name="reorder_level"
                               min="0" value="{{ old('reorder_level', 10) }}" required>
                    </div>

                    <div class="col-md-4">
                        <label for="unit_price" class="form-label">Unit Price</label>
                        <input type="number" class="form-control" id="unit_price" name="unit_price"
                               min="0" step="0.01" value="{{ old('unit_price', 0) }}" required>
                    </div>
                </div>

                <div class="mt-4 d-flex gap-2">
                    <button type="submit" class="btn btn-primary-custom">Save Product</button>
                    <a href="{{ route('inventory.index') }}" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
</body>
</html>
