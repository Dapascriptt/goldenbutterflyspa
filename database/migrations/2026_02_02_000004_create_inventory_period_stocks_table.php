<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventory_period_stocks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inventory_id')->constrained('inventories')->cascadeOnDelete();
            $table->unsignedSmallInteger('period_year');
            $table->unsignedTinyInteger('period_month');
            $table->unsignedInteger('stock_awal')->default(0);
            $table->unsignedInteger('stock_akhir')->default(0);
            $table->timestamps();

            $table->unique(['inventory_id', 'period_year', 'period_month'], 'inventory_period_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventory_period_stocks');
    }
};
