@extends('layouts.app')

@section('title', 'Dashboard')
@section('page_title', 'Beranda')

@section('content')
    <div class="grid gap-6 md:grid-cols-2 xl:grid-cols-4">
        <div class="rounded-2xl bg-white border border-[#eadfce] p-5">
            <div class="text-sm text-slate-500">Total Omset</div>
            <div class="mt-2 text-2xl font-semibold text-[#4b2f1a]">
                Rp {{ number_format($metrics['today_total'] ?? 0, 0, ',', '.') }}
            </div>
            <div class="mt-3 text-xs text-slate-400">Hari ini</div>
        </div>
        <div class="rounded-2xl bg-white border border-[#eadfce] p-5">
            <div class="text-sm text-slate-500">Transaksi Hari Ini</div>
            <div class="mt-2 text-2xl font-semibold text-[#4b2f1a]">{{ $metrics['today_count'] ?? 0 }}</div>
            <div class="mt-3 text-xs text-slate-400">Jumlah entry</div>
        </div>
        <div class="rounded-2xl bg-white border border-[#eadfce] p-5">
            <div class="text-sm text-slate-500">Omset Minggu Ini</div>
            <div class="mt-2 text-2xl font-semibold text-[#4b2f1a]">
                Rp {{ number_format($metrics['week_total'] ?? 0, 0, ',', '.') }}
            </div>
            <div class="mt-3 text-xs text-slate-400">7 hari terakhir</div>
        </div>
        <div class="rounded-2xl bg-white border border-[#eadfce] p-5">
            <div class="text-sm text-slate-500">Omset Bulan Ini</div>
            <div class="mt-2 text-2xl font-semibold text-[#4b2f1a]">
                Rp {{ number_format($metrics['month_total'] ?? 0, 0, ',', '.') }}
            </div>
            <div class="mt-3 text-xs text-slate-400">Akumulasi bulan berjalan</div>
        </div>
    </div>

    <div class="mt-8 rounded-2xl bg-white border border-[#eadfce] p-6">
        <h2 class="text-lg font-semibold text-[#4b2f1a]">Grafik Omset 30 Hari</h2>
        <p class="mt-2 text-sm text-slate-500">Ringkasan per hari dari 30 hari terakhir.</p>
        <div class="mt-4 h-64">
            <canvas id="omsetChart" height="120" data-series='@json($chart ?? [])'></canvas>
        </div>
    </div>
@endsection

@push('scripts')
    @vite('resources/js/pages/dashboard-omset.js')
@endpush
