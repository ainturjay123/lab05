@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">

        {{-- SIDEBAR --}}
        <aside class="col-md-3 col-lg-2 sidebar p-3">
            <h5 class="mb-4">Navigation</h5>

            <nav class="nav flex-column">
                <a href="{{ route('dashboard') }}"
                   class="nav-link active">🏠 Dashboard</a>

                <a href="#profileSettings"
                   class="nav-link">👤 Profile</a>

                <a href="#dashboardSettings"
                   class="nav-link">⚙️ Settings</a>

                <a href="{{ route('inventory.index') }}"
                   class="nav-link">📦 Inventory</a>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                            class="nav-link text-start w-100 border-0 bg-transparent">
                        🚪 Log Out
                    </button>
                </form>
            </nav>
        </aside>

        {{-- MAIN CONTENT --}}
        <main class="col-md-9 col-lg-10 p-4">

            {{-- PROFILE --}}
            <section id="profileSettings" class="mb-5">
                <h2 class="mb-4">Profile</h2>

                <div class="card">
                    <div class="card-body">
                        <h5 class="mb-4">Student Information</h5>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Student Name</label>
                                <input class="form-control"
                                       value="{{ auth()->user()->name ?? 'Admin' }}"
                                       readonly>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Student ID</label>
                                <input class="form-control"
                                       value="2026-001" readonly>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Academic Year</label>
                                <input class="form-control"
                                       value="2026-2027" readonly>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Year Level</label>
                                <input class="form-control"
                                       value="3rd Year" readonly>
                            </div>

                            <div class="col-12">
                                <label class="form-label">Program</label>
                                <input class="form-control"
                                       value="Bachelor of Science in Information Technology"
                                       readonly>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            {{-- DASHBOARD --}}
            <section id="dashboardSettings">
                <h2 class="mb-4">
                    Good {{ now()->hour >= 5 && now()->hour < 12 ? 'Morning' : (now()->hour < 17 && now()->hour >= 12 ? 'Afternoon' : (now()->hour < 21 && now()->hour >= 17 ? 'Evening' : 'Night')) }},
                    {{ auth()->user()->name ?? 'Admin' }}!
                </h2>

                {{-- STATISTICS --}}
                <div class="row g-4 mb-4">
                    <div class="col-md-6 col-xl-3">
                        <div class="card stat-card">
                            <div class="card-body">
                                <h6>🎓 GPA</h6>
                                <p class="card-text">{{ $gpa }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6 col-xl-3">
                        <div class="card stat-card">
                            <div class="card-body">
                                <h6>📚 Courses</h6>
                                <p class="card-text">{{ $totalCourses }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6 col-xl-3">
                        <div class="card stat-card">
                            <div class="card-body">
                                <h6>📝 Assignments</h6>
                                <p class="card-text">{{ $totalAssignments }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6 col-xl-3">
                        <div class="card stat-card">
                            <div class="card-body">
                                <h6>📅 Attendance</h6>
                                <p class="card-text">{{ $attendance }}%</p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- PERFORMANCE ALERT --}}
                @if($lowPerformanceCount > 0)
                    <div class="alert alert-warning mb-4">
                        <strong>Performance Alert:</strong>
                        {{ $lowPerformanceCount }} scored activity item(s)
                        are below 90. Review the highlighted rows.
                    </div>
                @endif

                {{-- SEARCH AND FILTERS --}}
                <div class="card mb-4">
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label for="searchInput" class="form-label">
                                    Search
                                </label>
                                <input id="searchInput"
                                       class="form-control"
                                       placeholder="Search activities...">
                            </div>

                            <div class="col-md-3">
                                <label for="categoryFilter" class="form-label">
                                    Category
                                </label>
                                <select id="categoryFilter"
                                        class="form-select">
                                    <option value="all">All Categories</option>
                                    @foreach($products->pluck('category')->filter()->unique()->sort() as $category)
                                        <option value="{{ $category }}">
                                            {{ $category }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-3">
                                <label for="statusFilter" class="form-label">
                                    Status
                                </label>
                                <select id="statusFilter" class="form-select">
                                    <option value="all">All Status</option>
                                    <option value="completed">Completed</option>
                                    <option value="pending">Pending</option>
                                    <option value="overdue">Overdue</option>
                                </select>
                            </div>

                            <div class="col-md-2 d-flex align-items-end">
                                <button id="resetFilters"
                                        class="btn btn-secondary w-100">
                                    Reset
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- CHARTS --}}
                <div class="row g-4 mb-4">
                    <div class="col-lg-6">
                        <div class="card chart-card">
                            <div class="card-body">
                                <h5>Course Performance</h5>
                                <div class="chart-container">
                                    <canvas id="performanceChart"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-6">
                        <div class="card chart-card">
                            <div class="card-body">
                                <h5>Assignment Status</h5>
                                <div class="chart-container">
                                    <canvas id="assignmentChart"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-12">
                        <div class="card chart-card">
                            <div class="card-body">
                                <h5>Attendance</h5>
                                <div class="chart-wide">
                                    <canvas id="attendanceChart"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- STUDENT ACTIVITIES --}}
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between
                                    align-items-center mb-3">
                            <h5 class="mb-0">Student Activities</h5>

                            <div>
                                <span class="me-3">
                                    Results:
                                    <strong id="resultCount">
                                        {{ $products->count() }}
                                    </strong>
                                </span>
                                <button id="exportCsvBtn"
                                        class="btn btn-sm btn-primary">
                                    Export CSV
                                </button>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Category</th>
                                        <th>Activity</th>
                                        <th>Status</th>
                                        <th>Score</th>
                                    </tr>
                                </thead>

                                <tbody id="activityTableBody">
                                    @foreach($products as $product)
                                        <tr class="{{ $product->score !== null && $product->score < 90 ? 'low-performance-row' : '' }}"
                                            data-category="{{ $product->category }}"
                                            data-status="{{ $product->status }}"
                                            data-search="{{ strtolower(($product->name ?? '') . ' ' . ($product->subject ?? '') . ' ' . ($product->category ?? '') . ' ' . ($product->status ?? '')) }}">

                                            <td>
                                                {{ $product->created_at?->format('Y-m-d') ?? 'N/A' }}
                                            </td>

                                            <td>{{ $product->category }}</td>

                                            <td>
                                                {{ $product->name ?? $product->subject ?? 'Student activity' }}
                                            </td>

                                            <td>
                                                <span class="badge
                                                    {{ $product->status === 'completed' ? 'bg-success' : ($product->status === 'overdue' ? 'bg-danger' : 'bg-warning text-dark') }}">
                                                    {{ $product->status }}
                                                </span>
                                            </td>

                                            <td>{{ $product->score ?? '—' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div id="emptyState"
                             class="text-center text-muted d-none">
                            No activities found.
                        </div>

                        <div class="text-end text-muted small mt-2">
                            Last Updated:
                            <span id="lastUpdated">{{ now()->format('h:i:s A') }}</span>
                        </div>
                    </div>
                </div>
            </section>
        </main>
    </div>
</div>

{{-- CHART.JS --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const categories = @json($categoryScores->keys()->values());
    const scores = @json($categoryScores->values());

    const completed = {{ $completedCount }};
    const pending = {{ $pendingCount }};
    const overdue = {{ $overdueCount }};

    const attendanceLabels = @json($attendanceLabels->values());
    const attendanceValues = @json($attendanceValues);

    new Chart(document.getElementById('performanceChart'), {
        type: 'bar',
        data: {
            labels: categories,
            datasets: [{
                label: 'Average Score',
                data: scores,
                backgroundColor: 'rgba(54, 162, 235, 0.55)',
                borderColor: '#36A2EB',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    max: 100
                }
            }
        }
    });

    new Chart(document.getElementById('assignmentChart'), {
        type: 'doughnut',
        data: {
            labels: ['Completed', 'Pending', 'Overdue'],
            datasets: [{
                data: [completed, pending, overdue],
                backgroundColor: ['#36A2EB', '#FF6384', '#FF9F40']
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'top'
                }
            }
        }
    });

    new Chart(document.getElementById('attendanceChart'), {
        type: 'line',
        data: {
            labels: attendanceLabels,
            datasets: [{
                label: 'Attendance %',
                data: attendanceValues,
                borderColor: '#36A2EB',
                backgroundColor: 'rgba(54, 162, 235, 0.15)',
                tension: 0.25,
                fill: false
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    max: 100
                }
            }
        }
    });

    // Search and filter activities.
    const searchInput = document.getElementById('searchInput');
    const categoryFilter = document.getElementById('categoryFilter');
    const statusFilter = document.getElementById('statusFilter');
    const rows = Array.from(
        document.querySelectorAll('#activityTableBody tr')
    );

    function filterRows() {
        const query = searchInput.value.toLowerCase().trim();
        const category = categoryFilter.value;
        const status = statusFilter.value;
        let visible = 0;

        rows.forEach(row => {
            const matchesSearch =
                row.dataset.search.includes(query);

            const matchesCategory =
                category === 'all' ||
                row.dataset.category === category;

            const matchesStatus =
                status === 'all' ||
                row.dataset.status === status;

            const show =
                matchesSearch &&
                matchesCategory &&
                matchesStatus;

            row.classList.toggle('d-none', !show);

            if (show) visible++;
        });

        document.getElementById('resultCount').textContent = visible;

        document.getElementById('emptyState')
            .classList.toggle('d-none', visible !== 0);
    }

    searchInput.addEventListener('input', filterRows);
    categoryFilter.addEventListener('change', filterRows);
    statusFilter.addEventListener('change', filterRows);

    document.getElementById('resetFilters')
        .addEventListener('click', function () {
            searchInput.value = '';
            categoryFilter.value = 'all';
            statusFilter.value = 'all';
            filterRows();
        });

    // Export currently visible rows to CSV.
    document.getElementById('exportCsvBtn')
        .addEventListener('click', function () {
            const visibleRows = rows.filter(
                row => !row.classList.contains('d-none')
            );

            const csvRows = [
                ['Date', 'Category', 'Activity', 'Status', 'Score']
            ];

            visibleRows.forEach(row => {
                csvRows.push(
                    Array.from(row.querySelectorAll('td'))
                        .map(cell => cell.innerText.trim())
                );
            });

            const csv = csvRows.map(row =>
                row.map(value =>
                    '"' + value.replaceAll('"', '""') + '"'
                ).join(',')
            ).join('\n');

            const blob = new Blob([csv], {
                type: 'text/csv;charset=utf-8;'
            });

            const url = URL.createObjectURL(blob);
            const link = document.createElement('a');

            link.href = url;
            link.download = 'student-activities.csv';
            link.click();

            URL.revokeObjectURL(url);
        });
});
</script>
@endsection
