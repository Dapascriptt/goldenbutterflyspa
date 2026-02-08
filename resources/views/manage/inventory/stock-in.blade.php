@extends('layouts.app')

@section('title', 'Stock In')
@section('page_title', 'Stock In')

@section('content')
    <div class="rounded-2xl bg-white border border-[#eadfce] p-6">
        <h2 class="text-lg font-semibold text-[#4b2f1a]">Stock In: {{ $item->name }}</h2>
        <p class="mt-1 text-sm text-slate-500">Periode: {{ $period['label'] }}</p>

        <form method="POST" action="{{ route('manage.inventory.stock-in', $item->id) }}" class="mt-6 space-y-4">
            @csrf
            <input type="hidden" name="month" value="{{ $period['month'] }}">
            <input type="hidden" name="year" value="{{ $period['year'] }}">
            <input type="hidden" name="start_date" value="{{ $period['start_date'] }}">
            <input type="hidden" name="end_date" value="{{ $period['end_date'] }}">
            <div>
                <label class="text-sm font-medium text-slate-600">Tanggal</label>
                <input type="date" name="movement_date" class="mt-1 w-full rounded-lg border-[#eadfce]" value="{{ old('movement_date', $period['default_date']) }}">
                @error('movement_date')
                    <div class="mt-1 text-xs text-red-600">{{ $message }}</div>
                @enderror
            </div>
            <div>
                <label class="text-sm font-medium text-slate-600">Jumlah Masuk</label>
                <input type="number" name="qty" class="mt-1 w-full rounded-lg border-[#eadfce]" min="1" value="{{ old('qty') }}">
                @error('qty')
                    <div class="mt-1 text-xs text-red-600">{{ $message }}</div>
                @enderror
            </div>
            <div>
                <label class="text-sm font-medium text-slate-600">Catatan (opsional)</label>
                <input type="text" name="note" class="mt-1 w-full rounded-lg border-[#eadfce]" value="{{ old('note') }}">
            </div>
            <div class="flex gap-2">
                <button type="submit" class="px-4 py-2 rounded-lg bg-[#9c7a4c] text-white text-sm font-medium hover:bg-[#7b5f3d]">
                    Simpan
                </button>
                <a href="{{ route('manage.inventory.index', $period['query']) }}" class="px-4 py-2 rounded-lg border border-[#eadfce] text-sm font-medium text-slate-600">
                    Batal
                </a>
            </div>
        </form>
    </div>
@endsection
