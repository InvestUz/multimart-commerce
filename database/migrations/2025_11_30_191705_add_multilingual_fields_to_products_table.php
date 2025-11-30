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
        Schema::table('products', function (Blueprint $table) {
            // Add JSON fields for multilingual content
            $table->json('name_translations')->nullable();
            $table->json('description_translations')->nullable();
            $table->json('short_description_translations')->nullable();
            $table->json('meta_title_translations')->nullable();
            $table->json('meta_description_translations')->nullable();
            $table->json('meta_keywords_translations')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn([
                'name_translations',
                'description_translations',
                'short_description_translations',
                'meta_title_translations',
                'meta_description_translations',
                'meta_keywords_translations'
            ]);
        });
    }
};