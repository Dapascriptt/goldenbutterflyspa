@extends('layouts.app')

@section('title', 'Inventory')
@section('page_title', 'Inventory')

@section('content')
    @php
        $units = $units ?? ['Bungkus', 'Pack', 'Ball', 'Dus', 'Kaleng', 'Botol', 'Kotak', 'Jerigen'];
    @endphp
    <div x-data="{ showCreate: false, showIn: false, showOut: false, showEdit: false, showHistory: false, selected: null, selectedMovements: [] }">
        <div class="rounded-2xl bg-white border border-[#eadfce] p-6">
            <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <h2 class="text-lg font-semibold text-[#4b2f1a]">Data Inventory</h2>
                <p class="text-sm text-slate-500">Periode: {{ $period['label'] }}</p>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                @if (auth()->user()->isKasir())
                    <button type="button" @click="showCreate = true" class="px-4 py-2 rounded-lg bg-[#9c7a4c] text-white text-sm font-medium hover:bg-[#7b5f3d]">
                        Tambah Inventory
                    </button>
                @endif
                @if (auth()->user()->isAdmin())
                    <a href="{{ route('manage.inventory.export.excel', $period['query']) }}" class="px-3 py-2 rounded-lg bg-[#f7f2eb] text-[#9c7a4c] text-xs font-semibold border border-[#eadfce]">
                        Export Excel
                    </a>
                    <a href="{{ route('manage.inventory.export.pdf', $period['query']) }}" class="px-3 py-2 rounded-lg bg-[#4b2f1a] text-white text-xs font-semibold">
                        Export PDF
                    </a>
                    <a href="{{ route('manage.inventory.print', $period['query']) }}" class="px-3 py-2 rounded-lg border border-[#9c7a4c] text-[#9c7a4c] text-xs font-semibold">
                        Print A4
                    </a>
                @endif
            </div>
        </div>

            <form method="GET" action="{{ route('manage.inventory.index') }}" class="mt-4 flex flex-wrap items-end gap-3">
            <div>
                <label class="text-xs font-semibold text-slate-500">Bulan</label>
                <select name="month" class="mt-1 w-full rounded-lg border-[#eadfce] text-sm">
                    @foreach ($months as $num => $label)
                        <option value="{{ $num }}" @selected((int) $period['month'] === $num)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="text-xs font-semibold text-slate-500">Tahun</label>
                <input type="number" name="year" class="mt-1 w-full rounded-lg border-[#eadfce] text-sm" value="{{ $period['year'] }}">
            </div>
            <div class="min-w-[220px]">
                <label class="text-xs font-semibold text-slate-500">Cari Nama Item</label>
                <input type="text" name="search" class="mt-1 w-full rounded-lg border-[#eadfce] text-sm"  value="{{ $search }}">
            </div>
            <button type="submit" class="px-4 py-2 rounded-lg bg-[#4b2f1a] text-white text-sm font-medium">
                Terapkan
            </button>
        </form>

            @php
                $units = ['Bungkus', 'Pack', 'Ball', 'Dus', 'Kaleng', 'Botol', 'Kotak', 'Jerigen'];
            @endphp
            <div class="mt-6 overflow-x-auto">
                <table class="w-full text-sm table-head-divider">
                <thead>
                    <tr class="text-left text-slate-500 border-b border-[#eadfce]">
                        <th class="py-3">Nama Item</th>
                        <th class="py-3">Satuan</th>
                        <th class="py-3">Stok Awal</th>
                        <th class="py-3">Stok Akhir</th>
                        <th class="py-3">Tanggal Input</th>
                        <th class="py-3 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($items as $item)
                        @php
                            $periodStock = $periodStocks[$item->id] ?? null;
                            $stockAwal = $periodStock?->stock_awal ?? $item->stock_awal;
                            $stockAkhir = $periodStock?->stock_akhir ?? $stockAwal;
                            $tanggalInput = $periodStock?->created_at?->format('d M Y') ?? '-';
                        @endphp
                        <tr class="border-b border-[#f1e7d8]">
                            <td class="py-3 font-medium text-[#4b2f1a]">{{ $item->name }}</td>
                            <td class="py-3">{{ $item->unit ?? '-' }}</td>
                            <td class="py-3">{{ $stockAwal }}</td>
                            <td class="py-3">{{ $stockAkhir }}</td>
                            <td class="py-3">{{ $tanggalInput }}</td>
                            <td class="py-3 text-center">
                                <div class="flex flex-wrap justify-center gap-2">
                                    <button
                                        type="button"
                                        @click="selected = @js(['id' => $item->id, 'name' => $item->name, 'unit' => $item->unit, 'stock_awal' => $stockAwal]); showIn = true"
                                        class="px-3 py-1 rounded-lg bg-[#f7f2eb] text-[#9c7a4c] text-xs font-semibold border border-[#eadfce]"
                                    >
                                        Stock In
                                    </button>
                                    <button
                                        type="button"
                                        @click="selected = @js(['id' => $item->id, 'name' => $item->name, 'unit' => $item->unit, 'stock_awal' => $stockAwal]); showOut = true"
                                        class="px-3 py-1 rounded-lg bg-[#4b2f1a] text-white text-xs font-semibold"
                                    >
                                        Stock Out
                                    </button>
                                    <button
                                        type="button"
                                        @click="selected = @js(['id' => $item->id, 'name' => $item->name, 'unit' => $item->unit, 'stock_awal' => $stockAwal]); showEdit = true"
                                        class="px-3 py-1 rounded-lg border border-[#9c7a4c] text-[#9c7a4c] text-xs font-semibold"
                                    >
                                        Edit
                                    </button>
                                    <button
                                        type="button"
                                        @click="selected = @js(['id' => $item->id, 'name' => $item->name]); selectedMovements = @js(($movementsByItem[$item->id] ?? collect())->map(fn($m) => ['date' => $m->movement_date->format('d M Y'), 'type' => strtoupper($m->type), 'qty' => $m->qty, 'note' => $m->note ?? '-', 'id' => $m->id])->values()); showHistory = true"
                                        class="px-3 py-1 rounded-lg border border-[#eadfce] text-slate-600 text-xs font-semibold"
                                    >
                                        Riwayat
                                    </button>
                                    <form method="POST" action="{{ route('manage.inventory.destroy', $item->id) }}" data-confirm>
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
                            <td colspan="6" class="py-6 text-center text-slate-500">Belum ada data inventory.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

            <div class="mt-6">
                {{ $items->appends(['search' => $search] + $period['query'])->links() }}
            </div>
        </div>

    <template x-if="showCreate">
        <div class="fixed inset-0 z-50 flex items-center justify-center">
            <div class="absolute inset-0 bg-black/40" @click="showCreate = false"></div>
            <div class="relative w-full max-w-lg rounded-2xl bg-white border border-[#eadfce] p-6 shadow-xl">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <h3 class="text-lg font-semibold text-[#4b2f1a]">Tambah Inventory</h3>
                        <p class="text-sm text-slate-500">Periode {{ $period['label'] }}</p>
                    </div>
                    <button type="button" class="h-9 w-9 rounded-lg border border-[#eadfce] text-[#9c7a4c]" @click="showCreate = false">✕</button>
                </div>
                <form method="POST" action="{{ route('manage.inventory.store') }}" class="mt-5 space-y-4">
                    @csrf
                    <input type="hidden" name="month" value="{{ $period['month'] }}">
                    <input type="hidden" name="year" value="{{ $period['year'] }}">
                    <div>
                        <label class="text-sm font-medium text-slate-600">Nama Item</label>
                        <input type="text" name="name" class="mt-1 w-full rounded-lg border-[#eadfce]" placeholder="Nama item">
                    </div>
                    <div>
                        <label class="text-sm font-medium text-slate-600">Satuan</label>
                        <select name="unit" class="mt-1 w-full rounded-lg border-[#eadfce]">
                            <option value="">Pilih satuan</option>
                            @foreach ($units as $unit)
                                <option value="{{ $unit }}">{{ $unit }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-slate-600">Stok Awal</label>
                        <input type="number" name="stock_awal" min="0" class="mt-1 w-full rounded-lg border-[#eadfce]" value="0">
                    </div>
                    <div class="flex justify-end gap-2">
                        <button type="button" class="px-4 py-2 rounded-lg border border-[#eadfce] text-sm" @click="showCreate = false">Batal</button>
                        <button type="submit" class="px-4 py-2 rounded-lg bg-[#9c7a4c] text-white text-sm">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </template>

    <template x-if="showIn">
        <div class="fixed inset-0 z-50 flex items-center justify-center">
            <div class="absolute inset-0 bg-black/40" @click="showIn = false"></div>
            <div class="relative w-full max-w-lg rounded-2xl bg-white border border-[#eadfce] p-6 shadow-xl">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <h3 class="text-lg font-semibold text-[#4b2f1a]">Stock In</h3>
                        <p class="text-sm text-slate-500" x-text="selected ? `${selected.name} · Periode {{ $period['label'] }}` : ''"></p>
                    </div>
                    <button type="button" class="h-9 w-9 rounded-lg border border-[#eadfce] text-[#9c7a4c]" @click="showIn = false">✕</button>
                </div>
                <form method="POST" x-bind:action="selected ? `{{ url('/manage/inventory') }}/${selected.id}/stock-in` : ''" class="mt-5 space-y-4">
                    @csrf
                    <input type="hidden" name="month" value="{{ $period['month'] }}">
                    <input type="hidden" name="year" value="{{ $period['year'] }}">
                    <input type="hidden" name="start_date" value="{{ $period['start_date'] }}">
                    <input type="hidden" name="end_date" value="{{ $period['end_date'] }}">
                    <div>
                        <label class="text-sm font-medium text-slate-600">Tanggal</label>
                        <input type="date" name="movement_date" class="mt-1 w-full rounded-lg border-[#eadfce]" value="{{ $period['default_date'] }}">
                    </div>
                    <div>
                        <label class="text-sm font-medium text-slate-600">Jumlah Masuk</label>
                        <input type="number" name="qty" min="1" class="mt-1 w-full rounded-lg border-[#eadfce]">
                    </div>
                    <div>
                        <label class="text-sm font-medium text-slate-600">Catatan (opsional)</label>
                        <input type="text" name="note" class="mt-1 w-full rounded-lg border-[#eadfce]">
                    </div>
                    <div class="flex justify-end gap-2">
                        <button type="button" class="px-4 py-2 rounded-lg border border-[#eadfce] text-sm" @click="showIn = false">Batal</button>
                        <button type="submit" class="px-4 py-2 rounded-lg bg-[#9c7a4c] text-white text-sm">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </template>

    <template x-if="showOut">
        <div class="fixed inset-0 z-50 flex items-center justify-center">
            <div class="absolute inset-0 bg-black/40" @click="showOut = false"></div>
            <div class="relative w-full max-w-lg rounded-2xl bg-white border border-[#eadfce] p-6 shadow-xl">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <h3 class="text-lg font-semibold text-[#4b2f1a]">Stock Out</h3>
                        <p class="text-sm text-slate-500" x-text="selected ? `${selected.name} · Periode {{ $period['label'] }}` : ''"></p>
                    </div>
                    <button type="button" class="h-9 w-9 rounded-lg border border-[#eadfce] text-[#9c7a4c]" @click="showOut = false">✕</button>
                </div>
                <form method="POST" x-bind:action="selected ? `{{ url('/manage/inventory') }}/${selected.id}/stock-out` : ''" class="mt-5 space-y-4">
                    @csrf
                    <input type="hidden" name="month" value="{{ $period['month'] }}">
                    <input type="hidden" name="year" value="{{ $period['year'] }}">
                    <input type="hidden" name="start_date" value="{{ $period['start_date'] }}">
                    <input type="hidden" name="end_date" value="{{ $period['end_date'] }}">
                    <div>
                        <label class="text-sm font-medium text-slate-600">Tanggal</label>
                        <input type="date" name="movement_date" class="mt-1 w-full rounded-lg border-[#eadfce]" value="{{ $period['default_date'] }}">
                    </div>
                    <div>
                        <label class="text-sm font-medium text-slate-600">Jumlah Keluar</label>
                        <input type="number" name="qty" min="1" class="mt-1 w-full rounded-lg border-[#eadfce]">
                    </div>
                    <div>
                        <label class="text-sm font-medium text-slate-600">Catatan (opsional)</label>
                        <input type="text" name="note" class="mt-1 w-full rounded-lg border-[#eadfce]">
                    </div>
                    <div class="flex justify-end gap-2">
                        <button type="button" class="px-4 py-2 rounded-lg border border-[#eadfce] text-sm" @click="showOut = false">Batal</button>
                        <button type="submit" class="px-4 py-2 rounded-lg bg-[#4b2f1a] text-white text-sm">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </template>

    <template x-if="showEdit">
        <div class="fixed inset-0 z-50 flex items-center justify-center">
            <div class="absolute inset-0 bg-black/40" @click="showEdit = false"></div>
            <div class="relative w-full max-w-lg rounded-2xl bg-white border border-[#eadfce] p-6 shadow-xl">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <h3 class="text-lg font-semibold text-[#4b2f1a]">Edit Inventory</h3>
                        <p class="text-sm text-slate-500" x-text="selected ? `${selected.name} · Periode {{ $period['label'] }}` : ''"></p>
                    </div>
                    <button type="button" class="h-9 w-9 rounded-lg border border-[#eadfce] text-[#9c7a4c]" @click="showEdit = false">✕</button>
                </div>
                <form method="POST" x-bind:action="selected ? `{{ url('/manage/inventory') }}/${selected.id}` : ''" class="mt-5 space-y-4">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="month" value="{{ $period['month'] }}">
                    <input type="hidden" name="year" value="{{ $period['year'] }}">
                    <div>
                        <label class="text-sm font-medium text-slate-600">Nama Item</label>
                        <input type="text" name="name" class="mt-1 w-full rounded-lg border-[#eadfce]" x-model="selected.name">
                    </div>
                    <div>
                        <label class="text-sm font-medium text-slate-600">Satuan</label>
                        <select name="unit" class="mt-1 w-full rounded-lg border-[#eadfce]" x-model="selected.unit">
                            <option value="">Pilih satuan</option>
                            @foreach ($units as $unit)
                                <option value="{{ $unit }}">{{ $unit }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-slate-600">Stok Awal (Periode)</label>
                        <input type="number" name="stock_awal" class="mt-1 w-full rounded-lg border-[#eadfce]" x-model="selected.stock_awal">
                    </div>
                    <div class="flex justify-end gap-2">
                        <button type="button" class="px-4 py-2 rounded-lg border border-[#eadfce] text-sm" @click="showEdit = false">Batal</button>
                        <button type="submit" class="px-4 py-2 rounded-lg bg-[#9c7a4c] text-white text-sm">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </template>

    <template x-if="showHistory">
        <div class="fixed inset-0 z-50 flex items-center justify-center">
            <div class="absolute inset-0 bg-black/40" @click="showHistory = false"></div>
            <div class="relative w-full max-w-3xl rounded-2xl bg-white border border-[#eadfce] p-6 shadow-xl">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <h3 class="text-lg font-semibold text-[#4b2f1a]">Riwayat Stock In/Out</h3>
                        <p class="text-sm text-slate-500" x-text="selected ? `${selected.name} · Periode {{ $period['label'] }}` : ''"></p>
                    </div>
                    <button type="button" class="h-9 w-9 rounded-lg border border-[#eadfce] text-[#9c7a4c]" @click="showHistory = false">✕</button>
                </div>
                <div class="mt-4 overflow-x-auto">
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
                            <template x-for="row in selectedMovements" :key="row.id">
                                <tr class="border-b border-[#f1e7d8]">
                                    <td class="py-2" x-text="row.date"></td>
                                    <td class="py-2 uppercase" x-text="row.type"></td>
                                    <td class="py-2" x-text="row.qty"></td>
                                    <td class="py-2" x-text="row.note"></td>
                                    <td class="py-2 text-right">
                                        <a class="px-3 py-1 rounded-lg border border-[#9c7a4c] text-[#9c7a4c] text-xs font-semibold" x-bind:href="`{{ url('/manage/inventory') }}/${selected.id}/movements/${row.id}/edit`">
                                            Edit
                                        </a>
                                    </td>
                                </tr>
                            </template>
                            <tr x-show="selectedMovements.length === 0">
                                <td colspan="5" class="py-4 text-center text-slate-500">Belum ada riwayat.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="mt-4 text-right">
                    <button type="button" class="px-4 py-2 rounded-lg border border-[#eadfce] text-sm" @click="showHistory = false">Tutup</button>
                </div>
            </div>
        </div>
    </template>
    </div>
@endsection
