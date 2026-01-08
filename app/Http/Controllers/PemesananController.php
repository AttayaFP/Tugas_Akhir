<?php

namespace App\Http\Controllers;

use App\Models\Pemesanan;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class PemesananController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $tanggal = $request->input('tanggal');

        // Grouping by no_nota for the list
        $pemesanan = Pemesanan::with(['pelanggan', 'layanan'])
            ->when($search, function ($q) use ($search) {
                $q->where(function ($inner) use ($search) {
                    $inner->where('no_nota', 'like', "%{$search}%")
                        ->orWhereHas('pelanggan', function ($pq) use ($search) {
                            $pq->where('nama_lengkap', 'like', "%{$search}%");
                        });
                });
            })
            ->when($tanggal, function ($q) use ($tanggal) {
                $q->whereDate('tanggal_pesan', $tanggal);
            })
            ->latest()
            ->get()
            ->unique('no_nota'); // Use unique for grouping in the list

        if ($request->ajax()) {
            return view('pemesanan.partials._table', compact('pemesanan'))->render();
        }

        return view('pemesanan.index', compact('pemesanan'));
    }

    public function create()
    {
        $pelanggans = \App\Models\Pelanggan::all();
        $layanans = \App\Models\Layanan::all();
        return view('pemesanan.create', compact('pelanggans', 'layanans'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_pelanggan' => 'required|exists:pelanggans,id',
            'items' => 'required|array|min:1',
            'items.*.id_layanan' => 'required|exists:layanans,id',
            'items.*.jumlah' => 'required|integer|min:1',
            'keterangan' => 'nullable|string',
            'uang_muka' => 'nullable|numeric|min:0',
            'metode_pembayaran' => 'nullable|string',
            'foto_desain' => 'nullable|image|max:10240',
            'bukti_pembayaran' => 'nullable|image|max:10240',
        ]);

        $pelanggan = \App\Models\Pelanggan::find($request->id_pelanggan);

        // Handle Files
        $fotoDesainPath = $request->hasFile('foto_desain')
            ? $request->file('foto_desain')->store('foto-desain', 'public')
            : null;

        $buktiPembayaranPath = $request->hasFile('bukti_pembayaran')
            ? $request->file('bukti_pembayaran')->store('bukti-pembayaran', 'public')
            : null;

        // Calculate Totals per Item and Grand Total
        $itemsData = [];
        $totalHargaNota = 0;

        foreach ($request->items as $item) {
            $layanan = \App\Models\Layanan::find($item['id_layanan']);
            $subtotal = $layanan->harga_per_satuan * $item['jumlah'];
            $totalHargaNota += $subtotal;

            $itemsData[] = [
                'id_layanan' => $item['id_layanan'],
                'jumlah' => $item['jumlah'],
                'total_harga' => $subtotal, // Each row stores its own subtotal
            ];
        }

        // DP Validation (shared logic)
        $uangMuka = $request->uang_muka ?? 0;
        $minDpPersen = Pemesanan::getMinDPPercentage($pelanggan->kategori_pelanggan);
        $minDpNominal = $totalHargaNota * $minDpPersen;

        if ($uangMuka < $minDpNominal && $uangMuka < $totalHargaNota) {
            return back()->withErrors(['uang_muka' => "Minimal DP untuk kategori {$pelanggan->kategori_pelanggan} adalah Rp " . number_format($minDpNominal, 0, ',', '.')])->withInput();
        }

        // Payment status
        $statusPembayaran = Pemesanan::STATUS_PEMBAYARAN_BELUM_LUNAS;
        if ($uangMuka >= $totalHargaNota) {
            $statusPembayaran = Pemesanan::STATUS_PEMBAYARAN_LUNAS;
        } elseif ($uangMuka > 0) {
            $statusPembayaran = Pemesanan::STATUS_PEMBAYARAN_DP;
        }

        $noNota = 'INV-' . strtoupper(\Illuminate\Support\Str::random(8));

        foreach ($itemsData as $data) {
            Pemesanan::create([
                'no_nota' => $noNota,
                'id_pelanggan' => $request->id_pelanggan,
                'id_layanan' => $data['id_layanan'],
                'tanggal_pesan' => now(),
                'jumlah' => $data['jumlah'],
                'total_harga' => $data['total_harga'],
                'status_pesanan' => Pemesanan::STATUS_PESANAN_PENDING,
                'bukti_pembayaran' => $buktiPembayaranPath,
                'foto_desain' => $fotoDesainPath,
                'metode_pembayaran' => $request->metode_pembayaran,
                'keterangan' => $request->keterangan,
                'uang_muka' => $uangMuka,
                'status_pembayaran' => $statusPembayaran,
            ]);
        }

        return redirect()->route('pemesanan.index')->with('success', "Pesanan #{$noNota} berhasil dibuat!");
    }

    public function edit($id)
    {
        $p = Pemesanan::findOrFail($id);
        $orderItems = Pemesanan::where('no_nota', $p->no_nota)->get();
        $pelanggans = \App\Models\Pelanggan::all();
        $layanans = \App\Models\Layanan::all();

        return view('pemesanan.edit', compact('p', 'orderItems', 'pelanggans', 'layanans'));
    }

    public function update(Request $request, $id)
    {
        $mainPemesanan = Pemesanan::findOrFail($id);
        $noNota = $mainPemesanan->no_nota;

        $request->validate([
            'metode_pembayaran' => 'nullable|string',
            'keterangan' => 'nullable|string',
            'foto_desain' => 'nullable|image|max:10240',
            'bukti_pembayaran' => 'nullable|image|max:10240',
            'status_pesanan' => 'required|string',
        ]);

        $updateData = [
            'metode_pembayaran' => $request->metode_pembayaran,
            'keterangan' => $request->keterangan,
            'status_pesanan' => $request->status_pesanan,
        ];

        if ($request->hasFile('foto_desain')) {
            if ($mainPemesanan->foto_desain) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($mainPemesanan->foto_desain);
            }
            $updateData['foto_desain'] = $request->file('foto_desain')->store('foto-desain', 'public');
        }

        if ($request->hasFile('bukti_pembayaran')) {
            if ($mainPemesanan->bukti_pembayaran) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($mainPemesanan->bukti_pembayaran);
            }
            $updateData['bukti_pembayaran'] = $request->file('bukti_pembayaran')->store('bukti-pembayaran', 'public');
        }

        // Apply to all items in same nota
        $items = Pemesanan::where('no_nota', $noNota)->get();
        foreach ($items as $item) {
            $item->update($updateData);
        }


        return redirect()->route('pemesanan.show', $id)->with('success', "Pesanan #{$noNota} berhasil diperbarui!");
    }

    public function show($id)
    {
        // Find the specific record first to get the no_nota
        $mainRecord = Pemesanan::findOrFail($id);

        // Get all items with the same no_nota
        $p = $mainRecord; // For backward compatibility in view if still using single $p
        $orderItems = Pemesanan::with('layanan')->where('no_nota', $mainRecord->no_nota)->get();

        return view('pemesanan.show', compact('p', 'orderItems'));
    }

    public function toggleStatus($id)
    {
        $pemesanan = Pemesanan::findOrFail($id);
        $noNota = $pemesanan->no_nota;

        $currentStatus = $pemesanan->status_pesanan;
        $nextStatus = match ($currentStatus) {
            Pemesanan::STATUS_PESANAN_PENDING => Pemesanan::STATUS_PESANAN_PROSES,
            Pemesanan::STATUS_PESANAN_PROSES  => Pemesanan::STATUS_PESANAN_DIKIRIM,
            Pemesanan::STATUS_PESANAN_DIKIRIM => Pemesanan::STATUS_PESANAN_SAMPAI,
            Pemesanan::STATUS_PESANAN_SAMPAI  => Pemesanan::STATUS_PESANAN_SELESAI,
            default => Pemesanan::STATUS_PESANAN_PENDING,
        };

        // Update all items with same no_nota
        $items = Pemesanan::where('no_nota', $noNota)->get();
        foreach ($items as $item) {
            $item->update(['status_pesanan' => $nextStatus]);
        }


        return back()->with('success', 'Status pesanan #' . $noNota . ' berhasil diperbarui ke ' . $nextStatus);
    }

    public function markAsPaid($id)
    {
        $pemesanan = Pemesanan::findOrFail($id);
        $noNota = $pemesanan->no_nota;

        // Calculate total for validation - use SUM of all items in this nota
        $totalHarga = Pemesanan::where('no_nota', $noNota)->sum('total_harga');

        // Update all items with same no_nota
        Pemesanan::where('no_nota', $noNota)->update([
            'status_pembayaran' => Pemesanan::STATUS_PEMBAYARAN_LUNAS,
            'uang_muka' => $totalHarga
        ]);

        return back()->with('success', 'Pembayaran pesanan #' . $noNota . ' telah ditandai LUNAS.');
    }

    public function destroy($id)
    {
        $pemesanan = Pemesanan::findOrFail($id);
        $noNota = $pemesanan->no_nota;

        // Delete all items with same no_nota
        Pemesanan::where('no_nota', $noNota)->delete();

        return back()->with('success', 'Pesanan #' . $noNota . ' berhasil dihapus.');
    }

    public function faktur($id)
    {
        $p = Pemesanan::with(['pelanggan', 'layanan'])->findOrFail($id);
        $orderItems = Pemesanan::with('layanan')->where('no_nota', $p->no_nota)->get();

        $pdf = Pdf::loadView('pemesanan.faktur', [
            'p' => $p,
            'items' => $orderItems
        ]);

        return $pdf->stream("faktur-{$p->no_nota}.pdf");
    }
}
