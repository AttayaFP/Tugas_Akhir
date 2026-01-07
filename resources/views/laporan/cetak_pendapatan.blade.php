<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Laporan Pendapatan</title>
    <style>
        body { font-family: sans-serif; padding: 20px; }
        h1, h2, h3 { text-align: center; margin: 0; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; font-size: 12px; }
        th, td { border: 1px solid #000; padding: 8px; text-align: left; }
        th { background-color: #f0f0f0; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .metadata { margin-top: 20px; font-size: 14px; }
        .grand-total { font-size: 14px; font-weight: bold; background-color: #f0f0f0; }
    </style>
</head>
<body onload="window.print()">
    <h2>DIGITAL PRINTING DASHBOARD</h2>
    <h1>LAPORAN PENDAPATAN</h1>
    
    <div class="metadata">
        @if($start_date && $end_date)
            <p>Periode: {{ \Carbon\Carbon::parse($start_date)->format('d/m/Y') }} s/d {{ \Carbon\Carbon::parse($end_date)->format('d/m/Y') }}</p>
        @else
            <p>Periode: Semua Data</p>
        @endif
        <p>Dicetak Pada: {{ now()->format('d/m/Y H:i') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 5%">No</th>
                <th>Tanggal</th>
                <th class="text-center">Jumlah Transaksi</th>
                <th class="text-right">Total Pendapatan (Rp)</th>
            </tr>
        </thead>
        <tbody>
            @forelse($data as $index => $item)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>{{ \Carbon\Carbon::parse($item->date)->format('d M Y') }}</td>
                <td class="text-center">{{ $item->total_orders }}</td>
                <td class="text-right">{{ number_format($item->total_income, 0, ',', '.') }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="4" class="text-center">Tidak ada data pendapatan pada periode ini.</td>
            </tr>
            @endforelse
            <tr class="grand-total">
                <td colspan="3" class="text-right">GRAND TOTAL</td>
                <td class="text-right">Rp {{ number_format($grand_total, 0, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>
</body>
</html>
