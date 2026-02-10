<!DOCTYPE html>
<html lang="id">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ $title ?? 'Laporan Therapist' }}</title>
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
            .center { text-align: center; }
            .badge { display: inline-block; padding: 2px 6px; border-radius: 999px; background: #f3ede4; color: #9c7a4c; font-size: 9px; }
        </style>
    </head>
    <body>
        <div class="page">
            <div class="header">
                <div>
                    <div class="title">{{ $title ?? 'Laporan Therapist' }}</div>
                    <div class="subtitle">
                        @if (!empty($meta['label']))
                            Periode: {{ $meta['label'] }}
                        @elseif (!empty($meta['period']))
                            Periode: {{ $meta['period'] }}
                        @else
                            Ringkasan data therapist.
                        @endif
                    </div>
                </div>
                <div class="meta">
                    Dicetak pada {{ now()->format('d M Y H:i') }}<br>
                    <span class="badge">{{ strtoupper($mode ?? 'detail') }}</span>
                </div>
            </div>

            @php
                $fmt = fn ($v) => 'Rp '.number_format((int) $v, 0, ',', '.');
            @endphp

            <table>
                <thead>
                    <tr>
                        @foreach ($headings ?? [] as $heading)
                            <th>{{ $heading }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @forelse ($rows ?? [] as $row)
                        <tr>
                            @foreach ($row as $index => $cell)
                                @php
                                    $isMoney = ($mode ?? 'detail') === 'detail' && in_array($index, [4,10,11,12], true);
                                @endphp
                                <td class="{{ is_numeric($cell) ? 'num' : '' }}">
                                    {{ $isMoney ? $fmt($cell) : $cell }}
                                </td>
                            @endforeach
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ count($headings ?? []) }}" class="center">Tidak ada data.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            @if (($mode ?? '') === 'monthly' && !empty($meta['chart']))
                @php
                    $labels = $meta['chart']['labels'] ?? [];
                    $extra = $meta['chart']['extra_time'] ?? [];
                    $total = $meta['chart']['total_treatment'] ?? [];
                    $maxValue = max(array_merge([0], $extra, $total));
                @endphp
                <div style="margin-top: 18px;">
                    <div style="font-weight: 600; color: #4b2f1a; margin-bottom: 6px;">Diagram Extra Time vs Total Treatment</div>
                    <div style="margin-top: 6px; margin-bottom: 6px; font-size: 9px; color:#6b7280;">
                        <span style="display:inline-block; width:10px; height:10px; background:#d8cfb4; margin-right:6px; vertical-align:middle;"></span>
                        Extra Time (30 menit)
                        <span style="display:inline-block; width:10px; height:10px; background:#3f3728; margin:0 6px 0 14px; vertical-align:middle;"></span>
                        Total Treatment
                    </div>
                    <div style="border:1px solid #eadfce; padding:10px;">
                        <table style="width: 100%; border-collapse: separate; border-spacing: 18px 0; font-size: 9px;">
                            <tr>
                                @foreach ($labels as $i => $label)
                                    @php
                                        $extraVal = (int) ($extra[$i] ?? 0);
                                        $totalVal = (int) ($total[$i] ?? 0);
                                        $extraH = $maxValue > 0 ? round(($extraVal / $maxValue) * 140) : 0;
                                        $totalH = $maxValue > 0 ? round(($totalVal / $maxValue) * 140) : 0;
                                    @endphp
                                    <td style="width: 40px; text-align:center; vertical-align:bottom; padding:0 4px;">
                                        <div style="position: relative; height:160px;">
                                            <div style="position:absolute; left:50%; bottom:0; width:12px; height:{{ $extraH }}px; margin-left:-12px; background:#d8cfb4;"></div>
                                            <div style="position:absolute; left:50%; bottom:0; width:12px; height:{{ $totalH }}px; margin-left:2px; background:#3f3728;"></div>
                                        </div>
                                        <div style="margin-top:6px; color:#6b7280; white-space:nowrap;">{{ $label }}</div>
                                    </td>
                                @endforeach
                            </tr>
                        </table>
                    </div>
                </div>
            @endif
        </div>
    </body>
</html>
