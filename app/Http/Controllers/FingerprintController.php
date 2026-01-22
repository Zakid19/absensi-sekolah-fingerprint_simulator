<?php

namespace App\Http\Controllers;
use App\Models\Student;
use App\Models\ClassRoom;
use App\Models\Attendance;
use App\Models\PendingFingerprint;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use App\Models\SystemSetting;
use App\Events\FingerprintSynced;
use App\Models\AttendanceSetting;


class FingerprintController extends Controller
{

    // public function set_fingerprint($id)
    // {
    //     try {
    //         $student = Student::findOrFail($id);

    //         return view('fingerprint.scan', compact('student'));

    //     } catch (\Throwable $e) {
    //         return back()->with('error', $e->getMessage());
    //     }
    // }

    public function attedance()
    {
        return view('fingerprints.attendance');
    }

    // public function scan(Request $request)
    // {
    //     Log::info('Fingerprint scan hit', $request->all());

    //     // 1. Validate payload
    //     $validator = Validator::make($request->all(), [
    //         'fingerprint_id' => 'required|string',
    //         'time'           => 'nullable|date_format:Y-m-d H:i:s',
    //     ]);

    //     if ($validator->fails()) {
    //         return response()->json([
    //             'status'  => 'error',
    //             'message' => 'Invalid payload',
    //             'errors'  => $validator->errors(),
    //         ], 422);
    //     }

    //     // 2. Ambil waktu scan (prioritas: device time)
    //     $now = $request->time
    //         ? Carbon::createFromFormat('Y-m-d H:i:s', $request->time, 'Asia/Jakarta')
    //         : Carbon::now('Asia/Jakarta');

    //     $today = $now->toDateString();

    //     // 3. Cari siswa (WAJIB sudah punya fingerprint)
    //     $student = Student::whereNotNull('fingerprint_id')
    //         ->where('fingerprint_id', $request->fingerprint_id)
    //         ->first();

    //     if (!$student) {
    //         return response()->json([
    //             'status'  => 'error',
    //             'message' => 'Fingerprint tidak terdaftar',
    //         ], 404);
    //     }

    //     // 4. Cegah double scan
    //     $exists = Attendance::where('student_id', $student->id)
    //         ->whereDate('date', $today)
    //         ->exists();

    //     if ($exists) {
    //         return response()->json([
    //             'status'  => 'duplicate',
    //             'message' => 'Sudah absen hari ini',
    //             'student' => $student->name,
    //         ], 409);
    //     }

    //     // ===============================
    //     // 5. LOGIKA JAM ABSENSI (FIXED)
    //     // ===============================

    //     $setting = \App\Models\AttendanceSetting::first();

    //     // Normalisasi jam (ANTI trailing data)
    //     $rawStartTime = $setting?->start_time ?? '07:00';

    //     $startTime = Carbon::parse($rawStartTime)
    //         ->timezone('Asia/Jakarta')
    //         ->format('H:i');

    //     $lateMinutes = (int) ($setting?->late_minutes ?? 0);

    //     // Jam masuk hari ini
    //     $startAt = Carbon::createFromFormat('H:i', $startTime, 'Asia/Jakarta')
    //         ->setDate($now->year, $now->month, $now->day);

    //     // Batas telat
    //     $lateLimit = $startAt->copy()->addMinutes($lateMinutes);

    //     $isLate = $now->greaterThan($lateLimit);

    //     // 6. Simpan absensi
    //     Attendance::create([
    //         'student_id' => $student->id,
    //         'date'       => $today,
    //         'time_in'    => $now->format('H:i:s'),
    //         'status'     => $isLate ? 'TERLAMBAT' : 'HADIR',
    //         'is_late'    => $isLate,
    //     ]);

    //     return response()->json([
    //         'status'       => 'success',
    //         'student'      => $student->name,
    //         'time'         => $now->format('H:i:s'),
    //         'is_late'      => $isLate,
    //         'start_time'   => $startTime,
    //         'late_limit'   => $lateLimit->format('H:i'),
    //     ]);
    // }

    public function scan(Request $request)
    {
        Log::info('Fingerprint scan hit', $request->all());

        $validator = Validator::make($request->all(), [
            'fingerprint_id' => 'required|string',
            'time' => 'nullable|date_format:Y-m-d H:i:s',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid payload',
                'errors' => $validator->errors(),
            ], 422);
        }

        // waktu scan (device > server)
        $now = $request->time
            ? Carbon::parse($request->time, 'Asia/Jakarta')
            : Carbon::now('Asia/Jakarta');

        $today = $now->toDateString();

        $student = Student::whereNotNull('fingerprint_id')
            ->where('fingerprint_id', $request->fingerprint_id)
            ->first();

        if (!$student) {
            return response()->json([
                'status' => 'error',
                'message' => 'Fingerprint tidak terdaftar',
            ], 404);
        }

        $exists = Attendance::where('student_id', $student->id)
            ->whereDate('date', $today)
            ->exists();

        if ($exists) {
            return response()->json([
                'status' => 'duplicate',
                'message' => 'Sudah absen hari ini',
                'student' => $student->name,
            ], 409);
        }

        // ===============================
        // JAM ABSENSI (SINKRON SETTING)
        // ===============================
        $setting = AttendanceSetting::first();

        $startTime = Carbon::parse(
            $setting?->start_time ?? '07:00'
        )->format('H:i');

        $lateMinutes = (int) ($setting?->late_minutes ?? 0);

        $startAt = Carbon::parse($today . ' ' . $startTime);
        $lateLimit = $startAt->copy()->addMinutes($lateMinutes);

        $isLate = $now->greaterThan($lateLimit);

        // ⚠️ SIMPAN DATA MENTAH SAJA
        Attendance::create([
            'student_id' => $student->id,
            'date'       => $today,
            'time_in'    => $now->format('H:i:s'),
            'status'     => $isLate ? 'TERLAMBAT' : 'HADIR',
            'is_late'    => $isLate,
        ]);


        return response()->json([
            'status'     => 'success',
            'student'    => $student->name,
            'time'       => $now->format('H:i:s'),
            'is_late'    => $isLate,
            'start_time' => $startTime,
            'late_limit' => $lateLimit->format('H:i'),
        ]);
    }

    public function push(Request $request)
    {
        try {
            Log::info('Fingerprint PUSH hit', [
                'payload' => $request->all(),
                'ip'      => $request->ip(),
            ]);

            // =========================
            // 1. AMBIL FINGERPRINT ID
            // =========================
            $fingerprintId =
                $request->input('PIN')
                ?? $request->input('user_id')
                ?? $request->input('uid')
                ?? $request->input('enroll_id');

            if (!$fingerprintId) {
                return response('NO_FINGERPRINT_ID', 400);
            }

            // =========================
            // 2. WAKTU SCAN
            // =========================
            try {
                $detectedAt = $request->input('DateTime')
                    ? Carbon::parse($request->input('DateTime'))
                    : now();
            } catch (\Exception $e) {
                $detectedAt = now();
            }

            // =========================
            // 3. BACA MODE
            // =========================
            $mode = SystemSetting::get('fingerprint_mode', 'attendance');

            // ==================================================
            // 🔵 MODE REGISTER
            // ==================================================
            if ($mode === 'register') {

                // Sudah dipakai siswa?
                if (Student::where('fingerprint_id', $fingerprintId)->exists()) {

                    PendingFingerprint::updateOrCreate(
                        ['fingerprint_id' => $fingerprintId],
                        [
                            'device_ip'   => $request->ip(),
                            'detected_at' => $detectedAt,
                            'is_mapped'   => true,
                            'status'      => 'mapped',
                        ]
                    );

                    return response('FINGERPRINT_ALREADY_USED', 200);
                }

                // Duplicate pending?
                $pending = PendingFingerprint::where('fingerprint_id', $fingerprintId)
                    ->where('is_mapped', false)
                    ->first();

                if ($pending) {
                    $pending->update([
                        'detected_at' => $detectedAt,
                        'status'      => 'duplicate',
                    ]);

                    return response('FINGERPRINT_DUPLICATE', 200);
                }

                // Fingerprint baru
                PendingFingerprint::create([
                    'fingerprint_id' => $fingerprintId,
                    'device_ip'      => $request->ip(),
                    'detected_at'    => $detectedAt,
                    'is_mapped'      => false,
                    'status'         => 'new',
                ]);

                return response('REGISTER_OK', 200);
            }

            if ($mode === 'attendance') {

                $student = Student::where('fingerprint_id', $fingerprintId)->first();

                if (!$student) {
                    return response('UNKNOWN_FINGERPRINT', 200);
                }

                $today = now()->toDateString();

                // Anti double scan
                if (
                    Attendance::where('student_id', $student->id)
                        ->where('date', $today)
                        ->exists()
                ) {
                    return response('ALREADY_ATTENDED', 200);
                }

                // Status hadir / terlambat (contoh jam 07:00)
                $scanTime  = now();
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

            // if ($mode === 'attendance') {

            //     $student = Student::where('fingerprint_id', $fingerprintId)->first();

            //     if (!$student) {
            //         return response('UNKNOWN_FINGERPRINT', 200);
            //     }

            //     $today = now()->toDateString();

            //     // Anti double scan
            //     if (
            //         Attendance::where('student_id', $student->id)
            //             ->where('date', $today)
            //             ->exists()
            //     ) {
            //         return response('ALREADY_ATTENDED', 200);
            //     }

            //     // Status hadir / terlambat (contoh jam 07:00)
            //     $scanTime  = now();
            //     $limitTime = Carbon::createFromTime(7, 0, 0);

            //     $status = $scanTime->gt($limitTime)
            //         ? 'terlambat'
            //         : 'hadir';

            //     Attendance::create([
            //         'student_id' => $student->id,
            //         'date'       => $today,
            //         'time_in'    => $scanTime->format('H:i:s'),
            //         'status'     => $status,
            //     ]);

            //     return response('ATTENDANCE_OK', 200);
            // }

            return response('UNKNOWN_MODE', 400);

        } catch (\Throwable $e) {

            Log::error('Fingerprint PUSH ERROR', [
                'message' => $e->getMessage(),
                'line'    => $e->getLine(),
                'file'    => $e->getFile(),
            ]);

            return response('INTERNAL_SERVER_ERROR', 500);
        }
    }



    // public function update(Request $request, $id)
    // {
    //     $request->validate([
    //         'fingerprint_id' => [
    //             'required',
    //             Rule::unique('students', 'fingerprint_id')->ignore($id),
    //         ],
    //     ], [
    //         'fingerprint_id.required' => 'Fingerprint wajib diisi.',
    //         'fingerprint_id.unique'   => 'Fingerprint sudah digunakan siswa lain.',
    //     ]);

    //     $student = Student::findOrFail($id);

    //     $student->update([
    //         'fingerprint_id' => $request->fingerprint_id,
    //     ]);

    //     return redirect()
    //         ->route('student.manage', $student->class_room_id)
    //         ->with('success', 'Fingerprint berhasil diupdate.');
    // }

    // by device

    public function index()
    {
        $classes = ClassRoom::with(['students' => function ($q) {
            $q->whereNull('fingerprint_id');
        }])->get();


        return view('fingerprint.register', compact('classes'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'fingerprint_id' => [
                'required',
                'string',
                Rule::unique('students', 'fingerprint_id')->ignore($id),
            ],
        ], [
            'fingerprint_id.required' => 'Fingerprint wajib diisi.',
            'fingerprint_id.unique'   => 'Fingerprint sudah digunakan siswa lain.',
        ]);

        $student = Student::findOrFail($id);

        // Optional: prevent overwrite silently
        if ($student->fingerprint_id && $student->fingerprint_id !== $request->fingerprint_id) {
            Log::warning('Fingerprint overwritten', [
                'student_id' => $student->id,
                'old' => $student->fingerprint_id,
                'new' => $request->fingerprint_id,
            ]);
        }

        $student->update([
            'fingerprint_id' => $request->fingerprint_id,
        ]);

        return redirect()
            ->route('student.manage', $student->class_room_id)
            ->with('success', 'Fingerprint berhasil diupdate.');
    }

    public function sync(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:students,id',
            'fingerprint_id' => 'required|string|unique:students,fingerprint_id',
        ]);

        $student = Student::findOrFail($request->student_id);

        $student->update([
            'fingerprint_id' => $request->fingerprint_id,
        ]);

        event(new FingerprintSynced($student));

        return response()->json(['status' => 'success']);
    }

    // public function clear($id)
    // {
    //     $student = Student::findOrFail($id);

    //     $student->update(['fingerprint_id' => null]);

    //     return redirect()
    //         ->route('student.manage', $student->class_room_id)
    //         ->with('success', 'Fingerprint berhasil dihapus.');
    // }


    // ========= TAHAP DEVICE ======= //

    // public function wait($id)
    // {
    //     Student::query()->update(['is_waiting_fingerprint' => false]);

    //     $student = Student::findOrFail($id);
    //     $student->update([
    //         'is_waiting_fingerprint' => true,
    //     ]);

    //     return response()->json([
    //         'status' => 'waiting',
    //         'student_id' => $student->id,
    //         'student_name' => $student->name,
    //     ]);
    // }

    // public function receiveFromDevice(Request $request)
    // {
    //     Log::info('Fingerprint masuk dari device:', $request->all());

    //     $fingerprintId = $request->finger_id ?? $request->fingerprint_id;

    //     if (!$fingerprintId) {
    //         return response()->json(['status' => 'no_fingerprint'], 400);
    //     }

    //     $student = Student::where('is_waiting_fingerprint', true)->first();

    //     if (!$student) {
    //         return response()->json(['status' => 'no_waiting_student'], 409);
    //     }

    //     if (Student::where('fingerprint_id', $fingerprintId)->exists()) {
    //         return response()->json(['status' => 'fingerprint_already_used'], 409);
    //     }

    //     $student->update([
    //         'fingerprint_id' => $fingerprintId,
    //         'is_waiting_fingerprint' => false,
    //     ]);

    //     event(new FingerprintSynced($student));

    //     return response()->json([
    //         'status' => 'success',
    //         'student_id' => $student->id,
    //         'fingerprint_id' => $fingerprintId,
    //     ]);
    // }

    // public function update(Request $request, $id)
    // {
    //     $fingerprintId = $request->fingerprint_id;

    //     $request->validate([
    //         'fingerprint_id' => [
    //             'required',
    //             Rule::unique('students', 'fingerprint_id')->ignore($id),
    //         ],
    //     ], [
    //         'fingerprint_id.required' => 'Fingerprint wajib diisi.',
    //         'fingerprint_id.unique'   => 'Fingerprint sudah digunakan siswa lain.',
    //     ]);

    //     $student = Student::findOrFail($id);

    //     $student->update([
    //         'fingerprint_id' => $fingerprintId,
    //         'is_waiting_fingerprint' => false,
    //     ]);

    //     return redirect()
    //         ->route('student.manage', $student->class_room_id)
    //         ->with('success', 'Fingerprint berhasil diupdate.');
    // }

}
