<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Data Layanan</title>
    <style>
        body { font-family: sans-serif; padding: 20px; }
        h1, h2 { text-align: center; margin: 0; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; font-size: 12px; }
        th, td { border: 1px solid #000; padding: 8px; text-align: left; }
        th { background-color: #f0f0f0; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .metadata { margin-top: 20px; font-size: 14px; }
    </style>
</head>
<body onload="window.print()">
    <h2>DIGITAL PRINTING DASHBOARD</h2>
    <h1>LAPORAN DATA LAYANAN</h1>
    
    <div class="metadata">
        <p>Total Layanan: {{ $data->count() }}</p>
        <p>Dicetak Pada: {{ now()->format('d/m/Y H:i') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 5%">No</th>
                <th>Nama Layanan</th>
                <th>Deskripsi</th>
                <th class="text-center">Satuan</th>
                <th class="text-right">Harga / Satuan</th>
                <th class="text-center">Minimal Order</th>
                <th class="text-center">Estimasi Waktu</th>
            </tr>
        </thead>
        <tbody>
            @forelse($data as $index => $item)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>{{ $item->nama_layanan }}</td>
                <td>{{ $item->deskripsi }}</td>
                <td class="text-center">{{ $item->satuan }}</td>
                <td class="text-right">Rp {{ number_format($item->harga_per_satuan, 0, ',', '.') }}</td>
                <td class="text-center">{{ $item->minimal_order }} {{ $item->satuan }}</td>
                <td class="text-center">{{ $item->estimasi_waktu }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="text-center">Belum ada data layanan.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
