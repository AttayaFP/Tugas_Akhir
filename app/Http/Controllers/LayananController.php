<?php

namespace App\Http\Controllers;

use App\Models\Layanan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class LayananController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');

        $layanan = Layanan::when($search, function ($q) use ($search) {
            $q->where(function ($inner) use ($search) {
                $inner->where('nama_layanan', 'like', "%{$search}%")
                    ->orWhere('deskripsi', 'like', "%{$search}%");
            });
        })
            ->latest()
            ->get();

        if ($request->ajax()) {
            return view('layanan.partials._grid', compact('layanan'))->render();
        }

        return view('layanan.index', compact('layanan'));
    }

    public function create()
    {
        return view('layanan.create');
    }

    public function store(Request $request)
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

        if ($request->hasFile('foto')) {
            $validated['foto'] = $request->file('foto')->store('foto-layanan', 'public');
        }

        Layanan::create($validated);

        return redirect()->route('layanan.index')->with('success', 'Layanan berhasil ditambahkan!');
    }

    public function edit($id)
    {
        $l = Layanan::findOrFail($id);
        return view('layanan.edit', compact('l'));
    }

    public function update(Request $request, $id)
    {
        $layanan = Layanan::findOrFail($id);

        $validated = $request->validate([
            'nama_layanan' => 'required|string|max:255',
            'satuan' => 'required|string|max:50',
            'harga_per_satuan' => 'required|numeric|min:0',
            'deskripsi' => 'nullable|string',
            'minimal_order' => 'integer|min:1',
            'estimasi_waktu' => 'nullable|string',
            'foto' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('foto')) {
            if ($layanan->foto) {
                Storage::disk('public')->delete($layanan->foto);
            }
            $validated['foto'] = $request->file('foto')->store('foto-layanan', 'public');
        }

        $layanan->update($validated);

        return redirect()->route('layanan.index')->with('success', 'Layanan berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $layanan = Layanan::findOrFail($id);

        if ($layanan->foto) {
            Storage::disk('public')->delete($layanan->foto);
        }

        $layanan->delete();

        return redirect()->route('layanan.index')->with('success', 'Layanan berhasil dihapus!');
    }
}
