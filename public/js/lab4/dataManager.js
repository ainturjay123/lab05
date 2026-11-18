/**
 * Lab 4 data manager — now reading from the Laravel/MySQL API.
 *
 * The original file fetched php/inventory_api.php and contained hardcoded
 * fallback products. Both are removed: all data comes from the database.
 */
class InventoryDataManager {
    constructor() {
        this.inventory = [];   // filtered products for the table
        this.allItems = [];    // all products for statistics and charts
        this.stats = {
            totalItems: 0,
            totalValue: '0.00',
            lowStockCount: 0,
            outOfStockCount: 0,
        };
        this.transactions = [];
        this.isLoaded = false;
    }

    /**
     * Fetch inventory data from Laravel.
     * Search/filtering happens in the database through the query string.
     */
    async loadInventory(filters = {}) {
        const params = new URLSearchParams();

        if (filters.searchTerm) params.set('search', filters.searchTerm);
        if (filters.category && filters.category !== 'all') params.set('category', filters.category);
        if (filters.status && filters.status !== 'all') params.set('status', filters.status);
        if (filters.minPrice) params.set('min_price', filters.minPrice);
        if (filters.maxPrice && Number.isFinite(filters.maxPrice)) params.set('max_price', filters.maxPrice);

        const url = window.inventoryConfig.apiUrl + (params.toString() ? '?' + params.toString() : '');

        try {
            const response = await fetch(url, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
            });

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const result = await response.json();

            if (!result.success || !Array.isArray(result.items)) {
                throw new Error('Invalid API response format');
            }

            this.inventory = result.items;
            this.allItems = result.all_items || [];
            this.stats = result.stats || this.stats;
            this.transactions = result.transactions || [];
            this.isLoaded = true;

            return this.inventory;
        } catch (error) {
            console.error('Failed to load inventory data from Laravel:', error);
            this.inventory = [];
            this.allItems = [];
            this.isLoaded = true;
            return this.inventory;
        }
    }

    getAllItems() {
        return this.inventory;
    }

    /** All products — used by charts so they do not change with filters. */
    getAllChartItems() {
        return this.allItems;
    }

    getStatistics() {
        return this.stats;
    }

    /** The server has already filtered the data. */
    filterAndSearch() {
        return this.inventory;
    }

    getTopProductsByValue(limit = 5) {
        const sorted = [...this.allItems].sort(
            (a, b) => (b.stock * b.price) - (a.stock * a.price)
        );
        return sorted.slice(0, limit);
    }

    getCategoryValueBreakdown() {
        const breakdown = {};
        this.allItems.forEach(item => {
            const value = item.stock * item.price;
            breakdown[item.category] = (breakdown[item.category] || 0) + value;
        });
        return breakdown;
    }

    getStockStatusCounts() {
        const counts = { 'In Stock': 0, 'Low Stock': 0, 'Out of Stock': 0 };
        this.allItems.forEach(item => {
            if (counts[item.status] !== undefined) {
                counts[item.status]++;
            }
        });
        return counts;
    }
}

// Export a global instance (same name used by the original Lab 4 code)
window.inventoryManager = new InventoryDataManager();
