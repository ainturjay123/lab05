/**
 * Lab 4 inventory UI — rendering logic preserved, data now from Laravel.
 * The original 10-second random stock simulation was removed because it
 * would corrupt real database records.
 */
class InventoryUI {
    constructor() {
        this.currentFilters = {
            category: 'all',
            status: 'all',
            minPrice: 0,
            maxPrice: Infinity,
            searchTerm: '',
        };
    }

    async init() {
        await this.reload();
        this.bindEvents();
    }

    /** Re-fetch filtered data from Laravel, then re-render everything. */
    async reload() {
        await window.inventoryManager.loadInventory(this.currentFilters);
        this.updateDashboard();
    }

    updateDashboard() {
        const manager = window.inventoryManager;
        const items = manager.getAllItems();
        const stats = manager.getStatistics();

        const totalItemsEl = document.getElementById('statTotalItems');
        const totalValueEl = document.getElementById('statTotalValue');
        const lowStockEl = document.getElementById('statLowStock');
        const outOfStockEl = document.getElementById('statOutOfStock');

        if (totalItemsEl) totalItemsEl.textContent = stats.totalItems;
        if (totalValueEl) totalValueEl.textContent = `$${stats.totalValue}`;
        if (lowStockEl) lowStockEl.textContent = stats.lowStockCount;
        if (outOfStockEl) outOfStockEl.textContent = stats.outOfStockCount;

        this.renderAlertBanner(stats.lowStockCount, stats.outOfStockCount);
        this.renderTable(items);

        // Charts always use all products, not just the filtered subset.
        if (window.inventoryCharts) {
            window.inventoryCharts.renderCharts(manager.getAllChartItems());
        }
    }

    escapeHtml(value) {
        const div = document.createElement('div');
        div.textContent = value === null || value === undefined ? '' : String(value);
        return div.innerHTML;
    }

    renderTable(items) {
        const tbody = document.getElementById('inventoryTableBody');
        if (!tbody) return;

        if (items.length === 0) {
            tbody.innerHTML = `<tr><td colspan="9" class="text-center py-4 text-muted">No matching inventory items found.</td></tr>`;
            return;
        }

        tbody.innerHTML = items.map(item => {
            let badgeClass = 'bg-success';
            if (item.status === 'Low Stock') badgeClass = 'bg-warning text-dark';
            if (item.status === 'Out of Stock') badgeClass = 'bg-danger';

            const rowClass = item.stock === 0
                ? 'table-danger'
                : (item.stock <= item.reorderLevel ? 'table-warning' : '');

            const totalValue = (item.stock * item.price).toFixed(2);
            const nameHtml = this.highlightText(item.name, this.currentFilters.searchTerm);
            const skuHtml = this.highlightText(item.sku, this.currentFilters.searchTerm);

            const showUrl = window.inventoryConfig.showBaseUrl + encodeURIComponent(item.id);
            const editUrl = window.inventoryConfig.editBaseUrl + encodeURIComponent(item.id) + '/edit';

            return `
                <tr class="${rowClass}">
                    <td>${skuHtml}</td>
                    <td><strong>${nameHtml}</strong></td>
                    <td>${this.escapeHtml(item.category)}</td>
                    <td>${item.stock} ${item.stock <= item.reorderLevel ? '<i class="bi bi-exclamation-triangle-fill text-warning"></i>' : ''}</td>
                    <td>$${Number(item.price).toFixed(2)}</td>
                    <td>$${totalValue}</td>
                    <td><span class="badge ${badgeClass}">${this.escapeHtml(item.status)}</span></td>
                    <td>${this.escapeHtml(item.lastUpdated)}</td>
                    <td class="text-nowrap">
                        <a href="${showUrl}" class="btn btn-sm btn-outline-secondary" title="View">
                            <i class="bi bi-eye"></i>
                        </a>
                        <a href="${editUrl}" class="btn btn-sm btn-outline-primary" title="Edit">
                            <i class="bi bi-pencil"></i>
                        </a>
                    </td>
                </tr>
            `;
        }).join('');
    }

    highlightText(text, term) {
        const safe = this.escapeHtml(text);
        if (!term || term.trim() === '') return safe;

        const escapedTerm = term.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
        const regex = new RegExp(`(${escapedTerm})`, 'gi');
        return safe.replace(regex, '<mark class="bg-warning px-1 rounded">$1</mark>');
    }

    renderAlertBanner(lowStockCount, outOfStockCount) {
        const alertContainer = document.getElementById('inventoryAlertContainer');
        if (!alertContainer) return;

        if (lowStockCount > 0 || outOfStockCount > 0) {
            alertContainer.innerHTML = `
                <div class="alert alert-warning alert-dismissible fade show mb-4 shadow-sm" role="alert" style="background-color: #F3E3D7; border-color: #C08552; color: #5E3023;">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                    <strong>Inventory Alert:</strong> There are <strong>${lowStockCount}</strong> items at or below reorder level and <strong>${outOfStockCount}</strong> items out of stock!
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            `;
        } else {
            alertContainer.innerHTML = '';
        }
    }

    bindEvents() {
        const searchInput = document.getElementById('inventorySearchInput');
        const categoryFilter = document.getElementById('categoryFilter');
        const statusFilter = document.getElementById('statusFilter');
        const minPriceInput = document.getElementById('minPriceInput');
        const maxPriceInput = document.getElementById('maxPriceInput');
        const exportCsvBtn = document.getElementById('exportCsvBtn');

        if (searchInput) {
            searchInput.addEventListener('input', (e) => {
                this.currentFilters.searchTerm = e.target.value;
                this.reload();
            });
        }

        if (categoryFilter) {
            categoryFilter.addEventListener('change', (e) => {
                this.currentFilters.category = e.target.value;
                this.reload();
            });
        }

        if (statusFilter) {
            statusFilter.addEventListener('change', (e) => {
                this.currentFilters.status = e.target.value;
                this.reload();
            });
        }

        if (minPriceInput) {
            minPriceInput.addEventListener('input', (e) => {
                this.currentFilters.minPrice = e.target.value ? parseFloat(e.target.value) : 0;
                this.reload();
            });
        }

        if (maxPriceInput) {
            maxPriceInput.addEventListener('input', (e) => {
                this.currentFilters.maxPrice = e.target.value ? parseFloat(e.target.value) : Infinity;
                this.reload();
            });
        }

        if (exportCsvBtn) {
            exportCsvBtn.addEventListener('click', () => this.exportCSV());
        }
    }

    /** Ask Laravel for the CSV of the currently filtered products. */
    exportCSV() {
        const params = new URLSearchParams();
        const filters = this.currentFilters;

        if (filters.searchTerm) params.set('search', filters.searchTerm);
        if (filters.category !== 'all') params.set('category', filters.category);
        if (filters.status !== 'all') params.set('status', filters.status);
        if (filters.minPrice) params.set('min_price', filters.minPrice);
        if (Number.isFinite(filters.maxPrice)) params.set('max_price', filters.maxPrice);

        const query = params.toString();
        window.location.href = window.inventoryConfig.exportUrl + (query ? '?' + query : '');
    }
}

// Export global instance
window.inventoryUI = new InventoryUI();
