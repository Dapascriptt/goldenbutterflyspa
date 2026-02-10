<?php

namespace App\Http\Controllers\Manage;

use App\Http\Controllers\Controller;
use App\Http\Requests\TherapistNameStoreRequest;
use App\Models\TherapistName;

class TherapistNameController extends Controller
{
    public function store(TherapistNameStoreRequest $request)
    {
        $data = $request->validated();

        TherapistName::create([
            'name' => $data['name'],
            'active' => true,
        ]);

        if (($data['redirect'] ?? '') === 'summary') {
            return redirect()
                ->route('manage.therapist.summary', [
                    'month' => $data['month'] ?? now()->month,
                    'year' => $data['year'] ?? now()->year,
                ])
                ->with('status', 'Nama therapist ditambahkan.');
        }

        return redirect()
            ->back()
            ->with('status', 'Nama therapist ditambahkan.');
    }

    public function destroy(int $id)
    {
        TherapistName::whereKey($id)->delete();

        return redirect()
            ->back()
            ->with('status', 'Nama therapist dihapus.');
    }
}
