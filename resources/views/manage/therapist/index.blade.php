@extends('layouts.app')

@section('title', 'Therapist')
@section('page_title', 'Therapist')

@section('content')
    <div class="rounded-2xl bg-white border border-[#eadfce] p-6">
        @php
            $fmt = fn ($v) => 'Rp '.number_format((int) $v, 0, ',', '.');
        @endphp
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <h2 class="text-lg font-semibold text-[#4b2f1a]">Data Therapist</h2>
                <p class="text-sm text-slate-500">Tampilan data per hari atau per bulan.</p>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                @if (auth()->user()->isKasir())
                    <a href="{{ route('manage.therapist.create') }}" class="px-4 py-2 rounded-lg bg-[#9c7a4c] text-white text-sm font-medium hover:bg-[#7b5f3d]">
                        Add Therapist
                    </a>
                @endif
                @if (auth()->user()->isAdmin())
                    <a href="{{ route('manage.therapist.export.excel') }}" class="px-3 py-2 rounded-lg bg-[#f7f2eb] text-[#9c7a4c] text-xs font-semibold border border-[#eadfce]">
                        Export Excel
                    </a>
                    <a href="{{ route('manage.therapist.export.pdf') }}" class="px-3 py-2 rounded-lg bg-[#4b2f1a] text-white text-xs font-semibold">
                        Export PDF
                    </a>
                    <a href="{{ route('manage.therapist.print') }}" class="px-3 py-2 rounded-lg border border-[#9c7a4c] text-[#9c7a4c] text-xs font-semibold">
                        Print A4
                    </a>
                @endif
            </div>
        </div>

        <form method="GET" action="{{ route('manage.therapist.index') }}" class="mt-4 flex flex-wrap items-end gap-3" x-data="{ filter: '{{ request('filter', 'month') }}' }">
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

        <div class="mt-6 overflow-x-auto">
            <table class="w-full text-sm min-w-[1400px] table-head-divider">
                <thead>
                    <tr class="text-left text-slate-500 border-b border-[#eadfce]">
                        <th class="py-3 whitespace-nowrap">Tanggal</th>
                        <th class="py-3 whitespace-nowrap">No</th>
                        <th class="py-3 whitespace-nowrap">Nama Therapist</th>
                        <th class="py-3 whitespace-nowrap">Waktu (24 jam)</th>
                        <th class="py-3 whitespace-nowrap">Extra Time (30 menit)</th>
                        <th class="py-3 whitespace-nowrap">Charge Extra Time (Rp)</th>
                        <th class="py-3 whitespace-nowrap">Traditional 60</th>
                        <th class="py-3 whitespace-nowrap">Fullbody 90</th>
                        <th class="py-3 whitespace-nowrap">Butterfly 90</th>
                        <th class="py-3 whitespace-nowrap">Add-ons Shock Wave</th>
                        <th class="py-3 whitespace-nowrap">Discount (%)</th>
                        <th class="py-3 whitespace-nowrap">Discount (Rp)</th>
                        <th class="py-3 whitespace-nowrap">Room Charge (Rp)</th>
                        <th class="py-3 whitespace-nowrap">Total Charge (Rp)</th>
                        <th class="py-3 whitespace-nowrap">Room</th>
                        <th class="py-3 text-center whitespace-nowrap">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($rows as $index => $row)
                        <tr class="border-b border-[#f1e7d8]">
                            <td class="py-2 whitespace-nowrap">{{ $row->date->format('d/m/Y') }}</td>
                            <td class="py-2 whitespace-nowrap">{{ ($rows->firstItem() ?? 0) + $index }}</td>
                            <td class="py-2 whitespace-nowrap font-medium text-[#4b2f1a]">{{ $row->therapist_name }}</td>
                            <td class="py-2 whitespace-nowrap">{{ $row->time ?? '-' }}</td>
                            <td class="py-2 whitespace-nowrap">{{ $row->extra_time }}</td>
                            <td class="py-2 whitespace-nowrap">{{ $fmt($row->extra_charge) }}</td>
                            <td class="py-2 whitespace-nowrap text-center">{{ $row->traditional }}</td>
                            <td class="py-2 whitespace-nowrap text-center">{{ $row->fullbody }}</td>
                            <td class="py-2 whitespace-nowrap text-center">{{ $row->butterfly }}</td>
                            <td class="py-2 whitespace-nowrap text-center">{{ $row->shockwave ? '1' : '-' }}</td>
                            <td class="py-2 whitespace-nowrap text-center">{{ $row->discount_percent }}</td>
                            <td class="py-2 whitespace-nowrap">{{ $fmt($row->discount_nominal) }}</td>
                            <td class="py-2 whitespace-nowrap">{{ $fmt($row->room_charge) }}</td>
                            <td class="py-2 whitespace-nowrap font-semibold text-[#4b2f1a]">{{ $fmt($row->total_charge) }}</td>
                            <td class="py-2 whitespace-nowrap">{{ $row->room ?? '-' }}</td>
                            <td class="py-2 text-center">
                                <div class="flex justify-center gap-2">
                                    <a href="{{ route('manage.therapist.edit', $row->id) }}" class="px-3 py-1 rounded-lg border border-[#9c7a4c] text-[#9c7a4c] text-xs font-semibold">
                                        Edit
                                    </a>
                                    <form method="POST" action="{{ route('manage.therapist.destroy', $row->id) }}" data-confirm>
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="px-3 py-1 rounded-lg border border-red-200 text-red-600 text-xs font-semibold">
                                            Hapus
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="16" class="py-6 text-center text-slate-500">Belum ada data therapist.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-6">
            {{ $rows->links() }}
        </div>
    </div>
@endsection
