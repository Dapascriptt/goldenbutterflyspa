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
                @if (auth()->user()->isKasir())
                    <a href="{{ route('manage.omset.create') }}" class="px-4 py-2 rounded-lg bg-[#9c7a4c] text-white text-sm font-medium hover:bg-[#7b5f3d]">
                        Tambah Omset
                    </a>
                @endif
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

        <div class="mt-6 text-sm text-slate-500">
            Tabel omset akan ditambahkan di sini.
        </div>
    </div>
@endsection
