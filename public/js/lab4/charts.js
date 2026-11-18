class InventoryCharts {
    constructor() {
        this.categoryChart = null;
        this.statusChart = null;
        this.topProductsChart = null;

        // Custom color palette matching project theme
        this.themeColors = [
            '#800776', // Primary purple
            '#895737', // Secondary brown
            '#C08552', // Accent
            '#5E3023', // Dark text / brown
            '#DAB49D'  // Background / neutral
        ];
    }

    /**
     * Initialize or update all dashboard charts
     */
    renderCharts(items) {
        this.renderCategoryChart(items);
        this.renderStatusChart(items);
        this.renderTopProductsChart(items);
    }

    /**
     * 1. Inventory Value by Category (Bar Chart)
     */
    renderCategoryChart(items) {
        const ctx = document.getElementById('categoryValueChart');
        if (!ctx) return;

        // Aggregate value by category
        const categoryData = {};
        items.forEach(item => {
            const val = item.stock * item.price;
            categoryData[item.category] = (categoryData[item.category] || 0) + val;
        });

        const labels = Object.keys(categoryData);
        const data = Object.values(categoryData);

        if (this.categoryChart) {
            this.categoryChart.data.labels = labels;
            this.categoryChart.data.datasets[0].data = data;
            this.categoryChart.update();
            return;
        }

        this.categoryChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Total Value ($)',
                    data: data,
                    backgroundColor: this.themeColors[0],
                    borderColor: '#5E3023',
                    borderWidth: 1,
                    borderRadius: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { color: 'rgba(94, 48, 35, 0.1)' }
                    },
                    x: {
                        grid: { display: false }
                    }
                }
            }
        });
    }

    /**
     * 2. Stock Status Distribution (Doughnut Chart)
     */
    renderStatusChart(items) {
        const ctx = document.getElementById('stockStatusChart');
        if (!ctx) return;

        const statusCounts = { "In Stock": 0, "Low Stock": 0, "Out of Stock": 0 };
        items.forEach(item => {
            if (statusCounts[item.status] !== undefined) {
                statusCounts[item.status]++;
            }
        });

        const labels = Object.keys(statusCounts);
        const data = Object.values(statusCounts);

        if (this.statusChart) {
            this.statusChart.data.labels = labels;
            this.statusChart.data.datasets[0].data = data;
            this.statusChart.update();
            return;
        }

        this.statusChart = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: labels,
                datasets: [{
                    data: data,
                    backgroundColor: ['#800776', '#C08552', '#5E3023'],
                    borderWidth: 2,
                    borderColor: '#F3E3D7'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: { boxWidth: 12, font: { size: 12 } }
                    }
                }
            }
        });
    }

    /**
     * 3. Top 5 Products by Inventory Value (Horizontal Bar Chart)
     */
    renderTopProductsChart(items) {
        const ctx = document.getElementById('topProductsChart');
        if (!ctx) return;

        const sorted = [...items].sort((a, b) => (b.stock * b.price) - (a.stock * a.price)).slice(0, 5);
        const labels = sorted.map(item => item.name.length > 22 ? item.name.substring(0, 20) + '...' : item.name);
        const data = sorted.map(item => item.stock * item.price);

        if (this.topProductsChart) {
            this.topProductsChart.data.labels = labels;
            this.topProductsChart.data.datasets[0].data = data;
            this.topProductsChart.update();
            return;
        }

        this.topProductsChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Inventory Value ($)',
                    data: data,
                    backgroundColor: this.themeColors[1],
                    borderColor: '#5E3023',
                    borderWidth: 1,
                    borderRadius: 4
                }]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    x: {
                        beginAtZero: true,
                        grid: { color: 'rgba(94, 48, 35, 0.1)' }
                    },
                    y: {
                        grid: { display: false }
                    }
                }
            }
        });
    }
}

// Export a global instance
window.inventoryCharts = new InventoryCharts();