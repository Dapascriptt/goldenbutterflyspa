@extends('layouts.app')

@section('title', 'Omset')
@section('page_title', 'Omset')

@section('content')
    <div class="rounded-2xl bg-white border border-[#eadfce] p-6">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <h2 class="text-lg font-semibold text-[#4b2f1a]">Data Omset</h2>
                <p class="text-sm text-slate-500">Halaman placeholder untuk modul omset.</p>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                @if (auth()->user()->isAdmin())
                    <a href="{{ route('manage.omset.export.excel') }}" class="px-3 py-2 rounded-lg bg-[#f7f2eb] text-[#9c7a4c] text-xs font-semibold border border-[#eadfce]">
                        Export Excel
                    </a>
                    <a href="{{ route('manage.omset.export.pdf') }}" class="px-3 py-2 rounded-lg bg-[#4b2f1a] text-white text-xs font-semibold">
                        Export PDF
                    </a>
                    <a href="{{ route('manage.omset.print') }}" class="px-3 py-2 rounded-lg border border-[#9c7a4c] text-[#9c7a4c] text-xs font-semibold">
                        Print A4
                    </a>
                @endif
            </div>
        </div>

        <form method="GET" action="{{ route('manage.omset.index') }}" class="mt-4 flex flex-wrap items-end gap-3" x-data="{ filter: '{{ request('filter', 'month') }}' }">
            <div>
                <label class="text-xs font-semibold text-slate-500">Filter</label>
                <select name="filter" class="mt-1 w-full rounded-lg border-[#eadfce] text-sm" x-model="filter">
                    <option value="month">Per Bulan</option>
                    <option value="day">Per Hari</option>
                    <option value="ten_days">Per 10 Hari</option>
                </select>
            </div>
            <div>
                <label class="text-xs font-semibold text-slate-500">Bulan</label>
                <select name="month" class="mt-1 w-full rounded-lg border-[#eadfce] text-sm">
                    @php
                        $months = [
                            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
                            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
                            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
                        ];
                        $selectedMonth = (int) request('month', now()->month);
                    @endphp
                    @foreach ($months as $num => $label)
                        <option value="{{ $num }}" @selected($selectedMonth === $num)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="text-xs font-semibold text-slate-500">Tahun</label>
                <input type="number" name="year" class="mt-1 w-full rounded-lg border-[#eadfce] text-sm" value="{{ request('year', now()->year) }}">
            </div>
            <div x-show="filter === 'day'">
                <label class="text-xs font-semibold text-slate-500">Tanggal</label>
                <input type="date" name="date" class="mt-1 w-full rounded-lg border-[#eadfce] text-sm" value="{{ request('date') }}">
            </div>
            <div x-show="filter === 'ten_days'">
                <label class="text-xs font-semibold text-slate-500">Mulai (10 Hari)</label>
                <input type="date" name="start_date" class="mt-1 w-full rounded-lg border-[#eadfce] text-sm" value="{{ request('start_date') }}">
            </div>
            <button type="submit" class="px-4 py-2 rounded-lg bg-[#4b2f1a] text-white text-sm font-medium">
                Terapkan
            </button>
        </form>

        <div class="mt-6 grid gap-6 lg:grid-cols-3">
            <div class="lg:col-span-2 rounded-2xl border border-[#eadfce] bg-white p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-sm font-semibold text-[#4b2f1a]">Tren Omset</h3>
                        <p class="text-xs text-slate-500">Periode {{ $chartRange['label'] }}</p>
                    </div>
                </div>
                <div class="mt-4 h-72 relative">
                    <div class="skeleton-overlay"></div>
                    <canvas id="omsetLineChart"
                            data-labels='@json($chartData["line"]["labels"])'
                            data-values='@json($chartData["line"]["data"])'></canvas>
                </div>
            </div>
            <div class="rounded-2xl border border-[#eadfce] bg-white p-4">
                <div>
                    <h3 class="text-sm font-semibold text-[#4b2f1a]">Sebaran Omset</h3>
                    <p class="text-xs text-slate-500">Total charge per therapist</p>
                </div>
                <div class="mt-4 h-72 relative">
                    <div class="skeleton-overlay"></div>
                    <canvas id="omsetDonutChart"
                            data-labels='@json($chartData["donut"]["labels"])'
                            data-values='@json($chartData["donut"]["data"])'></canvas>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    @vite('resources/js/pages/omset-charts.js')
@endpush
