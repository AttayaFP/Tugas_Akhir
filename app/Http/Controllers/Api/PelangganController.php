<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Pelanggan;
use Illuminate\Http\Request;

class PelangganController extends Controller
{
    public function tampil()
    {
        return response()->json(Pelanggan::all());
    }

    public function tambah(Request $request)
    {
        $validated = $request->validate([
            'nama_lengkap' => 'required|string|max:255',
            'no_telepon' => 'required|string|max:15',
            'alamat' => 'nullable|string',
            'email' => 'nullable|email|max:255|unique:pelanggans,email',
            'password' => 'required|string|min:6',
            'foto' => 'nullable|image|max:2048',
            'kode_member' => 'nullable|string|max:50',
            'kategori_pelanggan' => 'string|in:Regular,Member,VIP',
        ]);

        $kodeMember = $request->kode_member;
        $kategoriSeharusnya = 'Regular';

        if ($kodeMember && str_starts_with($kodeMember, 'M')) {
            $kategoriSeharusnya = 'Member';
        } elseif ($kodeMember && str_starts_with($kodeMember, 'V')) {
            $kategoriSeharusnya = 'VIP';
        }

        if ($request->has('kategori_pelanggan') && $request->kategori_pelanggan !== $kategoriSeharusnya) {
            return response()->json([
                'message' => 'Kategori pelanggan tidak sesuai dengan kode member. Seharusnya: ' . $kategoriSeharusnya,
            ], 422);
        }

        $pathFoto = null;
        if ($request->hasFile('foto')) {
            $pathFoto = $request->file('foto')->store('foto-pelanggan', 'public');
        }

        $pelanggan = Pelanggan::create(array_merge($validated, [
            'kategori_pelanggan' => $kategoriSeharusnya,
            'password' => \Illuminate\Support\Facades\Hash::make($request->password),
            'foto' => $pathFoto
        ]));

        $token = $pelanggan->createToken('pelanggan_token')->plainTextToken;

        return response()->json([
            'message' => 'Registrasi berhasil',
            'data' => $pelanggan,
            'access_token' => $token,
            'token_type' => 'Bearer'
        ], 201);
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $pelanggan = Pelanggan::where('email', $request->email)->first();

        if (! $pelanggan || ! \Illuminate\Support\Facades\Hash::check($request->password, $pelanggan->password)) {
            return response()->json([
                'message' => 'Email atau password salah'
            ], 401);
        }

        $token = $pelanggan->createToken('pelanggan_token')->plainTextToken;

        return response()->json([
            'message' => 'Login berhasil',
            'data' => $pelanggan,
            'access_token' => $token,
            'token_type' => 'Bearer'
        ]);
    }


    public function detail($id)
    {
        $pelanggan = Pelanggan::find($id);

        if (!$pelanggan) {
            return response()->json(['message' => 'Pelanggan not found'], 404);
        }

        return response()->json($pelanggan);
    }

    public function ubah(Request $request, $id)
    {
        $pelanggan = Pelanggan::find($id);

        if (!$pelanggan) {
            return response()->json(['message' => 'Pelanggan not found'], 404);
        }

        $validated = $request->validate([
            'nama_lengkap' => 'sometimes|required|string|max:255',
            'no_telepon' => 'sometimes|required|string|max:15',
            'alamat' => 'nullable|string',
            'email' => 'sometimes|required|email|max:255|unique:pelanggans,email,' . $id,
            'password' => 'nullable|string|min:6',
            'kode_member' => 'nullable|string|max:50',
            'foto' => 'nullable|image|max:2048',
            'kategori_pelanggan' => 'sometimes|string|in:Regular,Member,VIP',
        ]);

        if ($request->has('kode_member')) {
            $kodeMember = $request->kode_member;
            $kategoriSeharusnya = 'Regular';

            if ($kodeMember && str_starts_with($kodeMember, 'M')) {
                $kategoriSeharusnya = 'Member';
            } elseif ($kodeMember && str_starts_with($kodeMember, 'V')) {
                $kategoriSeharusnya = 'VIP';
            }

            if ($request->has('kategori_pelanggan') && $request->kategori_pelanggan !== $kategoriSeharusnya) {
                return response()->json([
                    'message' => 'Kategori pelanggan tidak sesuai dengan kode member. Seharusnya: ' . $kategoriSeharusnya,
                ], 422);
            }

            $validated['kategori_pelanggan'] = $kategoriSeharusnya;
        }

        if ($request->filled('password')) {
            $validated['password'] = \Illuminate\Support\Facades\Hash::make($request->password);
        } else {
            unset($validated['password']);
        }

        if ($request->hasFile('foto')) {
            if ($pelanggan->foto && \Illuminate\Support\Facades\Storage::disk('public')->exists($pelanggan->foto)) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($pelanggan->foto);
            }
            $validated['foto'] = $request->file('foto')->store('foto-pelanggan', 'public');
        }

        $pelanggan->update($validated);

        return response()->json($pelanggan);
    }


    public function hapus($id)
    {
        $pelanggan = Pelanggan::find($id);

        if (!$pelanggan) {
            return response()->json(['message' => 'Pelanggan Tidak Ditemukan'], 404);
        }

        $pelanggan->delete();

        return response()->json(['message' => 'Pelanggan berhasil dihapus']);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logout berhasil'
        ]);
    }
}
