<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Data Pelanggan</title>
    <style>
        body { font-family: sans-serif; padding: 20px; }
        h1, h2 { text-align: center; margin: 0; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; font-size: 12px; }
        th, td { border: 1px solid #000; padding: 8px; text-align: left; }
        th { background-color: #f0f0f0; }
        .text-center { text-align: center; }
        .metadata { margin-top: 20px; font-size: 14px; }
    </style>
</head>
<body onload="window.print()">
    <h2>DIGITAL PRINTING DASHBOARD</h2>
    <h1>LAPORAN DATA PELANGGAN</h1>
    
    <div class="metadata">
        <p>Total Pelanggan: {{ $data->count() }}</p>
        <p>Dicetak Pada: {{ now()->format('d/m/Y H:i') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 5%">No</th>
                <th>Kode Member</th>
                <th>Nama Pelanggan</th>
                <th>Kategori</th>
                <th>No Telepon</th>
                <th>Email</th>
                <th>Alamat</th>
            </tr>
        </thead>
        <tbody>
            @forelse($data as $index => $item)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>{{ $item->kode_member ?? '-' }}</td>
                <td>{{ $item->nama_lengkap }}</td>
                <td>{{ $item->kategori_pelanggan }}</td>
                <td>{{ $item->no_telepon }}</td>
                <td>{{ $item->email }}</td>
                <td>{{ $item->alamat }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="text-center">Belum ada data pelanggan.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
