<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Enhanced Inventory Management Dashboard</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <!-- Original Lab 4 Theme CSS -->
    <link rel="stylesheet" href="{{ asset('css/lab4-style.css') }}">
</head>
<body style="background-color: #DAB49D; color: #5E3023;">

    <!-- Top Navbar -->
    <nav class="navbar navbar-expand-lg px-4 shadow-sm" style="background-color: #895737;">
        <div class="container-fluid">
            <a class="navbar-brand fw-bold text-white" href="{{ route('dashboard') }}">Inventory & Academic Portal</a>
            <div class="d-flex align-items-center">
                <span class="text-white me-3" id="userWelcome">Welcome, {{ auth()->user()->name ?? 'Admin' }}</span>
                <form method="POST" action="{{ route('logout') }}" class="m-0">
                    @csrf
                    <button id="logoutBtn" type="submit" class="btn btn-outline-light btn-sm">Logout</button>
                </form>
            </div>
        </div>
    </nav>

    <div class="container-fluid">
        <div class="row">

            <!-- Sidebar Navigation -->
            <nav id="sidebar" class="col-md-3 col-lg-2 d-md-block sidebar collapse p-3" style="background-color: #800776; min-height: 100vh;">
                <div class="position-sticky">
                    <ul class="nav flex-column">
                        <li class="nav-item mb-2">
                            <a class="nav-link text-white active fw-bold rounded" style="background-color: #895737;" href="{{ route('dashboard') }}">
                                <i class="bi bi-speedometer2 me-2"></i> Dashboard
                            </a>
                        </li>
                        <li class="nav-item mb-2">
                            <a class="nav-link text-white" href="#inventorySection">
                                <i class="bi bi-box-seam me-2"></i> Inventory Items
                            </a>
                        </li>
                        <li class="nav-item mb-2">
                            <a class="nav-link text-white" href="#reportsSection">
                                <i class="bi bi-bar-chart-fill me-2"></i> Analytics & Charts
                            </a>
                        </li>
                        <li class="nav-item mb-2">
                            <a class="nav-link text-white" href="{{ route('inventory.index') }}">
                                <i class="bi bi-clipboard-data me-2"></i> Manage Inventory
                            </a>
                        </li>
                        <li class="nav-item mb-2">
                            <a class="nav-link text-white" href="{{ route('student-portal') }}">
                                <i class="bi bi-mortarboard me-2"></i> Student Portal
                            </a>
                        </li>
                        <li class="nav-item mt-4">
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="nav-link text-danger border-0 bg-transparent w-100 text-start" id="sidebarLogout">
                                    <i class="bi bi-box-arrow-right me-2"></i> Logout
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            </nav>

            <!-- Main Content Area -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">

                <!-- Greeting Header -->
                <div class="p-4 mb-4 rounded shadow-sm" style="background-color: #F3E3D7; border-left: 5px solid #800776;">
                    <h2 class="fw-bold mb-1" id="greetingTitle" style="color: #5E3023;">
                        Good {{ now()->hour >= 5 && now()->hour < 12 ? 'Morning' : (now()->hour >= 12 && now()->hour < 17 ? 'Afternoon' : (now()->hour >= 17 && now()->hour < 21 ? 'Evening' : 'Night')) }}, {{ auth()->user()->name ?? 'Admin' }}!
                    </h2>
                    <p class="text-muted mb-0">Manage hardware store inventory, monitor stock levels, and analyze inventory valuation in real time.</p>
                </div>

                @if (session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif

                <!-- Dynamic Low Stock Alert Container -->
                <div id="inventoryAlertContainer"></div>

                <!-- Statistics Cards Row -->
                <div class="row g-4 mb-4">
                    <div class="col-sm-6 col-xl-3">
                        <div class="card shadow-sm border-0 h-100" style="background-color: #F3E3D7;">
                            <div class="card-body text-center">
                                <h6 class="text-muted text-uppercase fw-semibold mb-2">Total Items</h6>
                                <h3 class="fw-bold mb-0" id="statTotalItems" style="color: #800776;">{{ $totalProducts }}</h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-xl-3">
                        <div class="card shadow-sm border-0 h-100" style="background-color: #F3E3D7;">
                            <div class="card-body text-center">
                                <h6 class="text-muted text-uppercase fw-semibold mb-2">Inventory Value</h6>
                                <h3 class="fw-bold mb-0" id="statTotalValue" style="color: #895737;">${{ number_format($inventoryValue, 2) }}</h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-xl-3">
                        <div class="card shadow-sm border-0 h-100" style="background-color: #F3E3D7;">
                            <div class="card-body text-center">
                                <h6 class="text-muted text-uppercase fw-semibold mb-2">Low Stock Items</h6>
                                <h3 class="fw-bold mb-0 text-warning" id="statLowStock">{{ $lowStockCount }}</h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-xl-3">
                        <div class="card shadow-sm border-0 h-100" style="background-color: #F3E3D7;">
                            <div class="card-body text-center">
                                <h6 class="text-muted text-uppercase fw-semibold mb-2">Out of Stock</h6>
                                <h3 class="fw-bold mb-0 text-danger" id="statOutOfStock">{{ $outOfStockCount }}</h3>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Analytics & Charts Section -->
                <div class="row g-4 mb-4" id="reportsSection">
                    <div class="col-lg-4">
                        <div class="card shadow-sm border-0 h-100" style="background-color: #F3E3D7;">
                            <div class="card-header bg-transparent fw-bold" style="color: #5E3023;">Inventory Value by Category</div>
                            <div class="card-body">
                                <div style="height: 250px;"><canvas id="categoryValueChart"></canvas></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="card shadow-sm border-0 h-100" style="background-color: #F3E3D7;">
                            <div class="card-header bg-transparent fw-bold" style="color: #5E3023;">Stock Status Distribution</div>
                            <div class="card-body">
                                <div style="height: 250px;"><canvas id="stockStatusChart"></canvas></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="card shadow-sm border-0 h-100" style="background-color: #F3E3D7;">
                            <div class="card-header bg-transparent fw-bold" style="color: #5E3023;">Top 5 Products by Value</div>
                            <div class="card-body">
                                <div style="height: 250px;"><canvas id="topProductsChart"></canvas></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Inventory Management Table & Filters Section -->
                <div class="card shadow-sm border-0 mb-4" id="inventorySection" style="background-color: #F3E3D7;">
                    <div class="card-header bg-transparent d-flex justify-content-between align-items-center py-3">
                        <h5 class="mb-0 fw-bold" style="color: #5E3023;"><i class="bi bi-table me-2"></i> Inventory Items Directory</h5>
                        <div class="d-flex gap-2">
                            <a href="{{ route('inventory.products.create') }}" class="btn btn-sm text-white" style="background-color: #800776;">
                                <i class="bi bi-plus-lg me-1"></i> Add Product
                            </a>
                            <button id="exportCsvBtn" class="btn btn-sm text-white" style="background-color: #800776;">
                                <i class="bi bi-download me-1"></i> Export CSV
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <!-- Search & Filters Toolbar -->
                        <div class="row g-3 mb-3">
                            <div class="col-md-4">
                                <div class="input-group">
                                    <span class="input-group-text bg-white" style="color: #5E3023;"><i class="bi bi-search"></i></span>
                                    <input type="text" id="inventorySearchInput" class="form-control" placeholder="Search by name or SKU...">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <select id="categoryFilter" class="form-select">
                                    <option value="all">All Categories</option>
                                    @foreach (['Lumber', 'Hardware', 'Tools', 'Electrical', 'Plumbing', 'Paint'] as $category)
                                        <option value="{{ $category }}">{{ $category }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <select id="statusFilter" class="form-select">
                                    <option value="all">All Statuses</option>
                                    <option value="In Stock">In Stock</option>
                                    <option value="Low Stock">Low Stock</option>
                                    <option value="Out of Stock">Out of Stock</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <input type="number" id="minPriceInput" class="form-control" placeholder="Min Price ($)" min="0">
                            </div>
                            <div class="col-md-2">
                                <input type="number" id="maxPriceInput" class="form-control" placeholder="Max Price ($)" min="0">
                            </div>
                        </div>

                        <!-- Inventory Table -->
                        <div class="table-responsive">
                            <table class="table table-hover align-middle bg-white rounded overflow-hidden">
                                <thead style="background-color: #895737; color: white;">
                                    <tr>
                                        <th>SKU</th>
                                        <th>Product Name</th>
                                        <th>Category</th>
                                        <th>Stock</th>
                                        <th>Unit Price</th>
                                        <th>Total Value</th>
                                        <th>Status</th>
                                        <th>Last Updated</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="inventoryTableBody">
                                    <!-- Rendered from MySQL by inventory.js -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Recent Transactions -->
                <div class="card shadow-sm border-0 mb-4" style="background-color: #F3E3D7;">
                    <div class="card-header bg-transparent fw-bold py-3" style="color: #5E3023;">
                        <i class="bi bi-arrow-left-right me-2"></i> Recent Inventory Transactions
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover bg-white">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Product</th>
                                        <th>Type</th>
                                        <th>Qty</th>
                                        <th>Reference</th>
                                        <th>User</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($recentTransactions as $transaction)
                                        <tr>
                                            <td>{{ $transaction->created_at?->format('Y-m-d H:i') }}</td>
                                            <td>{{ $transaction->inventoryProduct?->name ?? 'N/A' }}</td>
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
                                            <td colspan="6" class="text-center text-muted py-3">No transactions recorded yet.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Recently Added Products (server-rendered) -->
                <div class="card shadow-sm border-0 mb-4" style="background-color: #F3E3D7;">
                    <div class="card-header bg-transparent fw-bold py-3" style="color: #5E3023;">
                        <i class="bi bi-clock-history me-2"></i> Recently Added Products
                    </div>
                    <div class="card-body">
                        <ul class="list-unstyled mb-0">
                            @forelse ($recentProducts as $product)
                                <li class="border-bottom py-2">
                                    <a href="{{ route('inventory.products.show', $product->id) }}" class="fw-semibold">
                                        {{ $product->name }}
                                    </a>
                                    <span class="text-muted small">({{ $product->sku }})</span>
                                    <span class="float-end">{{ $product->quantity }} units</span>
                                </li>
                            @empty
                                <li class="text-muted">No products yet.</li>
                            @endforelse
                        </ul>
                    </div>
                </div>

            </main>
        </div>
    </div>

    <!-- Chart.js CDN -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Laravel route configuration for the Lab 4 JavaScript -->
    <script>
        window.inventoryConfig = {
            apiUrl: "{{ route('inventory.api.items') }}",
            exportUrl: "{{ route('inventory.export-csv') }}",
            showBaseUrl: "{{ url('/inventory/products') }}/",
            editBaseUrl: "{{ url('/inventory/products') }}/",
            username: @json(auth()->user()->name ?? 'Admin')
        };
    </script>

    <!-- Modular Application Scripts (original Lab 4 files, wired to Laravel) -->
    <script src="{{ asset('js/lab4/dataManager.js') }}"></script>
    <script src="{{ asset('js/lab4/charts.js') }}"></script>
    <script src="{{ asset('js/lab4/inventory.js') }}"></script>
    <script src="{{ asset('js/lab4/Dashboard.js') }}"></script>
</body>
</html>
