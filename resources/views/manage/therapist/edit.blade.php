@extends('layouts.app')

@section('title', 'Edit Therapist')
@section('page_title', 'Edit Therapist')

@section('content')
    <div class="rounded-2xl bg-white border border-[#eadfce] p-6">
        <form method="POST" action="{{ route('manage.therapist.update', $id ?? 1) }}" class="space-y-4">
            @csrf
            @method('PUT')
            <div>
                <label class="text-sm font-medium text-slate-600">Nama Therapist</label>
                <input type="text" name="nama" class="mt-1 w-full rounded-lg border-[#eadfce]" value="Therapist A">
            </div>
            <div>
                <label class="text-sm font-medium text-slate-600">Spesialisasi</label>
                <input type="text" name="spesialisasi" class="mt-1 w-full rounded-lg border-[#eadfce]" value="Massage">
            </div>
            <button type="submit" class="px-4 py-2 rounded-lg bg-[#9c7a4c] text-white text-sm font-medium hover:bg-[#7b5f3d]">
                Update
            </button>
        </form>
    </div>
@endsection
