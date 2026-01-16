<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\ClassRoom;

class DeviceFingerprintController extends Controller
{
    public function index()
    {
        $classes = ClassRoom::withCount('students')->get();
        return view('device.classes', compact('classes'));
    }

    public function showClass($id)
    {
        $class = ClassRoom::with('students')->findOrFail($id);
        return view('device.fingerprint', compact('class'));
    }

    public function scan()
    {
        $classes = ClassRoom::withCount('students')->get();
        return view('device.scan', compact('classes'));
    }

    public function register(Request $request)
{
    $validated = $request->validate(
        [
            'student_id' => 'required|exists:students,id',
            'fingerprint_id' => 'required|unique:students,fingerprint_id',
        ],
        [
            'fingerprint_id.required' => 'Fingerprint wajib diisi.',
            'fingerprint_id.unique'   => 'Fingerprint sudah digunakan oleh siswa lain.',
        ]
    );

    $student = Student::findOrFail($validated['student_id']);

    $student->fingerprint_id = $validated['fingerprint_id'];
    $student->save();

    return back()->with('success', 'Fingerprint berhasil diregistrasi.');
}


     public function clear($id)
    {
        $student = Student::findOrFail($id);

        $student->update(['fingerprint_id' => null]);

        return back()->with('success', 'Fingerprint berhasil diregistrasi.');
    }
}
