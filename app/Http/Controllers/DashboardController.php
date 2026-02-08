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
            if (!Schema::hasTable('omsets')) {
                return [
                    'today_total' => 0,
                    'week_total' => 0,
                    'month_total' => 0,
                    'today_count' => 0,
                ];
            }

            $dateColumn = Schema::hasColumn('omsets', 'date') ? 'date' : 'created_at';
            $amountColumn = Schema::hasColumn('omsets', 'amount') ? 'amount' : (Schema::hasColumn('omsets', 'total') ? 'total' : 'amount');

            $today = Carbon::now()->toDateString();
            $weekStart = Carbon::now()->startOfWeek()->toDateString();
            $monthStart = Carbon::now()->startOfMonth()->toDateString();

            $base = DB::table('omsets');

            $todayTotal = (clone $base)->whereDate($dateColumn, $today)->sum($amountColumn);
            $weekTotal = (clone $base)->whereDate($dateColumn, '>=', $weekStart)->sum($amountColumn);
            $monthTotal = (clone $base)->whereDate($dateColumn, '>=', $monthStart)->sum($amountColumn);
            $todayCount = (clone $base)->whereDate($dateColumn, $today)->count();

            return [
                'today_total' => (int) $todayTotal,
                'week_total' => (int) $weekTotal,
                'month_total' => (int) $monthTotal,
                'today_count' => (int) $todayCount,
            ];
        });

        $chart = Cache::remember("dashboard:omset:chart30d:$todayKey", 1200, function () {
            if (!Schema::hasTable('omsets')) {
                return [];
            }

            $dateColumn = Schema::hasColumn('omsets', 'date') ? 'date' : 'created_at';
            $amountColumn = Schema::hasColumn('omsets', 'amount') ? 'amount' : (Schema::hasColumn('omsets', 'total') ? 'total' : 'amount');

            return DB::table('omsets')
                ->whereDate($dateColumn, '>=', Carbon::now()->subDays(29)->toDateString())
                ->selectRaw("DATE($dateColumn) as date, SUM($amountColumn) as total")
                ->groupByRaw("DATE($dateColumn)")
                ->orderBy('date')
                ->get()
                ->map(fn ($row) => ['date' => $row->date, 'total' => (int) $row->total])
                ->all();
        });

        return view('dashboard.index', [
            'metrics' => $metrics,
            'chart' => $chart,
        ]);
    }
}
