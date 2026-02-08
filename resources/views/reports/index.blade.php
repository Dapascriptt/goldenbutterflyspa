@extends('layouts.app')

@section('title', 'Reports')
@section('page_title', 'Reports')

@section('content')
    <div class="rounded-2xl bg-white border border-[#eadfce] p-6">
        <h2 class="text-lg font-semibold text-[#4b2f1a]">Laporan Golden Spa</h2>
        <p class="mt-2 text-sm text-slate-500">
            Halaman laporan bersifat view untuk semua role. Export & print hanya untuk admin.
        </p>

        <div class="mt-6 flex flex-wrap gap-3">
            @if (auth()->user()->isAdmin())
                <a href="{{ route('reports.export.excel') }}" class="px-4 py-2 rounded-lg bg-[#9c7a4c] text-white text-sm font-medium hover:bg-[#7b5f3d]">
                    Export Excel
                </a>
                <a href="{{ route('reports.export.pdf') }}" class="px-4 py-2 rounded-lg bg-[#4b2f1a] text-white text-sm font-medium hover:bg-[#2f1c10]">
                    Export PDF
                </a>
                <a href="{{ route('reports.print') }}" class="px-4 py-2 rounded-lg border border-[#9c7a4c] text-[#9c7a4c] text-sm font-medium hover:bg-[#f7f2eb]">
                    Print A4
                </a>
            @else
                <div class="text-sm text-slate-500">
                    Akses export dan print hanya untuk admin.
                </div>
            @endif
        </div>
    </div>

    <div class="mt-6 rounded-2xl bg-white border border-[#eadfce] p-6">
        <h3 class="text-base font-semibold text-[#4b2f1a]">Ringkasan</h3>
        <div class="mt-3 grid gap-4 md:grid-cols-3">
            <div class="rounded-xl bg-[#f7f2eb] p-4">
                <div class="text-xs uppercase text-[#9c7a4c]">Omset</div>
                <div class="mt-2 text-lg font-semibold text-[#4b2f1a]">Rp 0</div>
            </div>
            <div class="rounded-xl bg-[#f7f2eb] p-4">
                <div class="text-xs uppercase text-[#9c7a4c]">Therapist</div>
                <div class="mt-2 text-lg font-semibold text-[#4b2f1a]">0 Orang</div>
            </div>
            <div class="rounded-xl bg-[#f7f2eb] p-4">
                <div class="text-xs uppercase text-[#9c7a4c]">Inventory</div>
                <div class="mt-2 text-lg font-semibold text-[#4b2f1a]">0 Item</div>
            </div>
        </div>
    </div>
@endsection
