<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Pemesanan;
use App\Models\Layanan;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

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
            'id_layanan' => 'required|exists:layanans,id',
            'jumlah' => 'required|integer|min:1',
            'status_pesanan' => 'nullable|string',
            'bukti_pembayaran' => 'nullable|file|image|max:10240',
            'keterangan' => 'nullable|string',
            'uang_muka' => 'nullable|numeric|min:0',
            'status_pembayaran' => 'nullable|string|in:Belum Lunas,DP,Lunas',
        ]);

        $buktiPembayaranPath = null;
        if ($request->hasFile('bukti_pembayaran')) {
            $buktiPembayaranPath = $request->file('bukti_pembayaran')->store('bukti-pembayaran', 'public');
        }

        $pelanggan = \App\Models\Pelanggan::find($request->id_pelanggan);
        $layanan = Layanan::find($request->id_layanan);
        $totalHarga = $layanan->harga_per_satuan * $request->jumlah;
        $uangMuka = $request->uang_muka ?? 0;

        $minDpPersen = Pemesanan::getMinDPPercentage($pelanggan->kategori_pelanggan);
        $minDpNominal = $totalHarga * $minDpPersen;

        if ($uangMuka < $minDpNominal && $uangMuka < $totalHarga) {
            return response()->json([
                'message' => "Minimal uang muka untuk kategori {$pelanggan->kategori_pelanggan} adalah " . number_format($minDpNominal, 0, ',', '.') . " (" . ($minDpPersen * 100) . "%)"
            ], 422);
        }

        $statusPembayaran = Pemesanan::STATUS_PEMBAYARAN_BELUM_LUNAS;
        if ($uangMuka >= $totalHarga) {
            $statusPembayaran = Pemesanan::STATUS_PEMBAYARAN_LUNAS;
        } elseif ($uangMuka > 0) {
            $statusPembayaran = Pemesanan::STATUS_PEMBAYARAN_DP;
        }

        $pemesanan = Pemesanan::create([
            'no_nota' => 'INV-' . strtoupper(Str::random(8)),
            'id_pelanggan' => $request->id_pelanggan,
            'id_layanan' => $request->id_layanan,
            'tanggal_pesan' => now(),
            'jumlah' => $request->jumlah,
            'total_harga' => $totalHarga,
            'status_pesanan' => $request->status_pesanan ?? Pemesanan::STATUS_PESANAN_PENDING,
            'bukti_pembayaran' => $buktiPembayaranPath,
            'keterangan' => $request->keterangan,
            'uang_muka' => $uangMuka,
            'status_pembayaran' => $statusPembayaran,
        ]);

        return response()->json($pemesanan->load(['pelanggan', 'layanan']), 201);
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
        ]);

        if (isset($validated['uang_muka'])) {
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

        return response()->json($pemesanan);
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
