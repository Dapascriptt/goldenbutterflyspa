<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ArchiveOldData extends Command
{
    protected $signature = 'archive:old-data {--months=12 : Archive data older than N months}';
    protected $description = 'Archive omset, therapist, and inventory data older than N months into archive tables.';

    public function handle(): int
    {
        $months = (int) $this->option('months');
        if ($months < 1) {
            $months = 12;
        }

        $cutoff = Carbon::now()->subMonths($months)->startOfDay();
        $cutoffDate = $cutoff->toDateString();

        DB::transaction(function () use ($cutoffDate) {
            if (Schema::hasTable('omsets') && Schema::hasTable('omsets_archive')) {
                DB::statement(
                    'INSERT INTO omsets_archive (id, date, code, description, amount, created_by, created_at, updated_at)
                     SELECT o.id, o.date, o.code, o.description, o.amount, o.created_by, o.created_at, o.updated_at
                     FROM omsets o
                     LEFT JOIN omsets_archive a ON a.id = o.id
                     WHERE o.date < ? AND a.id IS NULL',
                    [$cutoffDate]
                );
                DB::table('omsets')->where('date', '<', $cutoffDate)->delete();
            }

            if (Schema::hasTable('therapist_charges') && Schema::hasTable('therapist_charges_archive')) {
                DB::statement(
                    'INSERT INTO therapist_charges_archive (id, date, time, therapist_name, extra_time, extra_charge, traditional, fullbody, butterfly, shockwave, discount_percent, discount_nominal, room_charge, total_charge, room, created_at, updated_at)
                     SELECT t.id, t.date, t.time, t.therapist_name, t.extra_time, t.extra_charge, t.traditional, t.fullbody, t.butterfly, t.shockwave, t.discount_percent, t.discount_nominal, t.room_charge, t.total_charge, t.room, t.created_at, t.updated_at
                     FROM therapist_charges t
                     LEFT JOIN therapist_charges_archive a ON a.id = t.id
                     WHERE t.date < ? AND a.id IS NULL',
                    [$cutoffDate]
                );
                DB::table('therapist_charges')->where('date', '<', $cutoffDate)->delete();
            }

            if (Schema::hasTable('inventory_movements') && Schema::hasTable('inventory_movements_archive')) {
                DB::statement(
                    'INSERT INTO inventory_movements_archive (id, inventory_id, type, qty, note, movement_date, period_year, period_month, created_at, updated_at)
                     SELECT m.id, m.inventory_id, m.type, m.qty, m.note, m.movement_date, m.period_year, m.period_month, m.created_at, m.updated_at
                     FROM inventory_movements m
                     LEFT JOIN inventory_movements_archive a ON a.id = m.id
                     WHERE m.movement_date < ? AND a.id IS NULL',
                    [$cutoffDate]
                );
                DB::table('inventory_movements')->where('movement_date', '<', $cutoffDate)->delete();
            }

            if (Schema::hasTable('inventory_period_stocks') && Schema::hasTable('inventory_period_stocks_archive')) {
                $cutoffYear = (int) Carbon::parse($cutoffDate)->format('Y');
                $cutoffMonth = (int) Carbon::parse($cutoffDate)->format('m');

                DB::statement(
                    'INSERT INTO inventory_period_stocks_archive (id, inventory_id, period_year, period_month, stock_awal, stock_akhir, created_at, updated_at)
                     SELECT p.id, p.inventory_id, p.period_year, p.period_month, p.stock_awal, p.stock_akhir, p.created_at, p.updated_at
                     FROM inventory_period_stocks p
                     LEFT JOIN inventory_period_stocks_archive a ON a.id = p.id
                     WHERE (p.period_year < ? OR (p.period_year = ? AND p.period_month < ?)) AND a.id IS NULL',
                    [$cutoffYear, $cutoffYear, $cutoffMonth]
                );
                DB::table('inventory_period_stocks')
                    ->where(function ($q) use ($cutoffYear, $cutoffMonth) {
                        $q->where('period_year', '<', $cutoffYear)
                          ->orWhere(function ($inner) use ($cutoffYear, $cutoffMonth) {
                              $inner->where('period_year', $cutoffYear)
                                    ->where('period_month', '<', $cutoffMonth);
                          });
                    })
                    ->delete();
            }
        });

        $this->info("Archive selesai. Cutoff: {$cutoffDate}");

        return self::SUCCESS;
    }
}
