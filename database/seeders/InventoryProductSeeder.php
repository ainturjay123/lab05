<?php

namespace Database\Seeders;

use App\Models\InventoryProduct;
use Illuminate\Database\Seeder;

/**
 * Hardware-store sample products for Laboratory Exercise 5.
 *
 * SAFE BY DESIGN:
 * - Uses firstOrCreate() with SKU as the lookup key, so running it twice
 *   will never create duplicate products.
 * - It never deletes or updates existing records.
 * - The Student Portal ProductSeeder is separate and is not called here.
 */
class InventoryProductSeeder extends Seeder
{
    public function run(): void
    {
        $products = [
            // ---------- Lumber ----------
            [
                'name' => 'Douglas Fir Stud 2x4 x 8ft',
                'sku' => 'LUM-2X4-001',
                'description' => 'Kiln-dried construction stud for framing walls and partitions.',
                'category' => 'Lumber',
                'quantity' => 120,
                'reorder_level' => 40,
                'unit_price' => 4.25,
                'supplier' => 'Timberline Supply Co.',
            ],
            [
                'name' => 'Plywood Sheet 3/4in 4x8',
                'sku' => 'LUM-PLY-002',
                'description' => 'Structural plywood sheathing for floors, walls and roofs.',
                'category' => 'Lumber',
                'quantity' => 35,
                'reorder_level' => 20,
                'unit_price' => 28.50,
                'supplier' => 'Timberline Supply Co.',
            ],
            [
                'name' => 'MDF Board 1/2in 4x8',
                'sku' => 'LUM-MDF-003',
                'description' => 'Medium-density fiberboard for cabinets and interior panels.',
                'category' => 'Lumber',
                'quantity' => 8,
                'reorder_level' => 15,
                'unit_price' => 22.00,
                'supplier' => 'Boardmart Depot',
            ],
            [
                'name' => 'Cedar Deck Board 5/4x6x8',
                'sku' => 'LUM-CED-004',
                'description' => 'Weather-resistant cedar board for outdoor decking.',
                'category' => 'Lumber',
                'quantity' => 0,
                'reorder_level' => 25,
                'unit_price' => 12.75,
                'supplier' => 'Timberline Supply Co.',
            ],

            // ---------- Hardware ----------
            [
                'name' => '16d Common Nails 5lb Box',
                'sku' => 'HDW-NAIL-005',
                'description' => 'Hot-dipped galvanized nails for general framing work.',
                'category' => 'Hardware',
                'quantity' => 200,
                'reorder_level' => 50,
                'unit_price' => 9.99,
                'supplier' => 'FastenCo Hardware',
            ],
            [
                'name' => 'Deck Screws #9 x 3in (1000 pcs)',
                'sku' => 'HDW-SCRW-006',
                'description' => 'Corrosion-resistant exterior deck screws with star drive.',
                'category' => 'Hardware',
                'quantity' => 12,
                'reorder_level' => 30,
                'unit_price' => 24.99,
                'supplier' => 'FastenCo Hardware',
            ],
            [
                'name' => 'Heavy Duty Door Hinge (Pair)',
                'sku' => 'HDW-HING-007',
                'description' => 'Ball-bearing steel hinges for interior and exterior doors.',
                'category' => 'Hardware',
                'quantity' => 60,
                'reorder_level' => 20,
                'unit_price' => 8.50,
                'supplier' => 'FastenCo Hardware',
            ],
            [
                'name' => 'Hex Bolts 1/2in x 3in (50 pcs)',
                'sku' => 'HDW-BOLT-008',
                'description' => 'Zinc-plated hex bolts for structural fastening.',
                'category' => 'Hardware',
                'quantity' => 45,
                'reorder_level' => 15,
                'unit_price' => 15.75,
                'supplier' => 'FastenCo Hardware',
            ],

            // ---------- Tools ----------
            [
                'name' => '20oz Claw Hammer',
                'sku' => 'TOOL-HAM-009',
                'description' => 'Forged steel claw hammer with shock-absorbing grip.',
                'category' => 'Tools',
                'quantity' => 25,
                'reorder_level' => 10,
                'unit_price' => 18.95,
                'supplier' => 'ProTool Distributors',
            ],
            [
                'name' => '20V Cordless Drill Kit',
                'sku' => 'TOOL-DRL-010',
                'description' => 'Brushless drill/driver with two batteries and charger.',
                'category' => 'Tools',
                'quantity' => 6,
                'reorder_level' => 8,
                'unit_price' => 129.00,
                'supplier' => 'ProTool Distributors',
            ],
            [
                'name' => '48in Aluminum Box Level',
                'sku' => 'TOOL-LVL-011',
                'description' => 'Professional box level with three vials.',
                'category' => 'Tools',
                'quantity' => 0,
                'reorder_level' => 5,
                'unit_price' => 34.50,
                'supplier' => 'ProTool Distributors',
            ],
            [
                'name' => '10in Circular Saw',
                'sku' => 'TOOL-SAW-012',
                'description' => '15-amp circular saw with magnesium shoe.',
                'category' => 'Tools',
                'quantity' => 18,
                'reorder_level' => 6,
                'unit_price' => 89.99,
                'supplier' => 'ProTool Distributors',
            ],

            // ---------- Electrical ----------
            [
                'name' => '12/2 Romex Cable 250ft',
                'sku' => 'ELE-CAB-013',
                'description' => 'Indoor NM-B copper branch circuit cable.',
                'category' => 'Electrical',
                'quantity' => 40,
                'reorder_level' => 15,
                'unit_price' => 68.00,
                'supplier' => 'VoltWorks Supply',
            ],
            [
                'name' => 'Duplex Outlet 15A (10 pcs)',
                'sku' => 'ELE-OUT-014',
                'description' => 'Tamper-resistant white duplex receptacles.',
                'category' => 'Electrical',
                'quantity' => 9,
                'reorder_level' => 20,
                'unit_price' => 12.40,
                'supplier' => 'VoltWorks Supply',
            ],
            [
                'name' => '20A Single Pole Circuit Breaker',
                'sku' => 'ELE-BRK-015',
                'description' => 'Plug-in breaker for 120V branch circuits.',
                'category' => 'Electrical',
                'quantity' => 30,
                'reorder_level' => 10,
                'unit_price' => 7.85,
                'supplier' => 'VoltWorks Supply',
            ],

            // ---------- Plumbing ----------
            [
                'name' => '1/2in PVC Pipe 10ft',
                'sku' => 'PLB-PVC-016',
                'description' => 'Schedule 40 PVC pipe for cold water lines.',
                'category' => 'Plumbing',
                'quantity' => 80,
                'reorder_level' => 25,
                'unit_price' => 5.60,
                'supplier' => 'FlowLine Plumbing',
            ],
            [
                'name' => '1/2in PVC 90deg Elbow (25 pcs)',
                'sku' => 'PLB-ELB-017',
                'description' => 'Schedule 40 slip-fit elbow fittings.',
                'category' => 'Plumbing',
                'quantity' => 0,
                'reorder_level' => 12,
                'unit_price' => 9.25,
                'supplier' => 'FlowLine Plumbing',
            ],
            [
                'name' => 'Single Handle Kitchen Faucet',
                'sku' => 'PLB-FAU-018',
                'description' => 'Chrome-finish single-lever faucet with sprayer.',
                'category' => 'Plumbing',
                'quantity' => 14,
                'reorder_level' => 6,
                'unit_price' => 55.00,
                'supplier' => 'FlowLine Plumbing',
            ],

            // ---------- Paint ----------
            [
                'name' => 'Interior Latex Paint 1 Gal (White)',
                'sku' => 'PNT-LTX-019',
                'description' => 'Low-VOC washable flat paint for interior walls.',
                'category' => 'Paint',
                'quantity' => 55,
                'reorder_level' => 20,
                'unit_price' => 32.50,
                'supplier' => 'ColorCoat Paints',
            ],
            [
                'name' => 'Oil-Based Primer 1 Gal',
                'sku' => 'PNT-PRM-020',
                'description' => 'Multi-surface primer for wood, metal and drywall.',
                'category' => 'Paint',
                'quantity' => 4,
                'reorder_level' => 10,
                'unit_price' => 26.75,
                'supplier' => 'ColorCoat Paints',
            ],
        ];

        foreach ($products as $product) {
            InventoryProduct::firstOrCreate(
                ['sku' => $product['sku']],
                $product
            );
        }
    }
}
