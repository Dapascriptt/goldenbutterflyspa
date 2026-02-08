<!DOCTYPE html>
<html lang="id">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ $title ?? 'Laporan Therapist' }}</title>
        <style>
            @page { margin: 12mm; }
            body { font-family: Arial, sans-serif; color: #1f2937; }
            .page { width: 100%; margin: 0; }
            .title { font-size: 20px; font-weight: bold; margin-bottom: 4px; }
            .subtitle { font-size: 12px; color: #6b7280; margin-bottom: 20px; }
            table { width: 100%; border-collapse: collapse; font-size: 12px; }
            th, td { border: 1px solid #e5e7eb; padding: 8px; text-align: left; }
            th { background: #f3f4f6; }
        </style>
    </head>
    <body>
        <div class="page">
            <div class="title">{{ $title ?? 'Laporan Therapist' }}</div>
            <div class="subtitle">Dicetak pada {{ now()->format('d M Y H:i') }}</div>

            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nama</th>
                        <th>Spesialisasi</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($rows ?? [] as $row)
                        <tr>
                            <td>{{ $row[0] }}</td>
                            <td>{{ $row[1] }}</td>
                            <td>{{ $row[2] }}</td>
                            <td>{{ $row[3] }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </body>
</html>
