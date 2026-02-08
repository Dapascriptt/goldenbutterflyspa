@extends('layouts.app')

@section('title', 'Tambah Omset')
@section('page_title', 'Tambah Omset')

@section('content')
    <div class="rounded-2xl bg-white border border-[#eadfce] p-6">
        <form method="POST" action="{{ route('manage.omset.store') }}" class="space-y-4">
            @csrf
            <div>
                <label class="text-sm font-medium text-slate-600">Keterangan</label>
                <input type="text" name="keterangan" class="mt-1 w-full rounded-lg border-[#eadfce]" placeholder="Contoh: Omset harian">
            </div>
            <div>
                <label class="text-sm font-medium text-slate-600">Jumlah</label>
                <input type="number" name="jumlah" class="mt-1 w-full rounded-lg border-[#eadfce]" placeholder="0">
            </div>
            <button type="submit" class="px-4 py-2 rounded-lg bg-[#9c7a4c] text-white text-sm font-medium hover:bg-[#7b5f3d]">
                Simpan
            </button>
        </form>
    </div>
@endsection
