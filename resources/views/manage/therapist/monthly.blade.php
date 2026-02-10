@extends('layouts.app')

@section('title', 'Monthly Therapy Result')
@section('page_title', 'Monthly Therapy Result')

@section('content')
    <div class="rounded-2xl bg-white border border-[#eadfce] p-6">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <h2 class="text-lg font-semibold text-[#4b2f1a]">Monthly Therapy Result</h2>
                <p class="text-sm text-slate-500">Rekap hasil terapi per bulan.</p>
            </div>
            @if (auth()->user()->isAdmin())
                <div class="flex flex-wrap items-center gap-2">
                    <a href="{{ route('manage.therapist.export.excel', ['view' => 'monthly'] + request()->only(['month', 'year'])) }}" class="px-3 py-2 rounded-lg bg-[#f7f2eb] text-[#9c7a4c] text-xs font-semibold border border-[#eadfce]">
                        Export Excel
                    </a>
                    <a href="{{ route('manage.therapist.export.pdf', ['view' => 'monthly'] + request()->only(['month', 'year'])) }}" class="px-3 py-2 rounded-lg bg-[#4b2f1a] text-white text-xs font-semibold">
                        Export PDF
                    </a>
                    <a href="{{ route('manage.therapist.print', ['view' => 'monthly'] + request()->only(['month', 'year'])) }}" class="px-3 py-2 rounded-lg border border-[#9c7a4c] text-[#9c7a4c] text-xs font-semibold">
                        Print A4
                    </a>
                </div>
            @endif
        </div>

        <form method="GET" action="{{ route('manage.therapist.monthly') }}" class="mt-4 flex flex-wrap items-end gap-3">
            <div>
                <label class="text-xs font-semibold text-slate-500">Bulan</label>
                <select name="month" class="mt-1 w-full rounded-lg border-[#eadfce] text-sm">
                    @php
                        $months = [
                            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
                            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
                            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
                        ];
                    @endphp
                    @foreach ($months as $num => $label)
                        <option value="{{ $num }}" @selected($month === $num)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="text-xs font-semibold text-slate-500">Tahun</label>
                <input type="number" name="year" class="mt-1 w-full rounded-lg border-[#eadfce] text-sm" value="{{ $year }}">
            </div>
            <button type="submit" class="px-4 py-2 rounded-lg bg-[#4b2f1a] text-white text-sm font-medium">
                Terapkan
            </button>
        </form>

        <div class="mt-6 overflow-x-auto relative">
            <div class="skeleton-overlay"></div>
            <table class="w-full text-sm min-w-[980px] table-head-divider">
                <thead>
                    <tr class="text-left text-slate-500 border-b border-[#eadfce]">
                        <th class="py-3 whitespace-nowrap">No</th>
                        <th class="py-3 whitespace-nowrap">Nama Therapist</th>
                        <th class="py-3 whitespace-nowrap">Traditional 60</th>
                        <th class="py-3 whitespace-nowrap">Full Body 90</th>
                        <th class="py-3 whitespace-nowrap">Butterfly 90</th>
                        <th class="py-3 whitespace-nowrap">Extra Time (30 menit)</th>
                        <th class="py-3 whitespace-nowrap">Total Treatment</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($rows as $index => $row)
                        <tr class="border-b border-[#f1e7d8]">
                            <td class="py-2 whitespace-nowrap">{{ $index + 1 }}</td>
                            <td class="py-2 whitespace-nowrap font-medium text-[#4b2f1a]">{{ $row->therapist_name }}</td>
                            <td class="py-2 whitespace-nowrap text-center">{{ $row->traditional }}</td>
                            <td class="py-2 whitespace-nowrap text-center">{{ $row->fullbody }}</td>
                            <td class="py-2 whitespace-nowrap text-center">{{ $row->butterfly }}</td>
                            <td class="py-2 whitespace-nowrap text-center">{{ $row->extra_time }}</td>
                            <td class="py-2 whitespace-nowrap text-center font-semibold">{{ $row->total_treatment }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-6 text-center text-slate-500">Belum ada data.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-8 rounded-2xl border border-[#eadfce] bg-white p-4">
            <h3 class="text-sm font-semibold text-[#4b2f1a]">Diagram Extra Time vs Total Treatment</h3>
            <div class="mt-4 h-72">
                <canvas id="monthlyTherapyChart"
                        data-labels='@json($chart["labels"])'
                        data-extra='@json($chart["extra_time"])'
                        data-total='@json($chart["total_treatment"])'></canvas>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    @vite('resources/js/pages/therapist-monthly.js')
@endpush
