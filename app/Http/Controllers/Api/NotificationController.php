<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Pelanggan;

class NotificationController extends Controller
{
    public function updateToken(Request $request)
    {
        $request->validate([
            'fcm_token' => 'required|string',
            'id_pelanggan' => 'required|exists:pelanggans,id'
        ]);

        $pelanggan = Pelanggan::find($request->id_pelanggan);

        if ($pelanggan) {
            $pelanggan->update(['fcm_token' => $request->fcm_token]);
            return response()->json([
                'success' => true,
                'message' => 'FCM Token updated successfully'
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Pelanggan not found'
        ], 404);
    }
}
