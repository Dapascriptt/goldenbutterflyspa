<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('inventory_movements', function (Blueprint $table) {
            if (!Schema::hasColumn('inventory_movements', 'movement_date')) {
                $table->date('movement_date')->nullable()->after('note');
            }
            if (!Schema::hasColumn('inventory_movements', 'period_year')) {
                $table->unsignedSmallInteger('period_year')->nullable()->after('movement_date');
            }
            if (!Schema::hasColumn('inventory_movements', 'period_month')) {
                $table->unsignedTinyInteger('period_month')->nullable()->after('period_year');
            }
        });
    }

    public function down(): void
    {
        Schema::table('inventory_movements', function (Blueprint $table) {
            if (Schema::hasColumn('inventory_movements', 'period_month')) {
                $table->dropColumn('period_month');
            }
            if (Schema::hasColumn('inventory_movements', 'period_year')) {
                $table->dropColumn('period_year');
            }
            if (Schema::hasColumn('inventory_movements', 'movement_date')) {
                $table->dropColumn('movement_date');
            }
        });
    }
};
