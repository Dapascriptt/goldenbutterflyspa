<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $todayKey = Carbon::now()->format('Ymd');

        $metrics = Cache::remember("dashboard:omset:metrics:$todayKey", 180, function () {
            $hasTherapist = Schema::hasTable('therapist_charges');
            $hasOmset = Schema::hasTable('omsets');

            if (!$hasTherapist && !$hasOmset) {
                return [
                    'today_total' => 0,
                    'week_total' => 0,
                    'month_total' => 0,
                    'today_count' => 0,
                    'inventory_total' => 0,
                    'inventory_low' => 0,
                    'therapist_total' => 0,
                    'treatment_today' => 0,
                ];
            }

            $today = Carbon::now()->toDateString();
            $weekStart = Carbon::now()->startOfWeek()->toDateString();
            $monthStart = Carbon::now()->startOfMonth()->toDateString();

            if ($hasTherapist) {
                $base = DB::table('therapist_charges');
                $todayTotal = (clone $base)->whereDate('date', $today)->sum('total_charge');
                $weekTotal = (clone $base)->whereDate('date', '>=', $weekStart)->sum('total_charge');
                $monthTotal = (clone $base)->whereDate('date', '>=', $monthStart)->sum('total_charge');
                $todayCount = (clone $base)->whereDate('date', $today)->count();
                $treatmentToday = $todayCount;
            } else {
                $dateColumn = Schema::hasColumn('omsets', 'date') ? 'date' : 'created_at';
                $amountColumn = Schema::hasColumn('omsets', 'amount') ? 'amount' : (Schema::hasColumn('omsets', 'total') ? 'total' : 'amount');
                $base = DB::table('omsets');
                $todayTotal = (clone $base)->whereDate($dateColumn, $today)->sum($amountColumn);
                $weekTotal = (clone $base)->whereDate($dateColumn, '>=', $weekStart)->sum($amountColumn);
                $monthTotal = (clone $base)->whereDate($dateColumn, '>=', $monthStart)->sum($amountColumn);
                $todayCount = (clone $base)->whereDate($dateColumn, $today)->count();
                $treatmentToday = 0;
            }

            $inventoryTotal = Schema::hasTable('inventories')
                ? DB::table('inventories')->count()
                : 0;

            $inventoryLow = Schema::hasTable('inventories')
                ? DB::table('inventories')->where('stock_akhir', '<=', 5)->count()
                : 0;

            $therapistTotal = Schema::hasTable('therapist_names')
                ? DB::table('therapist_names')->where('active', 1)->count()
                : 0;

            return [
                'today_total' => (int) $todayTotal,
                'week_total' => (int) $weekTotal,
                'month_total' => (int) $monthTotal,
                'today_count' => (int) $todayCount,
                'inventory_total' => (int) $inventoryTotal,
                'inventory_low' => (int) $inventoryLow,
                'therapist_total' => (int) $therapistTotal,
                'treatment_today' => (int) $treatmentToday,
            ];
        });

        $chart = Cache::remember("dashboard:omset:chart30d:$todayKey", 1200, function () {
            $start = Carbon::now()->startOfMonth()->toDateString();
            $end = Carbon::now()->endOfMonth()->toDateString();

            if (Schema::hasTable('therapist_charges')) {
                return DB::table('therapist_charges')
                    ->whereBetween('date', [$start, $end])
                    ->selectRaw("DATE(date) as date, SUM(total_charge) as total")
                    ->groupByRaw("DATE(date)")
                    ->orderBy('date')
                    ->get()
                    ->map(fn ($row) => ['date' => $row->date, 'total' => (int) $row->total])
                    ->all();
            }

            if (Schema::hasTable('omsets')) {
                $dateColumn = Schema::hasColumn('omsets', 'date') ? 'date' : 'created_at';
                $amountColumn = Schema::hasColumn('omsets', 'amount') ? 'amount' : (Schema::hasColumn('omsets', 'total') ? 'total' : 'amount');

                return DB::table('omsets')
                    ->whereDate($dateColumn, '>=', $start)
                    ->whereDate($dateColumn, '<=', $end)
                    ->selectRaw("DATE($dateColumn) as date, SUM($amountColumn) as total")
                    ->groupByRaw("DATE($dateColumn)")
                    ->orderBy('date')
                    ->get()
                    ->map(fn ($row) => ['date' => $row->date, 'total' => (int) $row->total])
                    ->all();
            }

            return [];
        });

        $donut = Cache::remember("dashboard:omset:donut30d:$todayKey", 1200, function () {
            if (!Schema::hasTable('therapist_charges')) {
                return ['labels' => [], 'data' => []];
            }

            $rows = DB::table('therapist_charges')
                ->whereDate('date', '>=', Carbon::now()->subDays(29)->toDateString())
                ->selectRaw("therapist_name, SUM(total_charge) as total")
                ->groupBy('therapist_name')
                ->orderByDesc('total')
                ->get();

            $labels = [];
            $data = [];
            $other = 0;
            foreach ($rows as $index => $row) {
                if ($index < 8) {
                    $labels[] = $row->therapist_name;
                    $data[] = (int) $row->total;
                } else {
                    $other += (int) $row->total;
                }
            }
            if ($other > 0) {
                $labels[] = 'Lainnya';
                $data[] = $other;
            }

            return ['labels' => $labels, 'data' => $data];
        });

        return view('dashboard.index', [
            'metrics' => $metrics,
            'chart' => $chart,
            'donut' => $donut,
        ]);
    }
}
