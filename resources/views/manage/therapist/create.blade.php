@extends('layouts.app')

@section('title', 'Add Therapist')
@section('page_title', 'Add Therapist')

@section('content')
    <div class="rounded-2xl bg-white border border-[#eadfce] p-6">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-lg font-semibold text-[#4b2f1a]">Input Therapist</h2>
                <p class="text-sm text-slate-500">Charge otomatis sesuai paket, extra time, diskon, dan add-ons.</p>
            </div>
            <a href="{{ route('manage.therapist.index') }}" class="px-4 py-2 rounded-lg border border-[#eadfce] text-sm font-medium text-slate-600">
                Kembali
            </a>
        </div>

        <form method="POST" action="{{ route('manage.therapist.store') }}" class="mt-6" x-data="therapistRow()">
            @csrf
            <div class="grid gap-4 md:grid-cols-2">
                <div>
                    <label class="text-sm font-medium text-slate-600">Tanggal</label>
                    <input type="date" name="tanggal" class="mt-1 w-full rounded-lg border-[#eadfce]">
                </div>
                <div>
                    <label class="text-sm font-medium text-slate-600">Waktu (24 jam)</label>
                    <input type="time" name="waktu" class="mt-1 w-full rounded-lg border-[#eadfce]">
                </div>
                <div>
                    <label class="text-sm font-medium text-slate-600">Nama Therapist</label>
                    <input type="text" name="nama" class="mt-1 w-full rounded-lg border-[#eadfce]" placeholder="Nama therapist">
                </div>
                <div>
                    <label class="text-sm font-medium text-slate-600">Room</label>
                    <input type="text" name="room" class="mt-1 w-full rounded-lg border-[#eadfce]" placeholder="Room">
                </div>
                <div>
                    <label class="text-sm font-medium text-slate-600">Extra Time (30 menit)</label>
                    <input type="number" min="0" name="extra_time" class="mt-1 w-full rounded-lg border-[#eadfce]" x-model.number="extraTime">
                </div>
                <div>
                    <label class="text-sm font-medium text-slate-600">Charge Extra Time</label>
                    <div class="mt-1 rounded-lg border border-[#eadfce] bg-slate-50 px-3 py-2 text-sm font-semibold text-[#4b2f1a]" x-text="formatRupiah(extraTimeCharge)"></div>
                </div>
                <div>
                    <label class="text-sm font-medium text-slate-600">Traditional 60</label>
                    <input type="number" min="0" name="traditional" class="mt-1 w-full rounded-lg border-[#eadfce]" x-model.number="traditional">
                </div>
                <div>
                    <label class="text-sm font-medium text-slate-600">Fullbody 90</label>
                    <input type="number" min="0" name="fullbody" class="mt-1 w-full rounded-lg border-[#eadfce]" x-model.number="fullbody">
                </div>
                <div>
                    <label class="text-sm font-medium text-slate-600">Butterfly 90</label>
                    <input type="number" min="0" name="butterfly" class="mt-1 w-full rounded-lg border-[#eadfce]" x-model.number="butterfly">
                </div>
                <div class="flex items-center gap-3">
                    <input type="checkbox" name="shockwave" class="h-4 w-4" x-model="shockwave">
                    <label class="text-sm font-medium text-slate-600">Add-ons Shock Wave (Rp 250.000)</label>
                </div>
                <div>
                    <label class="text-sm font-medium text-slate-600">Discount (%)</label>
                    <input type="number" min="0" name="discount_percent" class="mt-1 w-full rounded-lg border-[#eadfce]" x-model.number="discountPercent">
                </div>
                <div>
                    <label class="text-sm font-medium text-slate-600">Discount (Rp)</label>
                    <input type="number" min="0" name="discount_nominal" class="mt-1 w-full rounded-lg border-[#eadfce]" x-model.number="discountNominal">
                </div>
                <div>
                    <label class="text-sm font-medium text-slate-600">Room Charge (Rp)</label>
                    <input type="number" min="0" name="room_charge" class="mt-1 w-full rounded-lg border-[#eadfce]" x-model.number="roomCharge">
                </div>
                <div>
                    <label class="text-sm font-medium text-slate-600">Total Charge</label>
                    <div class="mt-1 rounded-lg border border-[#eadfce] bg-[#f7f2eb] px-3 py-2 text-sm font-semibold text-[#4b2f1a]" x-text="formatRupiah(totalCharge)"></div>
                </div>
            </div>

            <div class="mt-6 flex justify-end gap-2">
                <button type="submit" class="px-4 py-2 rounded-lg bg-[#9c7a4c] text-white text-sm font-medium">
                    Simpan
                </button>
            </div>
        </form>
    </div>

    <script>
        function therapistRow() {
            return {
                extraTime: 0,
                traditional: 0,
                fullbody: 0,
                butterfly: 0,
                shockwave: false,
                discountPercent: 0,
                discountNominal: 0,
                roomCharge: 0,
                get extraTimeCharge() {
                    return (this.extraTime || 0) * 150000;
                },
                get packageCharge() {
                    return (this.traditional || 0) * 400000
                        + (this.fullbody || 0) * 550000
                        + (this.butterfly || 0) * 700000;
                },
                get addOnCharge() {
                    return this.shockwave ? 250000 : 0;
                },
                get subtotal() {
                    return this.extraTimeCharge + this.packageCharge + this.addOnCharge + (this.roomCharge || 0);
                },
                get discountValue() {
                    const percent = Math.min(Math.max(this.discountPercent || 0, 0), 100);
                    return Math.round((this.subtotal * percent) / 100);
                },
                get totalCharge() {
                    const total = this.subtotal - this.discountValue - (this.discountNominal || 0);
                    return Math.max(total, 0);
                },
                formatRupiah(value) {
                    const number = Number(value || 0);
                    return 'Rp ' + number.toLocaleString('id-ID');
                }
            };
        }
    </script>
@endsection
