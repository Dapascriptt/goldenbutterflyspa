<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ArchiveController extends Controller
{
    public function index(Request $request)
    {
        $type = $request->input('type', 'omset');
        $year = (int) $request->input('year', now()->year);
        $month = (int) $request->input('month', now()->month);

        if ($month < 1 || $month > 12) {
            $month = now()->month;
        }

        $months = [
            1 => 'Januari',
            2 => 'Februari',
            3 => 'Maret',
            4 => 'April',
            5 => 'Mei',
            6 => 'Juni',
            7 => 'Juli',
            8 => 'Agustus',
            9 => 'September',
            10 => 'Oktober',
            11 => 'November',
            12 => 'Desember',
        ];

        $data = [
            'type' => $type,
            'year' => $year,
            'month' => $month,
            'months' => $months,
            'rows' => [],
            'rows_secondary' => [],
        ];

        if ($type === 'omset' && Schema::hasTable('omsets_archive')) {
            $rows = DB::table('omsets_archive')
                ->whereYear('date', $year)
                ->whereMonth('date', $month)
                ->orderBy('date', 'desc')
                ->limit(300)
                ->get();
            $data['rows'] = $rows;
        }

        if ($type === 'therapist' && Schema::hasTable('therapist_charges_archive')) {
            $rows = DB::table('therapist_charges_archive')
                ->whereYear('date', $year)
                ->whereMonth('date', $month)
                ->orderBy('date', 'desc')
                ->limit(300)
                ->get();
            $data['rows'] = $rows;
        }

        if ($type === 'inventory') {
            if (Schema::hasTable('inventory_movements_archive')) {
                $rows = DB::table('inventory_movements_archive')
                    ->whereYear('movement_date', $year)
                    ->whereMonth('movement_date', $month)
                    ->orderBy('movement_date', 'desc')
                    ->limit(300)
                    ->get();
                $data['rows'] = $rows;
            }

            if (Schema::hasTable('inventory_period_stocks_archive')) {
                $rowsSecondary = DB::table('inventory_period_stocks_archive')
                    ->where('period_year', $year)
                    ->where('period_month', $month)
                    ->orderBy('inventory_id')
                    ->limit(300)
                    ->get();
                $data['rows_secondary'] = $rowsSecondary;
            }
        }

        return view('archives.index', $data);
    }

    public function archiveNow(Request $request)
    {
        $months = (int) $request->input('months', 12);
        if ($months < 1) {
            $months = 12;
        }

        Artisan::call('archive:old-data', [
            '--months' => $months,
        ]);

        return back()->with('status', 'Arsip dijalankan.');
    }

    public function export(Request $request)
    {
        $type = $request->input('type', 'omset');
        $year = (int) $request->input('year', now()->year);
        $month = (int) $request->input('month', now()->month);
        $mode = $request->input('mode', 'movements');

        if (!in_array($type, ['omset', 'therapist', 'inventory'], true)) {
            return back()->withErrors(['type' => 'Tipe arsip tidak valid.']);
        }

        $filename = "arsip-{$type}-{$year}-".str_pad((string) $month, 2, '0', STR_PAD_LEFT).'.csv';

        return response()->streamDownload(function () use ($type, $year, $month, $mode) {
            $out = fopen('php://output', 'w');

            if ($type === 'omset') {
                if (!Schema::hasTable('omsets_archive')) {
                    fputcsv($out, ['Tabel omsets_archive belum ada']);
                    fclose($out);
                    return;
                }
                fputcsv($out, ['Tanggal', 'Kode', 'Deskripsi', 'Nominal', 'Dibuat Oleh']);
                DB::table('omsets_archive')
                    ->whereYear('date', $year)
                    ->whereMonth('date', $month)
                    ->orderBy('date', 'desc')
                    ->chunk(1000, function ($rows) use ($out) {
                        foreach ($rows as $row) {
                            fputcsv($out, [
                                Carbon::parse($row->date)->format('Y-m-d'),
                                $row->code ?? '-',
                                $row->description ?? '-',
                                $row->amount ?? 0,
                                $row->created_by ?? '-',
                            ]);
                        }
                    });
            } elseif ($type === 'therapist') {
                if (!Schema::hasTable('therapist_charges_archive')) {
                    fputcsv($out, ['Tabel therapist_charges_archive belum ada']);
                    fclose($out);
                    return;
                }
                fputcsv($out, [
                    'Tanggal', 'Nama Therapist', 'Waktu', 'Extra Time',
                    'Traditional 60', 'Full Body 90', 'Butterfly 90', 'Total Charge', 'Room',
                ]);
                DB::table('therapist_charges_archive')
                    ->whereYear('date', $year)
                    ->whereMonth('date', $month)
                    ->orderBy('date', 'desc')
                    ->chunk(1000, function ($rows) use ($out) {
                        foreach ($rows as $row) {
                            fputcsv($out, [
                                Carbon::parse($row->date)->format('Y-m-d'),
                                $row->therapist_name,
                                $row->time ?? '-',
                                $row->extra_time ?? 0,
                                $row->traditional ?? 0,
                                $row->fullbody ?? 0,
                                $row->butterfly ?? 0,
                                $row->total_charge ?? 0,
                                $row->room ?? '-',
                            ]);
                        }
                    });
            } else {
                if ($mode === 'periods') {
                    if (!Schema::hasTable('inventory_period_stocks_archive')) {
                        fputcsv($out, ['Tabel inventory_period_stocks_archive belum ada']);
                        fclose($out);
                        return;
                    }
                    fputcsv($out, ['Inventory ID', 'Tahun', 'Bulan', 'Stok Awal', 'Stok Akhir']);
                    DB::table('inventory_period_stocks_archive')
                        ->where('period_year', $year)
                        ->where('period_month', $month)
                        ->orderBy('inventory_id')
                        ->chunk(1000, function ($rows) use ($out) {
                            foreach ($rows as $row) {
                                fputcsv($out, [
                                    $row->inventory_id,
                                    $row->period_year,
                                    $row->period_month,
                                    $row->stock_awal ?? 0,
                                    $row->stock_akhir ?? 0,
                                ]);
                            }
                        });
                } else {
                    if (!Schema::hasTable('inventory_movements_archive')) {
                        fputcsv($out, ['Tabel inventory_movements_archive belum ada']);
                        fclose($out);
                        return;
                    }
                    fputcsv($out, ['Tanggal', 'Inventory ID', 'Tipe', 'Qty', 'Catatan']);
                    DB::table('inventory_movements_archive')
                        ->whereYear('movement_date', $year)
                        ->whereMonth('movement_date', $month)
                        ->orderBy('movement_date', 'desc')
                        ->chunk(1000, function ($rows) use ($out) {
                            foreach ($rows as $row) {
                                fputcsv($out, [
                                    Carbon::parse($row->movement_date)->format('Y-m-d'),
                                    $row->inventory_id,
                                    strtoupper($row->type ?? '-'),
                                    $row->qty ?? 0,
                                    $row->note ?? '-',
                                ]);
                            }
                        });
                }
            }

            fclose($out);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }
}
