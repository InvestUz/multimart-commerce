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
        Schema::table('orders', function (Blueprint $table) {
            // Change the enum to include Uzum and Click payment methods
            $table->enum('payment_method', [
                'cash_on_delivery',
                'credit_card',
                'paypal',
                'bank_transfer',
                'Uzum',
                'Click'
            ])->default('cash_on_delivery')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->enum('payment_method', [
                'cash_on_delivery',
                'credit_card',
                'paypal',
                'bank_transfer'
            ])->default('cash_on_delivery')->change();
        });
    }
};
