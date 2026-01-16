<?php

namespace App\Http\Controllers;
use App\Models\Student;
use App\Models\ClassRoom;
use App\Models\Attendance;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use App\Events\FingerprintSynced;


class FingerprintController extends Controller
{
    public function set_fingerprint($id)
    {
        try {
            $student = Student::findOrFail($id);

            return view('fingerprint.scan', compact('student'));

        } catch (\Throwable $e) {
            return back()->with('error', $e->getMessage());
        }
    }


    // public function scan(Request $request)
    // {
    //     \Log::info('Fingerprint scan hit', $request->all());

    //     $student = Student::where('fingerprint_id', $request->pin)->first();

    //     if (!$student) {
    //         return response()->json([
    //             'status' => 'error',
    //             'message' => 'Fingerprint tidak terdaftar'
    //         ], 404);
    //     }

    //     $now = \Carbon\Carbon::now('Asia/Jakarta');
    //     $today = $now->toDateString();

    //     $exists = Attendance::where('student_id', $student->id)
    //         ->whereDate('date', $today)
    //         ->exists();

    //     if ($exists) {
    //         return response()->json([
    //             'status' => 'duplicate',
    //             'message' => 'Sudah absen hari ini',
    //             'student' => $student->name
    //         ], 409);
    //     }

    //     $limit = $now->copy()->setTime(7, 0);
    //     $isLate = $now->gt($limit);

    //     Attendance::create([
    //         'student_id' => $student->id,
    //         'date'       => $today,
    //         'time_in'    => $now->format('H:i:s'),
    //         'status'     => $isLate ? 'TERLAMBAT' : 'HADIR',
    //         'is_late'    => $isLate,
    //     ]);

    //     return response()->json([
    //         'status'  => 'success',
    //         'student' => $student->name,
    //         'time'    => $now->format('H:i:s'),
    //         'is_late' => $isLate,
    //     ]);
    // }

    public function scan(Request $request)
    {
        Log::info('Fingerprint scan hit', $request->all());

        // 1. Validate payload
        $validator = Validator::make($request->all(), [
            'fingerprint_id'  => 'required|string',
            'time' => 'nullable|date_format:Y-m-d H:i:s',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid payload',
                'errors' => $validator->errors(),
            ], 422);
        }

        // 2. Ambil waktu scan (dari device atau now)
        $now = $request->time
            ? Carbon::createFromFormat('Y-m-d H:i:s', $request->time, 'Asia/Jakarta')
            : Carbon::now('Asia/Jakarta');

        $today = $now->toDateString();

        // 3. Cari siswa berdasarkan fingerprint
        $student = Student::where('fingerprint_id', $request->fingerprint_id)->first();

        // kalo suatu saat pake device
        // $fingerprint = $request->fingerprint_id ?? $request->pin;

        if (!$student) {
            return response()->json([
                'status' => 'error',
                'message' => 'Fingerprint tidak terdaftar'
            ], 404);
        }

        // 4. Cegah double scan hari yang sama
        $exists = Attendance::where('student_id', $student->id)
            ->whereDate('date', $today)
            ->exists();

        if ($exists) {
            return response()->json([
                'status' => 'duplicate',
                'message' => 'Sudah absen hari ini',
                'student' => $student->name
            ], 409);
        }

        // 5. Deteksi terlambat
        $limit = $now->copy()->setTime(7, 0, 0);
        $isLate = $now->greaterThan($limit);

        // 6. Simpan absensi
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
            'is_late' => $isLate,
        ]);
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

    public function clear($id)
    {
        $student = Student::findOrFail($id);

        $student->update(['fingerprint_id' => null]);

        return redirect()
            ->route('student.manage', $student->class_room_id)
            ->with('success', 'Fingerprint berhasil dihapus.');
    }


    // ========= TAHAP DEVICE ======= //

    public function wait($id)
    {
        Student::query()->update(['is_waiting_fingerprint' => false]);

        $student = Student::findOrFail($id);
        $student->update([
            'is_waiting_fingerprint' => true,
        ]);

        return response()->json([
            'status' => 'waiting',
            'student_id' => $student->id,
            'student_name' => $student->name,
        ]);
    }

    public function receiveFromDevice(Request $request)
    {
        Log::info('Fingerprint masuk dari device:', $request->all());

        $fingerprintId = $request->finger_id ?? $request->fingerprint_id;

        if (!$fingerprintId) {
            return response()->json(['status' => 'no_fingerprint'], 400);
        }

        $student = Student::where('is_waiting_fingerprint', true)->first();

        if (!$student) {
            return response()->json(['status' => 'no_waiting_student'], 409);
        }

        if (Student::where('fingerprint_id', $fingerprintId)->exists()) {
            return response()->json(['status' => 'fingerprint_already_used'], 409);
        }

        $student->update([
            'fingerprint_id' => $fingerprintId,
            'is_waiting_fingerprint' => false,
        ]);

        event(new FingerprintSynced($student));

        return response()->json([
            'status' => 'success',
            'student_id' => $student->id,
            'fingerprint_id' => $fingerprintId,
        ]);
    }

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
