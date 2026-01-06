<?php

namespace App\Http\Controllers;

use App\Models\Pemesanan;
use Illuminate\Http\Request;

class PemesananController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $tanggal = $request->input('tanggal');

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
            ->get();

        if ($request->ajax()) {
            return view('pemesanan.partials._table', compact('pemesanan'))->render();
        }

        return view('pemesanan.index', compact('pemesanan'));
    }

    public function show($id)
    {
        $p = Pemesanan::with(['pelanggan', 'layanan'])->findOrFail($id);
        return view('pemesanan.show', compact('p'));
    }

    public function toggleStatus($id)
    {
        $pemesanan = Pemesanan::findOrFail($id);

        $currentStatus = $pemesanan->status_pesanan;
        $nextStatus = match ($currentStatus) {
            Pemesanan::STATUS_PESANAN_PENDING => Pemesanan::STATUS_PESANAN_PROSES,
            Pemesanan::STATUS_PESANAN_PROSES  => Pemesanan::STATUS_PESANAN_SELESAI,
            Pemesanan::STATUS_PESANAN_SELESAI => Pemesanan::STATUS_PESANAN_DIAMBIL,
            default => Pemesanan::STATUS_PESANAN_PENDING,
        };

        $pemesanan->update(['status_pesanan' => $nextStatus]);

        return back()->with('success', 'Status pesanan #' . $pemesanan->no_nota . ' berhasil diperbarui ke ' . $nextStatus);
    }

    public function markAsPaid($id)
    {
        $pemesanan = Pemesanan::findOrFail($id);
        $pemesanan->update([
            'status_pembayaran' => Pemesanan::STATUS_PEMBAYARAN_LUNAS,
            'uang_muka' => $pemesanan->total_harga
        ]);

        return back()->with('success', 'Pembayaran pesanan #' . $pemesanan->no_nota . ' telah ditandai LUNAS.');
    }

    public function destroy($id)
    {
        $pemesanan = Pemesanan::findOrFail($id);
        $pemesanan->delete();

        return back()->with('success', 'Pesanan #' . $pemesanan->no_nota . ' berhasil dihapus.');
    }
}
