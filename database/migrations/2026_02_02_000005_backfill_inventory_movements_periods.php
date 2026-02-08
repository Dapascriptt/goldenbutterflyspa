<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('inventory_movements')) {
            return;
        }

        if (
            !Schema::hasColumn('inventory_movements', 'movement_date') ||
            !Schema::hasColumn('inventory_movements', 'period_year') ||
            !Schema::hasColumn('inventory_movements', 'period_month')
        ) {
            return;
        }

        $now = now();
        DB::table('inventory_movements')
            ->whereNull('movement_date')
            ->update(['movement_date' => $now->toDateString()]);

        DB::table('inventory_movements')
            ->whereNull('period_year')
            ->update(['period_year' => (int) $now->format('Y')]);

        DB::table('inventory_movements')
            ->whereNull('period_month')
            ->update(['period_month' => (int) $now->format('m')]);
    }

    public function down(): void
    {
        // no-op
    }
};
