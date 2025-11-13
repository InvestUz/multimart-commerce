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
        Schema::create('banners', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('image_path');
            $table->string('link')->nullable();
            $table->enum('type', ['slider', 'promotional', 'category', 'vendor'])->default('slider');
            $table->enum('position', ['home_slider', 'home_top', 'home_middle', 'home_bottom', 'sidebar'])->default('home_slider');
            $table->string('button_text')->nullable()->default('Shop Now');
            $table->integer('order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->integer('clicks')->default(0);

            $table->dateTime('start_date')->nullable();
            $table->dateTime('end_date')->nullable();
            $table->timestamps();

            $table->index(['type', 'is_active']);
            $table->index('position');
            $table->index('order');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('banners');
    }
};
