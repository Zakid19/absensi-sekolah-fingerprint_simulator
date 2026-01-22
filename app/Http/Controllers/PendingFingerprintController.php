<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PendingFingerprint;
use App\Models\Student;
use DB;

class PendingFingerprintController extends Controller
{
    public function index()
    {
        $pending = PendingFingerprint::where('is_mapped', false)
            ->orderByDesc('detected_at')
            ->get();

        $students = Student::orderBy('name')->get();

        return view('fingerprints.pending', compact('pending', 'students'));
    }
    
    public function map(Request $request)
    {
        $request->validate([
            'fingerprint_id' => 'required',
            'student_id'     => 'required|exists:students,id',
        ]);

        DB::transaction(function () use ($request) {

            // 1. Update student
            Student::where('id', $request->student_id)
                ->update([
                    'fingerprint_id' => $request->fingerprint_id
                ]);

            // 2. Tandai pending sudah dimapping
            PendingFingerprint::where('fingerprint_id', $request->fingerprint_id)
                ->update(['is_mapped' => true]);
        });

        return back()->with('success', 'Fingerprint berhasil dihubungkan ke siswa');
    }

    public function last()
    {
        $last = PendingFingerprint::latest()->first();

        if (!$last) {
            return response()->json([
                'fingerprint_id' => null
            ]);
        }

        return response()->json([
            'id'             => $last->id,
            'fingerprint_id' => $last->fingerprint_id,
            'status'         => $last->status,
            'detected_at'    => $last->detected_at?->format('Y-m-d H:i:s'),
        ]);
    }

}
