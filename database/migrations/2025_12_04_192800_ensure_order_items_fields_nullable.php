<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            // Make product_name and product_sku have defaults if they don't exist yet
            // This migration handles existing tables gracefully
            if (!Schema::hasColumn('order_items', 'product_name')) {
                $table->string('product_name')->default('Product');
            }
            if (!Schema::hasColumn('order_items', 'product_sku')) {
                $table->string('product_sku')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No need to rollback as we're just ensuring fields exist
    }
};
