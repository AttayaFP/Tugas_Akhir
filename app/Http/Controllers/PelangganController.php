<?php

namespace App\Http\Controllers;

use App\Models\Pelanggan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class PelangganController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');

        $pelanggan = Pelanggan::when($search, function ($q) use ($search) {
            $q->where(function ($inner) use ($search) {
                $inner->where('nama_lengkap', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('no_telepon', 'like', "%{$search}%")
                    ->orWhere('kode_member', 'like', "%{$search}%");
            });
        })
            ->latest()
            ->get();

        if ($request->ajax()) {
            return view('pelanggan.partials._table', compact('pelanggan'))->render();
        }

        return view('pelanggan.index', compact('pelanggan'));
    }

    public function create()
    {
        return view('pelanggan.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_lengkap' => 'required|string|max:255',
            'no_telepon' => 'required|string|max:15',
            'alamat' => 'nullable|string',
            'email' => 'required|email|max:255|unique:pelanggans,email',
            'password' => 'required|string|min:6',
            'foto' => 'nullable|image|max:2048',
            'kode_member' => 'nullable|string|max:50',
        ]);

        $kategori = 'Regular';
        if ($request->kode_member) {
            if (str_starts_with($request->kode_member, 'M')) {
                $kategori = 'Member';
            } elseif (str_starts_with($request->kode_member, 'V')) {
                $kategori = 'VIP';
            }
        }

        if ($request->hasFile('foto')) {
            $validated['foto'] = $request->file('foto')->store('foto-pelanggan', 'public');
        }

        Pelanggan::create(array_merge($validated, [
            'kategori_pelanggan' => $kategori,
            'password' => Hash::make($request->password),
        ]));

        return redirect()->route('pelanggan.index')->with('success', 'Pelanggan berhasil ditambahkan!');
    }

    public function edit($id)
    {
        $p = Pelanggan::findOrFail($id);
        return view('pelanggan.edit', compact('p'));
    }

    public function update(Request $request, $id)
    {
        $pelanggan = Pelanggan::findOrFail($id);

        $validated = $request->validate([
            'nama_lengkap' => 'required|string|max:255',
            'no_telepon' => 'required|string|max:15',
            'alamat' => 'nullable|string',
            'email' => 'required|email|max:255|unique:pelanggans,email,' . $id,
            'password' => 'nullable|string|min:6',
            'kode_member' => 'nullable|string|max:50',
            'foto' => 'nullable|image|max:2048',
        ]);

        $kategori = 'Regular';
        if ($request->kode_member) {
            if (str_starts_with($request->kode_member, 'M')) {
                $kategori = 'Member';
            } elseif (str_starts_with($request->kode_member, 'V')) {
                $kategori = 'VIP';
            }
        }

        if ($request->hasFile('foto')) {
            if ($pelanggan->foto) {
                Storage::disk('public')->delete($pelanggan->foto);
            }
            $validated['foto'] = $request->file('foto')->store('foto-pelanggan', 'public');
        }

        $updateData = array_merge($validated, ['kategori_pelanggan' => $kategori]);

        if ($request->filled('password')) {
            $updateData['password'] = Hash::make($request->password);
        } else {
            unset($updateData['password']);
        }

        $pelanggan->update($updateData);

        return redirect()->route('pelanggan.index')->with('success', 'Data pelanggan berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $pelanggan = Pelanggan::findOrFail($id);

        if ($pelanggan->foto) {
            Storage::disk('public')->delete($pelanggan->foto);
        }

        $pelanggan->delete();

        return redirect()->route('pelanggan.index')->with('success', 'Pelanggan berhasil dihapus!');
    }
}
