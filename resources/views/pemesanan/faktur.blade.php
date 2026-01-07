<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Faktur #{{ $p->no_nota }}</title>
    <style>
        body { font-family: 'Helvetica', 'Arial', sans-serif; color: #333; line-height: 1.5; }
        .invoice-box { max-width: 800px; margin: auto; padding: 30px; border: 1px solid #eee; }
        .header { display: table; width: 100%; border-bottom: 2px solid #f06292; padding-bottom: 20px; margin-bottom: 20px; }
        .logo { display: table-cell; vertical-align: middle; }
        .title { display: table-cell; text-align: right; vertical-align: middle; }
        .title h1 { margin: 0; color: #f06292; font-size: 28px; font-weight: 900; }
        .info { display: table; width: 100%; margin-bottom: 40px; }
        .info-col { display: table-cell; width: 50%; }
        .info-col h4 { margin: 0 0 5px 0; color: #888; text-transform: uppercase; font-size: 10px; letter-spacing: 1px; }
        .info-col p { margin: 0; font-weight: bold; font-size: 14px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 30px; }
        table th { background: #f8f9fa; color: #555; text-align: left; padding: 12px; border-bottom: 1px solid #ddd; font-size: 12px; text-transform: uppercase; }
        table td { padding: 12px; border-bottom: 1px solid #eee; font-size: 13px; }
        .total-section { text-align: right; }
        .total-row { display: table; width: 250px; margin-left: auto; margin-bottom: 5px; }
        .total-label { display: table-cell; text-align: right; padding-right: 20px; color: #888; font-size: 12px; }
        .total-value { display: table-cell; text-align: right; font-weight: 900; font-size: 14px; }
        .grand-total { border-top: 2px solid #f06292; padding-top: 10px; margin-top: 10px; color: #f06292; }
        .footer { text-align: center; margin-top: 50px; color: #999; font-size: 10px; }
        .badge { display: inline-block; padding: 4px 10px; border-radius: 20px; font-size: 10px; font-weight: bold; text-transform: uppercase; }
        .badge-lunas { background: #e8f5e9; color: #2e7d32; }
        .badge-dp { background: #fff3e0; color: #ef6c00; }
        .badge-pending { background: #ffebee; color: #c62828; }
    </style>
</head>
<body>
    <div class="invoice-box">
        <div class="header">
            <div class="logo">
                <span style="font-size: 24px; font-weight: 900; color: #333;">DIGIPRIN<span style="color: #f06292;">.BIZ</span></span>
            </div>
            <div class="title">
                <h1>FAKTUR</h1>
                <p style="margin: 0; font-size: 12px; color: #888;">#{{ $p->no_nota }}</p>
            </div>
        </div>

        <div class="info">
            <div class="info-col">
                <h4>Pelanggan</h4>
                <p>{{ $p->pelanggan->nama_lengkap ?? 'Guest' }}</p>
                <p style="font-weight: normal; font-size: 12px; color: #555;">{{ $p->pelanggan->no_telepon ?? '-' }}</p>
            </div>
            <div class="info-col" style="text-align: right;">
                <h4>Tanggal Pesan</h4>
                <p>{{ $p->tanggal_pesan->format('d F Y') }}</p>
                <div style="margin-top: 10px;">
                    @if($p->status_pembayaran == 'Lunas')
                        <span class="badge badge-lunas">Lunas</span>
                    @elseif($p->status_pembayaran == 'DP')
                        <span class="badge badge-dp">DP Payment</span>
                    @else
                        <span class="badge badge-pending">Belum Lunas</span>
                    @endif
                </div>
            </div>
        </div>

        <table>
            <thead>
                <tr>
                    <th>Deskripsi Layanan</th>
                    <th style="text-align: center;">Jumlah</th>
                    <th style="text-align: right;">Harga Satuan</th>
                    <th style="text-align: right;">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @php $grandTotal = 0; @endphp
                @foreach($items as $item)
                    @php 
                        $sub = ($item->layanan->harga_per_satuan ?? 0) * $item->jumlah;
                        $grandTotal += $sub;
                    @endphp
                    <tr>
                        <td>{{ $item->layanan->nama_layanan ?? 'Unknown' }}</td>
                        <td style="text-align: center;">{{ $item->jumlah }}</td>
                        <td style="text-align: right;">Rp {{ number_format($item->layanan->harga_per_satuan ?? 0, 0, ',', '.') }}</td>
                        <td style="text-align: right;">Rp {{ number_format($sub, 0, ',', '.') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="total-section">
            <div class="total-row">
                <div class="total-label">Total Harga</div>
                <div class="total-value">Rp {{ number_format($grandTotal, 0, ',', '.') }}</div>
            </div>
            <div class="total-row">
                <div class="total-label">Uang Muka / DP</div>
                <div class="total-value">Rp {{ number_format($p->uang_muka, 0, ',', '.') }}</div>
            </div>
            <div class="total-row grand-total">
                <div class="total-label" style="color: #f06292; font-weight: bold;">Sisa Tagihan</div>
                <div class="total-value">Rp {{ number_format($grandTotal - $p->uang_muka, 0, ',', '.') }}</div>
            </div>
        </div>

        <div class="footer">
            <p>Terima kasih telah mempercayai DIGIPRIN.BIZ</p>
            <p>Dokumen ini diterbitkan secara digital dan sah tanpa tanda tangan.</p>
        </div>
    </div>
</body>
</html>
