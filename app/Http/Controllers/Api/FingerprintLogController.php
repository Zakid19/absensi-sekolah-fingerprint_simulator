<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use App\Models\Student;
use App\Models\Attendance;

class FingerprintLogController extends Controller
{
    /**
     * Endpoint khusus DEVICE
     * - bisa untuk absensi
     * - bisa untuk registrasi (pending)
     */
    public function receive(Request $request)
    {
        Log::info('Fingerprint device hit', $request->all());

        // 1. Validasi minimal
        $request->validate([
            'fingerprint_id' => 'required|string',
            'time' => 'nullable|date_format:Y-m-d H:i:s',
            'mode' => 'nullable|in:attendance,register'
        ]);

        $fingerprintId = trim($request->fingerprint_id);

        $now = $request->time
            ? Carbon::createFromFormat('Y-m-d H:i:s', $request->time, 'Asia/Jakarta')
            : Carbon::now('Asia/Jakarta');

        // default mode
        $mode = $request->mode ?? 'attendance';

        // 2. Cari siswa
        $student = Student::where('fingerprint_id', $fingerprintId)->first();

        /**
         * =========================
         * MODE REGISTRASI
         * =========================
         */
        if ($mode === 'register') {

            if ($student) {
                return response()->json([
                    'status' => 'exists',
                    'message' => 'Fingerprint sudah terdaftar',
                    'student' => $student->name,
                ]);
            }

            // simpan sebagai pending (tabel pending_fingerprints)
            \DB::table('pending_fingerprints')->updateOrInsert(
                ['fingerprint_id' => $fingerprintId],
                [
                    'detected_at' => $now,
                    'created_at'  => now(),
                    'updated_at'  => now(),
                ]
            );

            return response()->json([
                'status' => 'pending',
                'fingerprint_id' => $fingerprintId,
                'message' => 'Fingerprint ditangkap, menunggu mapping',
            ]);
        }

        /**
         * =========================
         * MODE ABSENSI NORMAL
         * =========================
         */
        if (!$student) {
            // fingerprint tidak dikenal → LOG SAJA
            Log::warning('Unknown fingerprint', [
                'fingerprint_id' => $fingerprintId
            ]);

            return response()->json([
                'status' => 'unknown',
                'message' => 'Fingerprint tidak terdaftar',
            ], 404);
        }

        $today = $now->toDateString();

        // cegah double scan
        $exists = Attendance::where('student_id', $student->id)
            ->whereDate('date', $today)
            ->exists();

        if ($exists) {
            return response()->json([
                'status' => 'duplicate',
                'message' => 'Sudah absen hari ini',
            ], 409);
        }

        // hitung keterlambatan (pakai logic lo yang sudah ada)
        $setting = \App\Models\AttendanceSetting::first();

        $startTime = Carbon::parse($setting->start_time)
            ->setDate($now->year, $now->month, $now->day);

        $lateLimit = $startTime->copy()->addMinutes($setting->late_minutes);

        $isLate = $now->greaterThan($lateLimit);

        Attendance::create([
            'student_id' => $student->id,
            'date'       => $today,
            'time_in'    => $now->format('H:i:s'),
            'status'     => $isLate ? 'TERLAMBAT' : 'HADIR',
            'is_late'    => $isLate,
        ]);

        return response()->json([
            'status'  => 'success',
            'student' => $student->name,
            'time'    => $now->format('H:i:s'),
            'late'    => $isLate,
        ]);
    }
}
