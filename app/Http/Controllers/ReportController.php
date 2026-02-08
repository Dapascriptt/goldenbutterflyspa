<?php

namespace App\Http\Controllers;

use App\Exports\ReportsExport;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        return view('reports.index');
    }

    public function exportExcel()
    {
        return Excel::download(new ReportsExport(), 'reports.xlsx');
    }

    public function exportPdf()
    {
        $pdf = Pdf::loadView('reports.print', [
            'title' => 'Laporan Golden Spa',
            'rows' => ReportsExport::dummyRows(),
        ])->setPaper('a4');

        return $pdf->download('reports.pdf');
    }

    public function print()
    {
        return view('reports.print', [
            'title' => 'Laporan Golden Spa',
            'rows' => ReportsExport::dummyRows(),
        ]);
    }
}
