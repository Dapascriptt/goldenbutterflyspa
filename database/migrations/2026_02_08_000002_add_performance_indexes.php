<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('inventories')) {
            Schema::table('inventories', function (Blueprint $table) {
                $table->index('name', 'inventories_name_idx');
            });
        }

        if (Schema::hasTable('inventory_period_stocks')) {
            Schema::table('inventory_period_stocks', function (Blueprint $table) {
                $table->index(['period_year', 'period_month', 'inventory_id'], 'inv_period_lookup_idx');
            });
        }

        if (Schema::hasTable('inventory_movements')) {
            Schema::table('inventory_movements', function (Blueprint $table) {
                $table->index(['inventory_id', 'period_year', 'period_month', 'movement_date'], 'inv_movements_period_idx');
            });
        }

        if (Schema::hasTable('therapist_charges')) {
            Schema::table('therapist_charges', function (Blueprint $table) {
                $table->index('date', 'therapist_charges_date_idx');
                $table->index('therapist_name', 'therapist_charges_name_idx');
            });
        }

        if (Schema::hasTable('omsets')) {
            Schema::table('omsets', function (Blueprint $table) {
                if (Schema::hasColumn('omsets', 'date')) {
                    $table->index('date', 'omsets_date_idx');
                } elseif (Schema::hasColumn('omsets', 'created_at')) {
                    $table->index('created_at', 'omsets_created_at_idx');
                }

                if (Schema::hasColumn('omsets', 'created_by')) {
                    $table->index('created_by', 'omsets_created_by_idx');
                }

                if (Schema::hasColumn('omsets', 'code')) {
                    $table->index('code', 'omsets_code_idx');
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('inventories')) {
            Schema::table('inventories', function (Blueprint $table) {
                $table->dropIndex('inventories_name_idx');
            });
        }

        if (Schema::hasTable('inventory_period_stocks')) {
            Schema::table('inventory_period_stocks', function (Blueprint $table) {
                $table->dropIndex('inv_period_lookup_idx');
            });
        }

        if (Schema::hasTable('inventory_movements')) {
            Schema::table('inventory_movements', function (Blueprint $table) {
                $table->dropIndex('inv_movements_period_idx');
            });
        }

        if (Schema::hasTable('therapist_charges')) {
            Schema::table('therapist_charges', function (Blueprint $table) {
                $table->dropIndex('therapist_charges_date_idx');
                $table->dropIndex('therapist_charges_name_idx');
            });
        }

        if (Schema::hasTable('omsets')) {
            Schema::table('omsets', function (Blueprint $table) {
                if (Schema::hasColumn('omsets', 'date')) {
                    $table->dropIndex('omsets_date_idx');
                } elseif (Schema::hasColumn('omsets', 'created_at')) {
                    $table->dropIndex('omsets_created_at_idx');
                }

                if (Schema::hasColumn('omsets', 'created_by')) {
                    $table->dropIndex('omsets_created_by_idx');
                }

                if (Schema::hasColumn('omsets', 'code')) {
                    $table->dropIndex('omsets_code_idx');
                }
            });
        }
    }
};
