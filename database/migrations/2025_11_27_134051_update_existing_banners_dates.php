<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Update existing banners to populate date fields
        DB::table('banners')->whereNull('start_date')->update([
            'start_date' => DB::raw('starts_at'),
            'end_date' => DB::raw('expires_at')
        ]);
        
        // For any remaining null values, set default dates
        DB::table('banners')->whereNull('start_date')->update([
            'start_date' => now(),
            'end_date' => now()->addYear()
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No need to reverse this migration
    }
};