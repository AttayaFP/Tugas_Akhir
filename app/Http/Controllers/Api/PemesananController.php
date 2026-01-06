<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Pemesanan;
use App\Models\Layanan;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;


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

        // 1. Handle Upload Files
        $buktiPembayaranPath = $request->hasFile('bukti_pembayaran')
            ? $request->file('bukti_pembayaran')->store('bukti-pembayaran', 'public')
            : null;

        $fotoDesainPath = $request->hasFile('foto_desain')
            ? $request->file('foto_desain')->store('foto-desain', 'public')
            : null;

        $pelanggan = \App\Models\Pelanggan::find($request->id_pelanggan);

        // 2. Hitung Total Seluruh Nota untuk Validasi DP
        $totalHargaNota = 0;
        foreach ($request->items as $item) {
            $layanan = Layanan::find($item['id_layanan']);
            $totalHargaNota += ($layanan->harga_per_satuan * $item['jumlah']);
        }

        // 3. Validasi DP berdasarkan Total Nota
        $uangMuka = $request->uang_muka ?? 0;
        $minDpPersen = Pemesanan::getMinDPPercentage($pelanggan->kategori_pelanggan);
        $minDpNominal = $totalHargaNota * $minDpPersen;

        if ($uangMuka < $minDpNominal && $uangMuka < $totalHargaNota) {
            return response()->json([
                'message' => "Minimal DP untuk kategori {$pelanggan->kategori_pelanggan} adalah Rp " . number_format($minDpNominal, 0, ',', '.')
            ], 422);
        }

        // 4. Tentukan Status Pembayaran (Global per Nota)
        $statusPembayaran = Pemesanan::STATUS_PEMBAYARAN_BELUM_LUNAS;
        if ($uangMuka >= $totalHargaNota) {
            $statusPembayaran = Pemesanan::STATUS_PEMBAYARAN_LUNAS;
        } elseif ($uangMuka > 0) {
            $statusPembayaran = Pemesanan::STATUS_PEMBAYARAN_DP;
        }

        $noNota = 'INV-' . strtoupper(Str::random(8));
        $createdRecords = [];

        // 5. Simpan Data per MASING-MASING ITEM dengan benar
        foreach ($request->items as $item) {
            $layananItem = Layanan::find($item['id_layanan']);
            $itemTotal = $layananItem->harga_per_satuan * $item['jumlah'];

            $createdRecords[] = Pemesanan::create([
                'no_nota' => $noNota,
                'id_pelanggan' => $request->id_pelanggan,
                'id_layanan' => $item['id_layanan'],
                'tanggal_pesan' => now(),
                'jumlah' => $item['jumlah'], // DISIMPAN JUMLAH ITEM INI SAJA
                'total_harga' => $itemTotal,   // DISIMPAN TOTAL HARGA ITEM INI SAJA
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
            'jumlah_bayar' => 'sometimes|numeric|min:0', // Tambahan pembayaran baru
        ]);
        // Handle upload bukti pembayaran baru
        if ($request->hasFile('bukti_pembayaran')) {
            // Hapus bukti lama jika ada
            if ($pemesanan->bukti_pembayaran && Storage::disk('public')->exists($pemesanan->bukti_pembayaran)) {
                Storage::disk('public')->delete($pemesanan->bukti_pembayaran);
            }

            $validated['bukti_pembayaran'] = $request->file('bukti_pembayaran')
                ->store('bukti-pembayaran', 'public');
        }
        // Update metode pembayaran jika ada pembayaran baru
        if (isset($validated['metode_pembayaran'])) {
            $pemesanan->metode_pembayaran = $validated['metode_pembayaran'];
        }
        // Logic pembayaran angsuran
        if (isset($validated['jumlah_bayar'])) {
            $jumlahBayar = $validated['jumlah_bayar'];
            $uangMukaSebelumnya = $pemesanan->uang_muka ?? 0;
            $uangMukaBaru = $uangMukaSebelumnya + $jumlahBayar;

            // Validasi: tidak boleh melebihi total harga
            if ($uangMukaBaru > $pemesanan->total_harga) {
                return response()->json([
                    'message' => 'Jumlah pembayaran melebihi sisa yang harus dibayar',
                    'sisa_pembayaran' => $pemesanan->total_harga - $uangMukaSebelumnya
                ], 422);
            }

            $validated['uang_muka'] = $uangMukaBaru;

            // Auto-update status pembayaran
            if ($uangMukaBaru >= $pemesanan->total_harga) {
                $validated['status_pembayaran'] = Pemesanan::STATUS_PEMBAYARAN_LUNAS;
            } else {
                $validated['status_pembayaran'] = Pemesanan::STATUS_PEMBAYARAN_DP;
            }
        }
        // Logic update uang_muka langsung (untuk admin)
        elseif (isset($validated['uang_muka'])) {
            $totalHarga = $pemesanan->total_harga;
            $uangMuka = $validated['uang_muka'];
            $minDpPersen = Pemesanan::getMinDPPercentage($pemesanan->pelanggan->kategori_pelanggan);
            $minDpNominal = $totalHarga * $minDpPersen;
            if ($uangMuka < $minDpNominal && $uangMuka < $totalHarga) {
                return response()->json([
                    'message' => "Minimal uang muka untuk kategori {$pemesanan->pelanggan->kategori_pelanggan} adalah " . number_format($minDpNominal, 0, ',', '.') . " (" . ($minDpPersen * 100) . "%)"
                ], 422);
            }
            if ($uangMuka >= $totalHarga) {
                $validated['status_pembayaran'] = Pemesanan::STATUS_PEMBAYARAN_LUNAS;
            } elseif ($uangMuka > 0) {
                $validated['status_pembayaran'] = Pemesanan::STATUS_PEMBAYARAN_DP;
            } else {
                $validated['status_pembayaran'] = Pemesanan::STATUS_PEMBAYARAN_BELUM_LUNAS;
            }
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

        $pemesanan->delete();

        return response()->json(['message' => 'Pemesanan deleted successfully']);
    }
}
