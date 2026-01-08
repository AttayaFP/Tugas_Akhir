<?php

namespace App\Http\Controllers;

use App\Models\Layanan;
use App\Models\Pelanggan;
use App\Models\Pemesanan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LaporanController extends Controller
{
    public function index()
    {
        return view('laporan.index');
    }

    public function cetakPemesanan(Request $request)
    {
        $request->validate([
            'start_date' => 'nullable|date',
            'end_date'   => 'nullable|date|after_or_equal:start_date',
        ]);

        $query = Pemesanan::with(['pelanggan', 'layanan'])
            ->orderBy('tanggal_pesan', 'desc');

        if ($request->start_date && $request->end_date) {
            $query->whereBetween('tanggal_pesan', [$request->start_date, $request->end_date]);
        }

        $data = $query->get();

        return view('laporan.cetak_pemesanan', [
            'data' => $data,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date
        ]);
    }

    public function cetakPelanggan()
    {
        $data = Pelanggan::orderBy('nama_lengkap', 'asc')->get();

        return view('laporan.cetak_pelanggan', [
            'data' => $data
        ]);
    }

    public function cetakLayanan()
    {
        $data = Layanan::orderBy('nama_layanan', 'asc')->get();

        return view('laporan.cetak_layanan', [
            'data' => $data
        ]);
    }

    public function cetakPendapatan(Request $request)
    {
        $request->validate([
            'start_date' => 'nullable|date',
            'end_date'   => 'nullable|date|after_or_equal:start_date',
        ]);

        $query = Pemesanan::select(
            DB::raw('DATE(tanggal_pesan) as date'),
            DB::raw('SUM(total_harga) as total_income'),
            DB::raw('COUNT(id) as total_orders')
        )
            ->groupBy('date')
            ->orderBy('date', 'desc');

        if ($request->start_date && $request->end_date) {
            $query->whereBetween('tanggal_pesan', [$request->start_date, $request->end_date]);
        }

        $data = $query->get();
        $grandTotal = $data->sum('total_income');

        return view('laporan.cetak_pendapatan', [
            'data' => $data,
            'grand_total' => $grandTotal,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date
        ]);
    }
}
