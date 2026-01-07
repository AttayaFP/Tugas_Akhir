<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Laporan Pemesanan</title>
    <style>
        body { font-family: sans-serif; padding: 20px; }
        h1, h2, h3 { text-align: center; margin: 0; }
        h2 { margin-bottom: 5px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; font-size: 12px; }
        th, td { border: 1px solid #000; padding: 8px; text-align: left; }
        th { background-color: #f0f0f0; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .metadata { margin-top: 20px; font-size: 14px; }
        @media print {
            .no-print { display: none; }
        }
    </style>
</head>
<body onload="window.print()">
    <h2>DIGITAL PRINTING DASHBOARD</h2>
    <h1>LAPORAN RIWAYAT PEMESANAN</h1>
    
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
                <th>No Nota</th>
                <th>Tanggal</th>
                <th>Pelanggan</th>
                <th>Layanan</th>
                <th>Status</th>
                <th class="text-right">Total Harga</th>
            </tr>
        </thead>
        <tbody>
            @forelse($data as $index => $item)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>{{ $item->no_nota }}</td>
                <td>{{ \Carbon\Carbon::parse($item->tanggal_pesan)->format('d/m/Y') }}</td>
                <td>{{ $item->pelanggan->nama ?? '-' }}</td>
                <td>{{ $item->layanan->nama_layanan ?? '-' }}</td>
                <td>{{ $item->status_pesanan }}</td>
                <td class="text-right">Rp {{ number_format($item->total_harga, 0, ',', '.') }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="text-center">Tidak ada data transaksi pada periode ini.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
