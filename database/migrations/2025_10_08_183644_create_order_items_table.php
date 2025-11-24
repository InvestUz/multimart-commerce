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
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->onDelete('cascade');
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->foreignId('vendor_id')->constrained('users')->onDelete('cascade');
            $table->string('product_name');
            $table->string('product_sku')->nullable();
            $table->integer('quantity');
            $table->decimal('price', 12, 2);
            $table->string('size')->nullable();
            $table->string('color')->nullable();
            $table->decimal('total', 12, 2);
            $table->enum('vendor_status', ['pending', 'processing', 'shipped', 'delivered'])->default('pending');
            $table->text('vendor_notes')->nullable();
            $table->foreignId('payout_id')->nullable()->onDelete('set null');

            $table->timestamps();

            $table->index('order_id');
            $table->index('vendor_id');
            $table->index('product_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
