<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Layanan;
use Illuminate\Http\Request;

class LayananController extends Controller
{
    public function tampil()
    {
        return response()->json(Layanan::all());
    }

    public function tambah(Request $request)
    {
        $validated = $request->validate([
            'nama_layanan' => 'required|string|max:255',
            'satuan' => 'required|string|max:50',
            'harga_per_satuan' => 'required|numeric|min:0',
            'deskripsi' => 'nullable|string',
            'minimal_order' => 'integer|min:1',
            'estimasi_waktu' => 'nullable|string',
            'foto' => 'nullable|image|max:2048',
        ]);

        $pathFoto = null;
        if ($request->hasFile('foto')) {
            $pathFoto = $request->file('foto')->store('foto-layanan', 'public');
        }

        $validated['foto'] = $pathFoto;

        $layanan = Layanan::create($validated);

        return response()->json($layanan, 201);
    }

    public function detail($id)
    {
        $layanan = Layanan::find($id);

        if (!$layanan) {
            return response()->json(['message' => 'Layanan Tidak Ditemukan'], 404);
        }

        return response()->json($layanan);
    }

    public function ubah(Request $request, $id)
    {
        $layanan = Layanan::find($id);

        if (!$layanan) {
            return response()->json(['message' => 'Layanan Tidak Ditemukan'], 404);
        }

        $validated = $request->validate([
            'nama_layanan' => 'sometimes|required|string|max:255',
            'satuan' => 'sometimes|required|string|max:50',
            'harga_per_satuan' => 'sometimes|required|numeric|min:0',
            'deskripsi' => 'nullable|string',
            'minimal_order' => 'integer|min:1',
            'estimasi_waktu' => 'nullable|string',
            'foto' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('foto')) {
            if ($layanan->foto && \Illuminate\Support\Facades\Storage::disk('public')->exists($layanan->foto)) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($layanan->foto);
            }
            $validated['foto'] = $request->file('foto')->store('foto-layanan', 'public');
        }

        $layanan->update($validated);

        return response()->json($layanan);
    }

    public function hapus($id)
    {
        $layanan = Layanan::find($id);

        if (!$layanan) {
            return response()->json(['message' => 'Layanan Tidak Ditemukan'], 404);
        }

        $layanan->delete();

        return response()->json(['message' => 'Layanan berhasil dihapus']);
    }
}
