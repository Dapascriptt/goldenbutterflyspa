@extends('layouts.app')

@section('title', 'Dashboard')
@section('page_title', 'Beranda')

@section('content')
    <div class="rounded-3xl border border-[#eadfce] bg-gradient-to-br from-white via-[#fdf9f3] to-[#f7f2eb] p-6">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <div class="text-sm uppercase tracking-[0.25em] text-[#9c7a4c]">Golden Spa</div>
                <h2 class="mt-2 text-2xl font-semibold text-[#4b2f1a]">Ringkasan Kinerja Hari Ini</h2>
                <p class="mt-1 text-sm text-slate-500">Insight cepat dari omset, therapist, dan inventory.</p>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('manage.omset.index') }}" class="px-4 py-2 rounded-lg border border-[#eadfce] bg-white text-sm font-medium text-[#4b2f1a] hover:bg-[#f7f2eb]">Lihat Omset</a>
                <a href="{{ route('manage.therapist.index') }}" class="px-4 py-2 rounded-lg bg-[#4b2f1a] text-white text-sm font-medium hover:bg-[#3a2414]">Kelola Therapist</a>
            </div>
        </div>
    </div>

    <div class="mt-8 grid gap-6 lg:grid-cols-12">
        <div class="lg:col-span-8">
            <div class="grid gap-4 sm:grid-cols-2">
                <div class="rounded-2xl bg-white border border-[#eadfce] p-5">
                    <div class="text-xs font-semibold uppercase tracking-widest text-[#9c7a4c]">Omset Hari Ini</div>
                    <div class="mt-3 text-2xl font-semibold text-[#4b2f1a]">
                        Rp {{ number_format($metrics['today_total'] ?? 0, 0, ',', '.') }}
                    </div>
                    <div class="mt-2 text-xs text-slate-400">Transaksi: {{ $metrics['today_count'] ?? 0 }}</div>
                </div>
                <div class="rounded-2xl bg-white border border-[#eadfce] p-5">
                    <div class="text-xs font-semibold uppercase tracking-widest text-[#9c7a4c]">Omset Minggu Ini</div>
                    <div class="mt-3 text-2xl font-semibold text-[#4b2f1a]">
                        Rp {{ number_format($metrics['week_total'] ?? 0, 0, ',', '.') }}
                    </div>
                    <div class="mt-2 text-xs text-slate-400">7 hari terakhir</div>
                </div>
                <div class="rounded-2xl bg-white border border-[#eadfce] p-5">
                    <div class="text-xs font-semibold uppercase tracking-widest text-[#9c7a4c]">Omset Bulan Ini</div>
                    <div class="mt-3 text-2xl font-semibold text-[#4b2f1a]">
                        Rp {{ number_format($metrics['month_total'] ?? 0, 0, ',', '.') }}
                    </div>
                    <div class="mt-2 text-xs text-slate-400">Akumulasi bulan berjalan</div>
                </div>
                <div class="rounded-2xl bg-white border border-[#eadfce] p-5">
                    <div class="text-xs font-semibold uppercase tracking-widest text-[#9c7a4c]">Therapist Aktif</div>
                    <div class="mt-3 text-2xl font-semibold text-[#4b2f1a]">{{ $metrics['therapist_total'] ?? 0 }}</div>
                    <div class="mt-2 text-xs text-slate-400">Treatment hari ini: {{ $metrics['treatment_today'] ?? 0 }}</div>
                </div>
            </div>

            <div class="mt-6 rounded-2xl bg-white border border-[#eadfce] p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-semibold text-[#4b2f1a]">Tren Omset 30 Hari</h3>
                        <p class="mt-1 text-sm text-slate-500">Total charge therapist per hari.</p>
                    </div>
                    <span class="px-3 py-1 rounded-full text-xs font-semibold bg-[#f7f2eb] text-[#9c7a4c]">30 Hari</span>
                </div>
                <div class="mt-4 h-72 relative">
                    <div class="skeleton-overlay"></div>
                    <canvas id="dashboardLineChart"
                            data-series='@json($chart ?? [])'></canvas>
                </div>
            </div>
        </div>

        <div class="lg:col-span-4 space-y-6">
            <div class="rounded-2xl bg-white border border-[#eadfce] p-5">
                <h3 class="text-base font-semibold text-[#4b2f1a]">Distribusi Omset</h3>
                <p class="mt-1 text-xs text-slate-500">Top therapist 30 hari terakhir.</p>
                <div class="mt-4 h-64 relative">
                    <div class="skeleton-overlay"></div>
                    <canvas id="dashboardDonutChart"
                            data-labels='@json($donut["labels"] ?? [])'
                            data-values='@json($donut["data"] ?? [])'></canvas>
                </div>
            </div>
            <div class="rounded-2xl bg-white border border-[#eadfce] p-5">
                <h3 class="text-base font-semibold text-[#4b2f1a]">Inventory Snapshot</h3>
                <div class="mt-4 grid gap-3">
                    <div class="flex items-center justify-between rounded-xl border border-[#eadfce] bg-[#fdf9f3] px-4 py-3">
                        <div class="text-xs font-semibold uppercase tracking-widest text-[#9c7a4c]">Total Item</div>
                        <div class="text-lg font-semibold text-[#4b2f1a]">{{ $metrics['inventory_total'] ?? 0 }}</div>
                    </div>
                    <div class="flex items-center justify-between rounded-xl border border-[#eadfce] bg-[#fff7ed] px-4 py-3">
                        <div class="text-xs font-semibold uppercase tracking-widest text-[#9c7a4c]">Low Stock</div>
                        <div class="text-lg font-semibold text-[#b45309]">{{ $metrics['inventory_low'] ?? 0 }}</div>
                    </div>
                    <a href="{{ route('manage.inventory.index') }}" class="w-full text-center px-4 py-2 rounded-lg border border-[#eadfce] text-sm font-medium text-[#4b2f1a] hover:bg-[#f7f2eb]">Cek Inventory</a>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    @vite('resources/js/pages/dashboard-omset.js')
@endpush
