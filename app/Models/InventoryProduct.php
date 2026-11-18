<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;

/**
 * Inventory Product Model
 *
 * NOTE: The existing `Product` model is the Student Portal activity model.
 * This model represents the REAL store inventory (the `inventory_products` table).
 */
class InventoryProduct extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'sku',
        'description',
        'category',
        'quantity',
        'reorder_level',
        'unit_price',
        'supplier',
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'integer',
            'reorder_level' => 'integer',
            'unit_price' => 'decimal:2',
        ];
    }

    /**
     * Every inventory product can have many stock movement transactions.
     */
    public function transactions(): HasMany
    {
        return $this->hasMany(InventoryTransaction::class);
    }

    /**
     * Low stock = quantity is at or below the reorder level.
     */
    public function scopeLowStock(Builder $query): Builder
    {
        return $query->whereColumn('quantity', '<=', 'reorder_level');
    }

    /**
     * Out of stock = zero quantity.
     */
    public function scopeOutOfStock(Builder $query): Builder
    {
        return $query->where('quantity', 0);
    }

    /**
     * Healthy stock = above the reorder level.
     */
    public function scopeInStock(Builder $query): Builder
    {
        return $query->where('quantity', '>', 0)
            ->whereColumn('quantity', '>', 'reorder_level');
    }

    /**
     * Business-logic helpers used by the views and controllers.
     */
    public function isLowStock(): bool
    {
        return $this->quantity > 0 && $this->quantity <= $this->reorder_level;
    }

    public function isOutOfStock(): bool
    {
        return (int) $this->quantity === 0;
    }

    public function getStatusLabelAttribute(): string
    {
        if ($this->isOutOfStock()) {
            return 'Out of Stock';
        }

        if ($this->isLowStock()) {
            return 'Low Stock';
        }

        return 'In Stock';
    }

    public function getTotalValueAttribute(): float
    {
        return round((float) $this->quantity * (float) $this->unit_price, 2);
    }
}
