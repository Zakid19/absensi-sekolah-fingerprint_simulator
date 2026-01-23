<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\Attendance;
use App\Models\SystemSetting;
use Carbon\Carbon;

class FingerprintAttendanceController extends Controller
{
    public function push(Request $request)
    {
        // MODE GUARD
        if (SystemSetting::get('fingerprint_mode') !== 'attendance') {
            return response('SKIP_ATTENDANCE', 200);
        }

        // Ambil fingerprint ID
        $fingerprintId =
            $request->input('PIN')
            ?? $request->input('uid')
            ?? $request->input('enroll_id');

        if (!$fingerprintId) {
            return response('NO_FINGERPRINT_ID', 400);
        }

        // Cari siswa
        $student = Student::where('fingerprint_id', $fingerprintId)->first();
        if (!$student) {
            return response('UNKNOWN_FINGERPRINT', 200);
        }

        $today = now()->toDateString();

        // Anti double scan
        if (Attendance::where('student_id', $student->id)
            ->where('date', $today)
            ->exists()) {
            return response('ALREADY_ATTENDED', 200);
        }

        // Tentukan status (contoh jam masuk 07:00)
        $scanTime = now();
        $limitTime = Carbon::createFromTime(7, 0, 0);

        $status = $scanTime->gt($limitTime)
            ? 'terlambat'
            : 'hadir';

        Attendance::create([
            'student_id' => $student->id,
            'date'       => $today,
            'time_in'    => $scanTime->format('H:i:s'),
            'status'     => $status,
        ]);

        return response('ATTENDANCE_OK', 200);
    }

    public function last()
    {
        $last = Attendance::with('students')
            ->orderBy('id', 'desc')
            ->first();

        if (!$last) {
            return response()->json(null);
        }

        return response()->json([
            'id'        => $last->id,               // 🔑 EVENT KEY
            'name'      => $last->student->name ?? '-',
            'time_in'   => $last->time_in,
            'status'    => $last->status,
            'scan_time' => $last->created_at->format('H:i:s'),
        ]);
    }

}
