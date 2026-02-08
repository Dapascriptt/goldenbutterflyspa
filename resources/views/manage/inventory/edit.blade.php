@extends('layouts.app')

@section('title', 'Edit Inventory')
@section('page_title', 'Edit Inventory')

@section('content')
    <div class="rounded-2xl bg-white border border-[#eadfce] p-6">
        <div class="mb-6">
            <h2 class="text-lg font-semibold text-[#4b2f1a]">Detail Inventory</h2>
            <p class="text-sm text-slate-500">Periode: {{ $period['label'] }}</p>
        </div>

        <form method="GET" action="{{ route('manage.inventory.edit', $item->id) }}" class="mb-6 flex flex-wrap items-end gap-3">
            <div>
                <label class="text-xs font-semibold text-slate-500">Bulan</label>
                <select name="month" class="mt-1 w-full rounded-lg border-[#eadfce] text-sm">
                    @foreach ($months as $num => $label)
                        <option value="{{ $num }}" @selected((int) request('month', $period['month']) === $num)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="text-xs font-semibold text-slate-500">Tahun</label>
                <input type="number" name="year" class="mt-1 w-full rounded-lg border-[#eadfce] text-sm" value="{{ request('year', $period['year']) }}">
            </div>
            <div>
                <label class="text-xs font-semibold text-slate-500">Tanggal Mulai</label>
                <input type="date" name="start_date" class="mt-1 w-full rounded-lg border-[#eadfce] text-sm" value="{{ request('start_date', $period['start_date']) }}">
            </div>
            <div>
                <label class="text-xs font-semibold text-slate-500">Tanggal Selesai</label>
                <input type="date" name="end_date" class="mt-1 w-full rounded-lg border-[#eadfce] text-sm" value="{{ request('end_date', $period['end_date']) }}">
            </div>
            <button type="submit" class="px-4 py-2 rounded-lg bg-[#4b2f1a] text-white text-sm font-medium">
                Terapkan
            </button>
        </form>

        <div class="mb-6 rounded-xl border border-[#eadfce] bg-[#f7f2eb] p-4">
            <div class="text-xs uppercase text-[#9c7a4c]">Stok Periode</div>
            <div class="mt-2 text-sm text-slate-600">Stok Awal: <span class="font-semibold text-[#4b2f1a]">{{ $periodStock['stock_awal'] }}</span></div>
            <div class="text-sm text-slate-600">Stok Akhir: <span class="font-semibold text-[#4b2f1a]">{{ $periodStock['stock_akhir'] }}</span></div>
        </div>

        <form method="POST" action="{{ route('manage.inventory.update', $item->id) }}" class="space-y-4">
            @csrf
            @method('PUT')
            <input type="hidden" name="month" value="{{ $period['month'] }}">
            <input type="hidden" name="year" value="{{ $period['year'] }}">
            <div>
                <label class="text-sm font-medium text-slate-600">Nama Item</label>
                <input type="text" name="name" class="mt-1 w-full rounded-lg border-[#eadfce]" value="{{ old('name', $item->name) }}">
            </div>
            <div>
                <label class="text-sm font-medium text-slate-600">Satuan</label>
                <select name="unit" class="mt-1 w-full rounded-lg border-[#eadfce]">
                    @php
                        $units = ['Bungkus', 'Pack', 'Ball', 'Dus', 'Kaleng', 'Botol', 'Kotak', 'Jerigen'];
                    @endphp
                    <option value="">Pilih satuan</option>
                    @foreach ($units as $unit)
                        <option value="{{ $unit }}" @selected(old('unit', $item->unit) === $unit)>{{ $unit }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="text-sm font-medium text-slate-600">Stok Awal</label>
                <input type="number" name="stock_awal" class="mt-1 w-full rounded-lg border-[#eadfce]" value="{{ old('stock_awal', $periodStock['stock_awal']) }}">
            </div>
            <button type="submit" class="px-4 py-2 rounded-lg bg-[#9c7a4c] text-white text-sm font-medium hover:bg-[#7b5f3d]">
                Update
            </button>
        </form>

        <div class="mt-8">
            <h3 class="text-base font-semibold text-[#4b2f1a]">Riwayat Stock In/Out</h3>
            <div class="mt-3 overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="text-left text-slate-500 border-b border-[#eadfce]">
                            <th class="py-2">Tanggal</th>
                            <th class="py-2">Tipe</th>
                            <th class="py-2">Qty</th>
                            <th class="py-2">Catatan</th>
                            <th class="py-2 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($movements ?? [] as $movement)
                            <tr class="border-b border-[#f1e7d8]">
                                <td class="py-2">{{ $movement->movement_date->format('d M Y') }}</td>
                                <td class="py-2 uppercase">{{ $movement->type }}</td>
                                <td class="py-2">{{ $movement->qty }}</td>
                                <td class="py-2">{{ $movement->note ?? '-' }}</td>
                                <td class="py-2 text-right">
                                    <a href="{{ route('manage.inventory.movements.edit', [$item->id, $movement->id]) }}" class="px-3 py-1 rounded-lg border border-[#9c7a4c] text-[#9c7a4c] text-xs font-semibold">
                                        Edit
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-4 text-center text-slate-500">Belum ada riwayat.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ ($movements ?? null)?->appends($period['query'])->links() }}
            </div>
        </div>
    </div>
@endsection
