<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Pemesanan;
use App\Models\Layanan;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;


class PemesananController extends Controller
{
    public function tampil()
    {
        $pemesanan = Pemesanan::with(['pelanggan', 'layanan'])->get();
        return response()->json($pemesanan);
    }


    public function tambah(Request $request)
    {
        $validated = $request->validate([
            'id_pelanggan' => 'required|exists:pelanggans,id',
            'items' => 'required|array|min:1',
            'items.*.id_layanan' => 'required|exists:layanans,id',
            'items.*.jumlah' => 'required|integer|min:1',
            'status_pesanan' => 'nullable|string',
            'bukti_pembayaran' => 'nullable|file|image|max:10240',
            'foto_desain' => 'nullable|file|image|max:10240',
            'metode_pembayaran' => 'nullable|string',
            'keterangan' => 'nullable|string',
            'uang_muka' => 'nullable|numeric|min:0',
            'status_pembayaran' => 'nullable|string|in:Belum Lunas,DP,Lunas',
        ]);

        $buktiPembayaranPath = $request->hasFile('bukti_pembayaran')
            ? $request->file('bukti_pembayaran')->store('bukti-pembayaran', 'public')
            : null;

        $fotoDesainPath = $request->hasFile('foto_desain')
            ? $request->file('foto_desain')->store('foto-desain', 'public')
            : null;

        $pelanggan = \App\Models\Pelanggan::find($request->id_pelanggan);
        $totalHargaNota = 0;
        foreach ($request->items as $item) {
            $layanan = Layanan::find($item['id_layanan']);
            $totalHargaNota += ($layanan->harga_per_satuan * $item['jumlah']);
        }

        $uangMuka = $request->uang_muka ?? 0;
        $minDpPersen = Pemesanan::getMinDPPercentage($pelanggan->kategori_pelanggan);
        $minDpNominal = $totalHargaNota * $minDpPersen;

        if ($uangMuka < $minDpNominal && $uangMuka < $totalHargaNota) {
            return response()->json([
                'message' => "Minimal DP untuk kategori {$pelanggan->kategori_pelanggan} adalah Rp " . number_format($minDpNominal, 0, ',', '.')
            ], 422);
        }

        $statusPembayaran = Pemesanan::STATUS_PEMBAYARAN_BELUM_LUNAS;
        if ($uangMuka >= $totalHargaNota) {
            $statusPembayaran = Pemesanan::STATUS_PEMBAYARAN_LUNAS;
        } elseif ($uangMuka > 0) {
            $statusPembayaran = Pemesanan::STATUS_PEMBAYARAN_DP;
        }

        $noNota = 'INV-' . strtoupper(Str::random(8));
        $createdRecords = [];
        foreach ($request->items as $item) {
            $layananItem = Layanan::find($item['id_layanan']);
            $itemTotal = $layananItem->harga_per_satuan * $item['jumlah'];

            $createdRecords[] = Pemesanan::create([
                'no_nota' => $noNota,
                'id_pelanggan' => $request->id_pelanggan,
                'id_layanan' => $item['id_layanan'],
                'tanggal_pesan' => now(),
                'jumlah' => $item['jumlah'],
                'total_harga' => $itemTotal,
                'status_pesanan' => $request->status_pesanan ?? Pemesanan::STATUS_PESANAN_PENDING,
                'bukti_pembayaran' => $buktiPembayaranPath,
                'foto_desain' => $fotoDesainPath,
                'metode_pembayaran' => $request->metode_pembayaran,
                'keterangan' => $request->keterangan,
                'uang_muka' => $uangMuka,
                'status_pembayaran' => $statusPembayaran,
            ]);
        }

        return response()->json([
            'message' => 'Pemesanan massal berhasil diproses',
            'no_nota' => $noNota,
            'total_nota' => $totalHargaNota,
            'data' => $createdRecords
        ], 201);
    }


    public function detail($id)
    {
        $pemesanan = Pemesanan::with(['pelanggan', 'layanan'])->find($id);

        if (!$pemesanan) {
            return response()->json(['message' => 'Pemesanan not found'], 404);
        }

        return response()->json($pemesanan);
    }


    public function ubah(Request $request, $id)
    {
        $pemesanan = Pemesanan::find($id);
        if (!$pemesanan) {
            return response()->json(['message' => 'Pemesanan not found'], 404);
        }

        $validated = $request->validate([
            'status_pesanan' => 'sometimes|required|string',
            'uang_muka' => 'sometimes|numeric|min:0',
            'keterangan' => 'nullable|string',
            'metode_pembayaran' => 'sometimes|string',
            'bukti_pembayaran' => 'nullable|file|image|max:10240',
            'jumlah_bayar' => 'sometimes|numeric|min:0',
        ]);

        if ($request->hasFile('bukti_pembayaran')) {
            if ($pemesanan->bukti_pembayaran && Storage::disk('public')->exists($pemesanan->bukti_pembayaran)) {
                Storage::disk('public')->delete($pemesanan->bukti_pembayaran);
            }
            $validated['bukti_pembayaran'] = $request->file('bukti_pembayaran')->store('bukti-pembayaran', 'public');
        }

        // Logic Pembayaran Berulang / Cicilan
        if (isset($validated['jumlah_bayar'])) {
            $jumlahBayar = $validated['jumlah_bayar'];

            // Hitung Grand Total seluruh item dalam nota ini
            $grandTotal = Pemesanan::where('no_nota', $pemesanan->no_nota)->sum('total_harga');
            $uangMukaBaru = ($pemesanan->uang_muka ?? 0) + $jumlahBayar;

            if ($uangMukaBaru > $grandTotal + 0.01) { // Adding small epsilon for float comparison if needed
                return response()->json([
                    'message' => 'Jumlah pembayaran melebihi sisa yang harus dibayar',
                    'sisa_pembayaran' => $grandTotal - $pemesanan->uang_muka
                ], 422);
            }

            $validated['uang_muka'] = $uangMukaBaru;
            $validated['status_pembayaran'] = ($uangMukaBaru >= $grandTotal - 0.01)
                ? Pemesanan::STATUS_PEMBAYARAN_LUNAS
                : Pemesanan::STATUS_PEMBAYARAN_DP;

            // Update seluruh item dalam satu nota untuk data finansial
            Pemesanan::where('no_nota', $pemesanan->no_nota)->update([
                'uang_muka' => $validated['uang_muka'],
                'status_pembayaran' => $validated['status_pembayaran']
            ]);
        }

        // Update seluruh item dalam satu nota jika status berubah
        if (isset($validated['status_pesanan'])) {
            $user = $request->user();
            $newStatus = $validated['status_pesanan'];

            // Check if user is Admin (User model) or Pelanggan
            $isAdmin = $user instanceof \App\Models\User;
            $isPelanggan = $user instanceof \App\Models\Pelanggan;

            if ($isAdmin && !in_array($newStatus, Pemesanan::ADMIN_ALLOWED_STATUS)) {
                return response()->json([
                    'message' => 'Admin hanya bisa mengubah status ke: ' . implode(', ', Pemesanan::ADMIN_ALLOWED_STATUS)
                ], 403);
            }

            if ($isPelanggan && !in_array($newStatus, Pemesanan::PELANGGAN_ALLOWED_STATUS)) {
                return response()->json([
                    'message' => 'Pelanggan hanya bisa mengubah status pesanan ke: Selesai'
                ], 403);
            }

            Pemesanan::where('no_nota', $pemesanan->no_nota)->update([
                'status_pesanan' => $validated['status_pesanan']
            ]);
        }

        $pemesanan->update($validated);

        return response()->json([
            'message' => 'Pemesanan berhasil diupdate',
            'data' => $pemesanan->fresh(),
            'sisa_pembayaran' => $pemesanan->total_harga - $pemesanan->uang_muka
        ]);
    }

    public function hapus($id)
    {
        $pemesanan = Pemesanan::find($id);
        if (!$pemesanan) {
            return response()->json(['message' => 'Pemesanan not found'], 404);
        }

        // Hapus semua item dalam nota yang sama
        Pemesanan::where('no_nota', $pemesanan->no_nota)->delete();
        return response()->json(['message' => 'Seluruh item dalam nota berhasil dihapus']);
    }

    /**
     * Generate Faktur PDF untuk Mobile
     */
    public function faktur($id)
    {
        $p = Pemesanan::with(['pelanggan', 'layanan'])->findOrFail($id);
        $items = Pemesanan::with('layanan')->where('no_nota', $p->no_nota)->get();

        $pdf = Pdf::loadView('pemesanan.faktur', compact('p', 'items'));

        return $pdf->stream("faktur-{$p->no_nota}.pdf");
    }
}
