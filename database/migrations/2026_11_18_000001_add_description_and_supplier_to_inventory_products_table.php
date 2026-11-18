<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Add the two Lab 5 fields that were missing from the existing
     * inventory_products table. This is ADDITIVE: no column or data is removed.
     */
    public function up(): void
    {
        Schema::table('inventory_products', function (Blueprint $table) {
            if (! Schema::hasColumn('inventory_products', 'description')) {
                $table->text('description')->nullable()->after('category');
            }

            if (! Schema::hasColumn('inventory_products', 'supplier')) {
                $table->string('supplier')->nullable()->after('unit_price');
            }
        });
    }

    public function down(): void
    {
        Schema::table('inventory_products', function (Blueprint $table) {
            $table->dropColumn(['description', 'supplier']);
        });
    }
};
