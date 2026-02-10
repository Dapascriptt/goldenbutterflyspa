<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventory_period_stocks_archive', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('inventory_id');
            $table->unsignedSmallInteger('period_year');
            $table->unsignedTinyInteger('period_month');
            $table->unsignedInteger('stock_awal')->default(0);
            $table->unsignedInteger('stock_akhir')->default(0);
            $table->timestamps();

            $table->index(['inventory_id', 'period_year', 'period_month'], 'inv_period_archive_lookup_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventory_period_stocks_archive');
    }
};
