@extends('layouts.app')

@section('title', 'Edit Stock Movement')
@section('page_title', 'Edit Stock Movement')

@section('content')
    <div class="rounded-2xl bg-white border border-[#eadfce] p-6">
        <h2 class="text-lg font-semibold text-[#4b2f1a]">
            Edit {{ strtoupper($movement->type) }}: {{ $item->name }}
        </h2>
        <p class="mt-1 text-sm text-slate-500">Periode: {{ $period['month'] }}/{{ $period['year'] }}</p>

        <form method="POST" action="{{ route('manage.inventory.movements.update', [$item->id, $movement->id]) }}" class="mt-6 space-y-4">
            @csrf
            @method('PUT')
            <div>
                <label class="text-sm font-medium text-slate-600">Tipe</label>
                <input type="text" class="mt-1 w-full rounded-lg border-[#eadfce] bg-slate-50" value="{{ strtoupper($movement->type) }}" readonly>
            </div>
            <div>
                <label class="text-sm font-medium text-slate-600">Jumlah</label>
                <input type="number" name="qty" class="mt-1 w-full rounded-lg border-[#eadfce]" min="1" value="{{ old('qty', $movement->qty) }}">
                @error('qty')
                    <div class="mt-1 text-xs text-red-600">{{ $message }}</div>
                @enderror
            </div>
            <div>
                <label class="text-sm font-medium text-slate-600">Tanggal</label>
                <input type="date" name="movement_date" class="mt-1 w-full rounded-lg border-[#eadfce]" value="{{ old('movement_date', $movement->movement_date?->toDateString()) }}">
                @error('movement_date')
                    <div class="mt-1 text-xs text-red-600">{{ $message }}</div>
                @enderror
            </div>
            <div>
                <label class="text-sm font-medium text-slate-600">Catatan (opsional)</label>
                <input type="text" name="note" class="mt-1 w-full rounded-lg border-[#eadfce]" value="{{ old('note', $movement->note) }}">
            </div>
            <div class="flex gap-2">
                <button type="submit" class="px-4 py-2 rounded-lg bg-[#9c7a4c] text-white text-sm font-medium hover:bg-[#7b5f3d]">
                    Simpan
                </button>
                <a href="{{ route('manage.inventory.edit', array_merge(['id' => $item->id], $period['query'])) }}" class="px-4 py-2 rounded-lg border border-[#eadfce] text-sm font-medium text-slate-600">
                    Batal
                </a>
            </div>
        </form>
    </div>
@endsection
