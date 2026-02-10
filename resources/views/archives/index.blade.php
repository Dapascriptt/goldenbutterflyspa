@extends('layouts.app')

@section('title', 'Arsip')
@section('page_title', 'Arsip')

@section('content')
    <div class="rounded-2xl bg-white border border-[#eadfce] p-6">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <h2 class="text-lg font-semibold text-[#4b2f1a]">Arsip Data</h2>
                <p class="text-sm text-slate-500">Data lama lebih dari 1 tahun disimpan di sini.</p>
            </div>
            <form method="POST" action="{{ route('archives.archive') }}" onsubmit="return confirm('Jalankan arsip sekarang?');">
                @csrf
                <input type="hidden" name="months" value="12">
                <button type="submit" class="px-4 py-2 rounded-lg bg-[#4b2f1a] text-white text-sm font-medium">
                    Archive Now
                </button>
            </form>
        </div>

        <form method="GET" action="{{ route('archives.index') }}" class="mt-4 flex flex-wrap items-end gap-3">
            <div>
                <label class="text-xs font-semibold text-slate-500">Tipe</label>
                <select name="type" class="mt-1 w-full rounded-lg border-[#eadfce] text-sm">
                    <option value="omset" @selected($type === 'omset')>Omset</option>
                    <option value="therapist" @selected($type === 'therapist')>Therapist</option>
                    <option value="inventory" @selected($type === 'inventory')>Inventory</option>
                </select>
            </div>
            <div>
                <label class="text-xs font-semibold text-slate-500">Bulan</label>
                <select name="month" class="mt-1 w-full rounded-lg border-[#eadfce] text-sm">
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
        <div class="mt-4 flex flex-wrap items-center gap-2">
            @if ($type === 'inventory')
                <a href="{{ route('archives.export', ['type' => $type, 'year' => $year, 'month' => $month, 'mode' => 'movements']) }}" class="px-3 py-2 rounded-lg bg-[#f7f2eb] text-[#9c7a4c] text-xs font-semibold border border-[#eadfce]">
                    Export Movement
                </a>
                <a href="{{ route('archives.export', ['type' => $type, 'year' => $year, 'month' => $month, 'mode' => 'periods']) }}" class="px-3 py-2 rounded-lg border border-[#9c7a4c] text-[#9c7a4c] text-xs font-semibold">
                    Export Period
                </a>
            @else
                <a href="{{ route('archives.export', ['type' => $type, 'year' => $year, 'month' => $month]) }}" class="px-3 py-2 rounded-lg bg-[#f7f2eb] text-[#9c7a4c] text-xs font-semibold border border-[#eadfce]">
                    Export CSV
                </a>
            @endif
        </div>

        @if ($type === 'omset')
            <div class="mt-6 overflow-x-auto">
                <table class="w-full text-sm min-w-[860px] table-head-divider">
                    <thead>
                        <tr class="text-left text-slate-500 border-b border-[#eadfce]">
                            <th class="py-3 whitespace-nowrap">Tanggal</th>
                            <th class="py-3 whitespace-nowrap">Kode</th>
                            <th class="py-3 whitespace-nowrap">Deskripsi</th>
                            <th class="py-3 whitespace-nowrap">Nominal</th>
                            <th class="py-3 whitespace-nowrap">Dibuat Oleh</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($rows as $row)
                            <tr class="border-b border-[#f1e7d8]">
                                <td class="py-2 whitespace-nowrap">{{ \Carbon\Carbon::parse($row->date)->format('d/m/Y') }}</td>
                                <td class="py-2 whitespace-nowrap">{{ $row->code ?? '-' }}</td>
                                <td class="py-2 whitespace-nowrap">{{ $row->description ?? '-' }}</td>
                                <td class="py-2 whitespace-nowrap">Rp {{ number_format((int) $row->amount, 0, ',', '.') }}</td>
                                <td class="py-2 whitespace-nowrap">{{ $row->created_by ?? '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-6 text-center text-slate-500">Belum ada data arsip omset.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        @elseif ($type === 'therapist')
            <div class="mt-6 overflow-x-auto">
                <table class="w-full text-sm min-w-[1200px] table-head-divider">
                    <thead>
                        <tr class="text-left text-slate-500 border-b border-[#eadfce]">
                            <th class="py-3 whitespace-nowrap">Tanggal</th>
                            <th class="py-3 whitespace-nowrap">Nama Therapist</th>
                            <th class="py-3 whitespace-nowrap">Waktu</th>
                            <th class="py-3 whitespace-nowrap">Extra Time</th>
                            <th class="py-3 whitespace-nowrap">Traditional 60</th>
                            <th class="py-3 whitespace-nowrap">Full Body 90</th>
                            <th class="py-3 whitespace-nowrap">Butterfly 90</th>
                            <th class="py-3 whitespace-nowrap">Total Charge</th>
                            <th class="py-3 whitespace-nowrap">Room</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($rows as $row)
                            <tr class="border-b border-[#f1e7d8]">
                                <td class="py-2 whitespace-nowrap">{{ \Carbon\Carbon::parse($row->date)->format('d/m/Y') }}</td>
                                <td class="py-2 whitespace-nowrap font-medium text-[#4b2f1a]">{{ $row->therapist_name }}</td>
                                <td class="py-2 whitespace-nowrap">{{ $row->time ?? '-' }}</td>
                                <td class="py-2 whitespace-nowrap text-center">{{ $row->extra_time }}</td>
                                <td class="py-2 whitespace-nowrap text-center">{{ $row->traditional }}</td>
                                <td class="py-2 whitespace-nowrap text-center">{{ $row->fullbody }}</td>
                                <td class="py-2 whitespace-nowrap text-center">{{ $row->butterfly }}</td>
                                <td class="py-2 whitespace-nowrap">Rp {{ number_format((int) $row->total_charge, 0, ',', '.') }}</td>
                                <td class="py-2 whitespace-nowrap">{{ $row->room ?? '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="py-6 text-center text-slate-500">Belum ada data arsip therapist.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        @else
            <div class="mt-6">
                <h3 class="text-sm font-semibold text-[#4b2f1a]">Arsip Pergerakan Stok</h3>
                <div class="mt-3 overflow-x-auto">
                    <table class="w-full text-sm min-w-[900px] table-head-divider">
                        <thead>
                            <tr class="text-left text-slate-500 border-b border-[#eadfce]">
                                <th class="py-3 whitespace-nowrap">Tanggal</th>
                                <th class="py-3 whitespace-nowrap">Inventory ID</th>
                                <th class="py-3 whitespace-nowrap">Type</th>
                                <th class="py-3 whitespace-nowrap">Qty</th>
                                <th class="py-3 whitespace-nowrap">Catatan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($rows as $row)
                                <tr class="border-b border-[#f1e7d8]">
                                    <td class="py-2 whitespace-nowrap">{{ \Carbon\Carbon::parse($row->movement_date)->format('d/m/Y') }}</td>
                                    <td class="py-2 whitespace-nowrap">{{ $row->inventory_id }}</td>
                                    <td class="py-2 whitespace-nowrap">{{ strtoupper($row->type) }}</td>
                                    <td class="py-2 whitespace-nowrap">{{ $row->qty }}</td>
                                    <td class="py-2 whitespace-nowrap">{{ $row->note ?? '-' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="py-6 text-center text-slate-500">Belum ada data arsip inventory.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <h3 class="mt-6 text-sm font-semibold text-[#4b2f1a]">Arsip Stok Periode</h3>
                <div class="mt-3 overflow-x-auto">
                    <table class="w-full text-sm min-w-[700px] table-head-divider">
                        <thead>
                            <tr class="text-left text-slate-500 border-b border-[#eadfce]">
                                <th class="py-3 whitespace-nowrap">Inventory ID</th>
                                <th class="py-3 whitespace-nowrap">Tahun</th>
                                <th class="py-3 whitespace-nowrap">Bulan</th>
                                <th class="py-3 whitespace-nowrap">Stok Awal</th>
                                <th class="py-3 whitespace-nowrap">Stok Akhir</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($rows_secondary as $row)
                                <tr class="border-b border-[#f1e7d8]">
                                    <td class="py-2 whitespace-nowrap">{{ $row->inventory_id }}</td>
                                    <td class="py-2 whitespace-nowrap">{{ $row->period_year }}</td>
                                    <td class="py-2 whitespace-nowrap">{{ $row->period_month }}</td>
                                    <td class="py-2 whitespace-nowrap">{{ $row->stock_awal }}</td>
                                    <td class="py-2 whitespace-nowrap">{{ $row->stock_akhir }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="py-6 text-center text-slate-500">Belum ada data arsip periode.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    </div>
@endsection
