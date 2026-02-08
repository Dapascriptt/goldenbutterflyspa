@extends('layouts.app')

@section('title', 'Exports')
@section('page_title', 'Exports')

@section('content')
    <div class="rounded-2xl bg-white border border-[#eadfce] p-6">
        <div>
            <h2 class="text-lg font-semibold text-[#4b2f1a]">Daftar Export</h2>
            <p class="text-sm text-slate-500">File hasil export yang siap diunduh.</p>
        </div>

        <div class="mt-6 overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left text-slate-500 border-b border-[#eadfce]">
                        <th class="py-3">Nama File</th>
                        <th class="py-3">Ukuran</th>
                        <th class="py-3">Diperbarui</th>
                        <th class="py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($files as $file)
                        <tr class="border-b border-[#f1e7d8]">
                            <td class="py-3">{{ $file['name'] }}</td>
                            <td class="py-3">{{ number_format($file['size'] / 1024, 1) }} KB</td>
                            <td class="py-3">{{ \Illuminate\Support\Carbon::createFromTimestamp($file['modified'])->format('d/m/Y H:i') }}</td>
                            <td class="py-3 text-right">
                                <a href="{{ route('exports.download', $file['name']) }}" class="px-3 py-1 rounded-lg border border-[#9c7a4c] text-[#9c7a4c] text-xs font-semibold">
                                    Download
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="py-6 text-center text-slate-500">Belum ada file export.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
