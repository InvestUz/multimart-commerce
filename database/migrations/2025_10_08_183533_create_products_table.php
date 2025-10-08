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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Vendor
            $table->foreignId('category_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->decimal('price', 12, 2);
            $table->decimal('old_price', 12, 2)->nullable();
            $table->integer('discount_percentage')->default(0);
            $table->integer('stock')->default(0);
            $table->string('sku')->unique()->nullable();
            $table->json('sizes')->nullable(); // ["S", "M", "L", "XL"]
            $table->json('colors')->nullable(); // ["red", "blue", "green"]
            $table->json('specifications')->nullable(); // Additional product specs
            $table->string('brand')->nullable();
            $table->string('model')->nullable();
            $table->string('condition')->default('new'); // new, used, refurbished
            $table->decimal('weight', 8, 2)->nullable(); // in kg
            $table->string('dimensions')->nullable(); // LxWxH
            $table->boolean('is_active')->default(true);
            $table->boolean('is_featured')->default(false);
            $table->decimal('average_rating', 3, 2)->default(0);
            $table->integer('total_reviews')->default(0);
            $table->integer('views')->default(0);
            $table->integer('total_sales')->default(0);
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index(['user_id', 'is_active']);
            $table->index(['category_id', 'is_active']);
            $table->index('is_featured');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
