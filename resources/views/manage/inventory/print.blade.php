<!DOCTYPE html>
<html lang="id">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ $title ?? 'Laporan Inventory' }}</title>
        <style>
            @page { margin: 10mm; }
            body { font-family: Arial, sans-serif; color: #1f2937; font-size: 11px; }
            .page { width: 100%; margin: 0; }
            .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 14px; }
            .title { font-size: 18px; font-weight: bold; margin-bottom: 2px; }
            .subtitle { font-size: 11px; color: #6b7280; }
            .meta { font-size: 11px; color: #6b7280; text-align: right; }
            table { width: 100%; border-collapse: collapse; font-size: 10px; }
            th, td { border: 1px solid #e5e7eb; padding: 6px 8px; text-align: left; }
            th { background: #f7f2eb; color: #4b2f1a; font-weight: 600; }
            tbody tr:nth-child(even) { background: #fbf8f2; }
            .num { text-align: right; }
        </style>
    </head>
    <body>
        <div class="page">
            <div class="header">
                <div>
                    <div class="title">{{ $title ?? 'Laporan Inventory' }}</div>
                    @if (!empty($period))
                        <div class="subtitle">
                            Periode {{ $period['label'] }} ({{ $period['start_date'] }} s/d {{ $period['end_date'] }})
                        </div>
                    @else
                        <div class="subtitle">Ringkasan data inventory.</div>
                    @endif
                </div>
                <div class="meta">Dicetak pada {{ now()->format('d M Y H:i') }}</div>
            </div>

            <table>
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Item</th>
                        <th>Stok Awal</th>
                        <th>Stok Akhir</th>
                        <th>Satuan</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($rows ?? [] as $row)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $row[1] }}</td>
                            <td class="num">{{ $row[2] }}</td>
                            <td class="num">{{ $row[3] }}</td>
                            <td>{{ $row[4] }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </body>
</html>
