<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PendingFingerprint;
use App\Models\Student;
use Carbon\Carbon;

class FingerprintRegisterController extends Controller
{
    public function scan(Request $request)
    {
        $request->validate([
            'fingerprint_id' => 'required|string'
        ]);

        // ❌ Jika fingerprint sudah dipakai siswa
        if (Student::where('fingerprint_id', $request->fingerprint_id)->exists()) {
            return response()->json([
                'status' => 'used',
                'message' => 'Fingerprint sudah terdaftar'
            ], 409);
        }

        // ❌ Jika fingerprint sudah masuk pending
        if (PendingFingerprint::where('fingerprint_id', $request->fingerprint_id)->exists()) {
            return response()->json([
                'status' => 'pending',
                'message' => 'Fingerprint sudah terdeteksi, belum dimapping'
            ], 200);
        }

        // ✅ Simpan fingerprint baru
        PendingFingerprint::create([
            'fingerprint_id' => $request->fingerprint_id,
            'device_ip'      => $request->ip(),
            'detected_at'    => Carbon::now('Asia/Jakarta'),
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Fingerprint berhasil ditangkap'
        ]);
    }

    public function last()
    {
        $last = PendingFingerprint::where('is_mapped', false)
            ->orderBy('detected_at', 'desc')
            ->first();

        return response()->json([
            'fingerprint_id' => $last?->fingerprint_id
        ]);
    }
}
