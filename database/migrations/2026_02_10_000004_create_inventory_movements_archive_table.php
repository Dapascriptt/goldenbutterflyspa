<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventory_movements_archive', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('inventory_id');
            $table->enum('type', ['in', 'out']);
            $table->unsignedInteger('qty');
            $table->string('note')->nullable();
            $table->date('movement_date');
            $table->unsignedSmallInteger('period_year');
            $table->unsignedTinyInteger('period_month');
            $table->timestamps();

            $table->index(['inventory_id', 'period_year', 'period_month'], 'inv_movements_archive_period_idx');
            $table->index('movement_date', 'inv_movements_archive_date_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventory_movements_archive');
    }
};
