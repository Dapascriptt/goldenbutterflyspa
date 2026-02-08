@extends('layouts.app')

@section('title', 'Tambah Inventory')
@section('page_title', 'Tambah Inventory')

@section('content')
    <div class="rounded-2xl bg-white border border-[#eadfce] p-6">
        @if ($errors->any())
            <div class="mb-4 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                <div class="font-semibold">Gagal menyimpan:</div>
                <ul class="mt-1 list-disc pl-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <form method="POST" action="{{ route('manage.inventory.store') }}" class="space-y-4">
            @csrf
            <input type="hidden" name="month" value="{{ request('month', now()->month) }}">
            <input type="hidden" name="year" value="{{ request('year', now()->year) }}">
            <div>
                <label class="text-sm font-medium text-slate-600">Nama Item</label>
                <input type="text" name="name" class="mt-1 w-full rounded-lg border-[#eadfce]" placeholder="Nama item" value="{{ old('name') }}">
                @error('name')
                    <div class="mt-1 text-xs text-red-600">{{ $message }}</div>
                @enderror
            </div>
            <div>
                <label class="text-sm font-medium text-slate-600">Satuan</label>
                <select name="unit" class="mt-1 w-full rounded-lg border-[#eadfce]">
                    @php
                        $units = ['Bungkus', 'Pack', 'Ball', 'Dus', 'Kaleng', 'Botol', 'Kotak', 'Jerigen'];
                    @endphp
                    <option value="">Pilih satuan</option>
                    @foreach ($units as $unit)
                        <option value="{{ $unit }}" @selected(old('unit') === $unit)>{{ $unit }}</option>
                    @endforeach
                </select>
                @error('unit')
                    <div class="mt-1 text-xs text-red-600">{{ $message }}</div>
                @enderror
            </div>
            <div>
                <label class="text-sm font-medium text-slate-600">Stok Awal</label>
                <input type="number" name="stock_awal" class="mt-1 w-full rounded-lg border-[#eadfce]" placeholder="0" value="{{ old('stock_awal', 0) }}">
                @error('stock_awal')
                    <div class="mt-1 text-xs text-red-600">{{ $message }}</div>
                @enderror
            </div>
            <button type="submit" class="px-4 py-2 rounded-lg bg-[#9c7a4c] text-white text-sm font-medium hover:bg-[#7b5f3d]">
                Simpan
            </button>
        </form>
    </div>
@endsection
