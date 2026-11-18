<?php

namespace Database\Seeders;

use App\Models\InventoryProduct;
use App\Models\InventoryTransaction;
use App\Models\User;
use Illuminate\Database\Seeder;

/**
 * Sample stock movements for Laboratory Exercise 5.
 *
 * SAFE BY DESIGN:
 * - Uses firstOrCreate() keyed on a stable reference number, so running the
 *   seeder twice never creates duplicates and never changes stock twice.
 * - Stock quantities are NOT edited here. The existing
 *   InventoryTransactionObserver applies the stock in/out movement.
 * - Stock-out amounts are deliberately small and always less than the
 *   seeded quantity, so no product can go negative.
 */
class InventoryTransactionSeeder extends Seeder
{
    public function run(): void
    {
        $userId = User::query()->min('id') ?? 1;

        $records = [
            // ---------- Stock In (purchases / restocks) ----------
            ['sku' => 'LUM-2X4-001', 'type' => 'stock_in', 'quantity' => 50,
             'reference' => 'PO-2026-001', 'notes' => 'Weekly lumber delivery'],

            ['sku' => 'LUM-PLY-002', 'type' => 'stock_in', 'quantity' => 20,
             'reference' => 'PO-2026-002', 'notes' => 'Plywood restock'],

            ['sku' => 'HDW-SCRW-006', 'type' => 'stock_in', 'quantity' => 30,
             'reference' => 'PO-2026-003', 'notes' => 'Deck screw restock'],

            ['sku' => 'TOOL-DRL-010', 'type' => 'stock_in', 'quantity' => 10,
             'reference' => 'PO-2026-004', 'notes' => 'Cordless drill kit delivery'],

            ['sku' => 'ELE-OUT-014', 'type' => 'stock_in', 'quantity' => 25,
             'reference' => 'PO-2026-005', 'notes' => 'Electrical outlet restock'],

            // ---------- Stock Out (sales / usage) ----------
            ['sku' => 'LUM-2X4-001', 'type' => 'stock_out', 'quantity' => 15,
             'reference' => 'SO-2026-001', 'notes' => 'Framing job issue'],

            ['sku' => 'HDW-NAIL-005', 'type' => 'stock_out', 'quantity' => 20,
             'reference' => 'SO-2026-002', 'notes' => 'Nails issued to carpentry'],

            ['sku' => 'TOOL-HAM-009', 'type' => 'stock_out', 'quantity' => 5,
             'reference' => 'SO-2026-003', 'notes' => 'Hammers for tool crib'],

            ['sku' => 'PNT-LTX-019', 'type' => 'stock_out', 'quantity' => 10,
             'reference' => 'SO-2026-004', 'notes' => 'Interior paint for job site'],
        ];

        foreach ($records as $record) {
            $product = InventoryProduct::where('sku', $record['sku'])->first();

            if (! $product) {
                // Skip safely if a product was removed — never create a
                // transaction pointing at a non-existent product.
                continue;
            }

            InventoryTransaction::firstOrCreate(
                ['reference_number' => $record['reference']],
                [
                    'inventory_product_id' => $product->id,
                    'user_id'              => $userId,
                    'type'                 => $record['type'],
                    'quantity'             => $record['quantity'],
                    'notes'                => $record['notes'],
                ]
            );
        }
    }
}
