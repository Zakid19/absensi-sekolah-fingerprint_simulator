<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PendingFingerprint;
use App\Models\Student;

class FingerprintMappingController extends Controller
{
    public function map(Request $request)
    {
        $request->validate([
            'fingerprint_id' => 'required',
            'student_id'     => 'required|exists:students,id'
        ]);

        $pending = PendingFingerprint::where('fingerprint_id', $request->fingerprint_id)
            ->where('is_mapped', false)
            ->firstOrFail();

        $student = Student::findOrFail($request->student_id);

        // ❌ student sudah punya fingerprint
        if ($student->fingerprint_id) {
            return back()->with('error', 'Siswa sudah memiliki fingerprint');
        }

        // ❌ fingerprint bentrok
        if (Student::where('fingerprint_id', $pending->fingerprint_id)->exists()) {
            return back()->with('error', 'Fingerprint sudah dipakai siswa lain');
        }

        // ✅ mapping
        $student->update([
            'fingerprint_id' => $pending->fingerprint_id
        ]);

        $pending->update([
            'is_mapped' => true
        ]);

        return back()->with('success', 'Fingerprint berhasil dihubungkan');
    }
}
