<?php

namespace App\Http\Controllers\Manage;

use App\Http\Controllers\Controller;
use App\Exports\TherapistExport;
use App\Http\Requests\TherapistStoreRequest;
use App\Http\Requests\TherapistUpdateRequest;
use App\Models\TherapistCharge;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;

class TherapistController extends Controller
{
    public function index()
    {
        $query = TherapistCharge::query();

        $filter = request('filter', 'month');

        if ($filter === 'day' && request('date')) {
            $query->whereDate('date', request('date'));
        } elseif ($filter === 'ten_days' && request('start_date')) {
            $start = Carbon::parse(request('start_date'))->startOfDay();
            $end = (clone $start)->addDays(9)->endOfDay();
            $query->whereBetween('date', [$start->toDateString(), $end->toDateString()]);
        } else {
            $month = (int) request('month', now()->month);
            $year = (int) request('year', now()->year);
            $query->whereMonth('date', $month)->whereYear('date', $year);
        }

        $rows = $query->orderBy('date')->orderBy('time')->paginate(25)->appends(request()->query());

        return view('manage.therapist.index', [
            'rows' => $rows,
        ]);
    }

    public function summary()
    {
        return view('manage.therapist.summary');
    }

    public function create()
    {
        return view('manage.therapist.create');
    }

    public function store(TherapistStoreRequest $request)
    {
        $data = $request->validated();

        $extraTime = (int) ($data['extra_time'] ?? 0);
        $traditional = (int) ($data['traditional'] ?? 0);
        $fullbody = (int) ($data['fullbody'] ?? 0);
        $butterfly = (int) ($data['butterfly'] ?? 0);
        $shockwave = (bool) ($data['shockwave'] ?? false);
        $discountPercent = (int) ($data['discount_percent'] ?? 0);
        $discountNominal = (int) ($data['discount_nominal'] ?? 0);
        $roomCharge = (int) ($data['room_charge'] ?? 0);

        $extraCharge = $extraTime * 150000;
        $packageCharge = ($traditional * 400000) + ($fullbody * 550000) + ($butterfly * 700000);
        $addOnCharge = $shockwave ? 250000 : 0;
        $subtotal = $extraCharge + $packageCharge + $addOnCharge + $roomCharge;
        $discountValue = (int) round($subtotal * ($discountPercent / 100));
        $total = max($subtotal - $discountValue - $discountNominal, 0);

        $record = TherapistCharge::create([
            'date' => $data['tanggal'],
            'time' => $data['waktu'] ?? null,
            'therapist_name' => $data['nama'],
            'extra_time' => $extraTime,
            'extra_charge' => $extraCharge,
            'traditional' => $traditional,
            'fullbody' => $fullbody,
            'butterfly' => $butterfly,
            'shockwave' => $shockwave,
            'discount_percent' => $discountPercent,
            'discount_nominal' => $discountNominal,
            'room_charge' => $roomCharge,
            'total_charge' => $total,
            'room' => $data['room'] ?? null,
        ]);

        $date = Carbon::parse($record->date);

        return redirect()
            ->route('manage.therapist.index', [
                'year' => $date->year,
                'month' => $date->month,
                'date' => $date->toDateString(),
            ])
            ->with('status', 'Therapist tersimpan.');
    }

    public function edit(int $id)
    {
        return view('manage.therapist.edit', ['id' => $id]);
    }

    public function update(TherapistUpdateRequest $request, int $id)
    {
        $request->validated();

        return redirect()
            ->route('manage.therapist.index')
            ->with('status', 'Therapist diperbarui (dummy).');
    }

    public function destroy(int $id)
    {
        TherapistCharge::whereKey($id)->delete();

        return redirect()
            ->route('manage.therapist.index')
            ->with('status', 'Therapist dihapus.');
    }

    public function exportExcel()
    {
        return Excel::download(new TherapistExport(), 'therapist.xlsx');
    }

    public function exportPdf()
    {
        $pdf = Pdf::loadView('manage.therapist.print', [
            'title' => 'Laporan Therapist',
            'rows' => TherapistExport::dummyRows(),
        ])->setPaper('a4');

        return $pdf->download('therapist.pdf');
    }

    public function print()
    {
        return view('manage.therapist.print', [
            'title' => 'Laporan Therapist',
            'rows' => TherapistExport::dummyRows(),
        ]);
    }
}
