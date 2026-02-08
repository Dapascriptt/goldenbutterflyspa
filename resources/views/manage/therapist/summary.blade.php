@extends('layouts.app')

@section('title', 'Summary Therapist')
@section('page_title', 'Summary Therapist')

@section('content')
    <div class="rounded-2xl bg-white border border-[#eadfce] p-6" x-data="{ showCreate: false, showEdit: false, selected: null }">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <h2 class="text-lg font-semibold text-[#4b2f1a]">Summary Therapist</h2>
                <p class="text-sm text-slate-500">Rekap per bulan (jumlah customer boleh duplikat nama).</p>
            </div>
            <div>
                @if (auth()->user()->isKasir())
                    <button type="button" @click="showCreate = true" class="px-4 py-2 rounded-lg bg-[#9c7a4c] text-white text-sm font-medium hover:bg-[#7b5f3d]">
                        Add Therapist
                    </button>
                @endif
            </div>
        </div>

        <form method="GET" action="{{ route('manage.therapist.summary') }}" class="mt-4 flex flex-wrap items-end gap-3">
            <div>
                <label class="text-xs font-semibold text-slate-500">Bulan</label>
                <select name="month" class="mt-1 w-full rounded-lg border-[#eadfce] text-sm">
                    @php
                        $months = [
                            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
                            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
                            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
                        ];
                    @endphp
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

        <div class="mt-6 overflow-x-auto">
            <table class="w-full text-sm min-w-[1100px] table-head-divider">
                <thead>
                    <tr class="text-left text-slate-500 border-b border-[#eadfce]">
                        <th class="py-3 whitespace-nowrap">No</th>
                        <th class="py-3 whitespace-nowrap">Nama Therapist</th>
                        <th class="py-3 whitespace-nowrap">Traditional 60</th>
                        <th class="py-3 whitespace-nowrap">Full Body 90</th>
                        <th class="py-3 whitespace-nowrap">Butterfly 90</th>
                        <th class="py-3 whitespace-nowrap">Extra Time (30 menit)</th>
                        <th class="py-3 whitespace-nowrap">Total Treatment</th>
                        <th class="py-3 whitespace-nowrap">Note</th>
                        <th class="py-3 whitespace-nowrap text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($rows as $index => $row)
                        @php
                            $totalTreatment = (int) $row->traditional + (int) $row->fullbody + (int) $row->butterfly;
                        @endphp
                        <tr class="border-b border-[#f1e7d8]">
                            <td class="py-2 whitespace-nowrap">{{ $index + 1 }}</td>
                            <td class="py-2 whitespace-nowrap font-medium text-[#4b2f1a]">{{ $row->therapist_name }}</td>
                            <td class="py-2 whitespace-nowrap text-center">{{ $row->traditional }}</td>
                            <td class="py-2 whitespace-nowrap text-center">{{ $row->fullbody }}</td>
                            <td class="py-2 whitespace-nowrap text-center">{{ $row->butterfly }}</td>
                            <td class="py-2 whitespace-nowrap text-center">{{ $row->extra_time }}</td>
                            <td class="py-2 whitespace-nowrap text-center font-semibold">{{ $totalTreatment }}</td>
                            <td class="py-2 whitespace-nowrap">{{ $row->room ?? '-' }}</td>
                            <td class="py-2 text-center">
                                <div class="flex justify-center gap-2">
                                    <button
                                        type="button"
                                        class="px-3 py-1 rounded-lg border border-[#9c7a4c] text-[#9c7a4c] text-xs font-semibold"
                                        @click="selected = @js([
                                            'id' => $row->id,
                                            'tanggal' => $row->date->format('Y-m-d'),
                                            'nama' => $row->therapist_name,
                                            'traditional' => $row->traditional,
                                            'fullbody' => $row->fullbody,
                                            'butterfly' => $row->butterfly,
                                            'extra_time' => $row->extra_time,
                                            'room' => $row->room,
                                        ]); showEdit = true"
                                    >
                                        Edit
                                    </button>
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
                            <td colspan="9" class="py-6 text-center text-slate-500">Belum ada data summary.</td>
                        </tr>
                    @endforelse
                </tbody>
                @if ($rows->count() > 0)
                    <tfoot>
                        <tr class="bg-[#f7f2eb]">
                            <td class="py-3 font-semibold text-[#4b2f1a]" colspan="5">Total</td>
                            <td class="py-3 text-center font-semibold text-white" style="background:#12a150;">
                                {{ $totalExtraTime }}
                            </td>
                            <td class="py-3 text-center font-semibold text-slate-900" style="background:#f2d74e;">
                                {{ $totalCustomers }}
                            </td>
                            <td class="py-3"></td>
                            <td class="py-3"></td>
                        </tr>
                    </tfoot>
                @endif
            </table>
        </div>

        <template x-if="showCreate">
            <div class="fixed inset-0 z-50 flex items-center justify-center">
                <div class="absolute inset-0 bg-black/40" @click="showCreate = false"></div>
                <div class="relative w-full max-w-xl rounded-2xl bg-white border border-[#eadfce] p-6 shadow-xl">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <h3 class="text-lg font-semibold text-[#4b2f1a]">Tambah Summary</h3>
                            <p class="text-sm text-slate-500">Input tanpa harga (summary bulanan).</p>
                        </div>
                        <button type="button" class="h-9 w-9 rounded-lg border border-[#eadfce] text-[#9c7a4c]" @click="showCreate = false">✕</button>
                    </div>
                    <form method="POST" action="{{ route('manage.therapist.store') }}" class="mt-5 space-y-4">
                        @csrf
                        <input type="hidden" name="redirect" value="summary">
                        <div>
                            <label class="text-sm font-medium text-slate-600">Tanggal</label>
                            <input type="date" name="tanggal" class="mt-1 w-full rounded-lg border-[#eadfce]" required>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-slate-600">Nama Therapist</label>
                            <input type="text" name="nama" class="mt-1 w-full rounded-lg border-[#eadfce]" required>
                        </div>
                        <div class="grid gap-3 md:grid-cols-2">
                            <div>
                                <label class="text-sm font-medium text-slate-600">Traditional 60</label>
                                <input type="number" name="traditional" min="0" value="0" class="mt-1 w-full rounded-lg border-[#eadfce]">
                            </div>
                            <div>
                                <label class="text-sm font-medium text-slate-600">Full Body 90</label>
                                <input type="number" name="fullbody" min="0" value="0" class="mt-1 w-full rounded-lg border-[#eadfce]">
                            </div>
                            <div>
                                <label class="text-sm font-medium text-slate-600">Butterfly 90</label>
                                <input type="number" name="butterfly" min="0" value="0" class="mt-1 w-full rounded-lg border-[#eadfce]">
                            </div>
                            <div>
                                <label class="text-sm font-medium text-slate-600">Extra Time (30 menit)</label>
                                <input type="number" name="extra_time" min="0" value="0" class="mt-1 w-full rounded-lg border-[#eadfce]">
                            </div>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-slate-600">Note</label>
                            <input type="text" name="room" class="mt-1 w-full rounded-lg border-[#eadfce]" placeholder="Catatan">
                        </div>
                        <div class="flex justify-end gap-2">
                            <button type="button" class="px-4 py-2 rounded-lg border border-[#eadfce] text-sm" @click="showCreate = false">Batal</button>
                            <button type="submit" class="px-4 py-2 rounded-lg bg-[#9c7a4c] text-white text-sm">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </template>

        <template x-if="showEdit">
            <div class="fixed inset-0 z-50 flex items-center justify-center">
                <div class="absolute inset-0 bg-black/40" @click="showEdit = false"></div>
                <div class="relative w-full max-w-xl rounded-2xl bg-white border border-[#eadfce] p-6 shadow-xl">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <h3 class="text-lg font-semibold text-[#4b2f1a]">Edit Summary</h3>
                            <p class="text-sm text-slate-500">Perbarui data summary tanpa harga.</p>
                        </div>
                        <button type="button" class="h-9 w-9 rounded-lg border border-[#eadfce] text-[#9c7a4c]" @click="showEdit = false">✕</button>
                    </div>
                    <form method="POST" x-bind:action="selected ? `{{ url('/manage/therapist') }}/${selected.id}` : ''" class="mt-5 space-y-4">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="redirect" value="summary">
                        <div>
                            <label class="text-sm font-medium text-slate-600">Tanggal</label>
                            <input type="date" name="tanggal" class="mt-1 w-full rounded-lg border-[#eadfce]" x-model="selected.tanggal" required>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-slate-600">Nama Therapist</label>
                            <input type="text" name="nama" class="mt-1 w-full rounded-lg border-[#eadfce]" x-model="selected.nama" required>
                        </div>
                        <div class="grid gap-3 md:grid-cols-2">
                            <div>
                                <label class="text-sm font-medium text-slate-600">Traditional 60</label>
                                <input type="number" name="traditional" min="0" class="mt-1 w-full rounded-lg border-[#eadfce]" x-model="selected.traditional">
                            </div>
                            <div>
                                <label class="text-sm font-medium text-slate-600">Full Body 90</label>
                                <input type="number" name="fullbody" min="0" class="mt-1 w-full rounded-lg border-[#eadfce]" x-model="selected.fullbody">
                            </div>
                            <div>
                                <label class="text-sm font-medium text-slate-600">Butterfly 90</label>
                                <input type="number" name="butterfly" min="0" class="mt-1 w-full rounded-lg border-[#eadfce]" x-model="selected.butterfly">
                            </div>
                            <div>
                                <label class="text-sm font-medium text-slate-600">Extra Time (30 menit)</label>
                                <input type="number" name="extra_time" min="0" class="mt-1 w-full rounded-lg border-[#eadfce]" x-model="selected.extra_time">
                            </div>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-slate-600">Note</label>
                            <input type="text" name="room" class="mt-1 w-full rounded-lg border-[#eadfce]" x-model="selected.room">
                        </div>
                        <div class="flex justify-end gap-2">
                            <button type="button" class="px-4 py-2 rounded-lg border border-[#eadfce] text-sm" @click="showEdit = false">Batal</button>
                            <button type="submit" class="px-4 py-2 rounded-lg bg-[#9c7a4c] text-white text-sm">Update</button>
                        </div>
                    </form>
                </div>
            </div>
        </template>
    </div>
@endsection
