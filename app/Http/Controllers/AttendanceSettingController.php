<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AttendanceSetting;
use Carbon\Carbon;

class AttendanceSettingController extends Controller
{
    public function edit()
    {
        // Ambil atau buat default
        $setting = AttendanceSetting::firstOrCreate(
            ['id' => 1],
            [
                'start_time'   => '07:00',
                'late_minutes' => 0,
            ]
        );

        return view('attendances.settings', compact('setting'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'start_time'   => 'required|date_format:H:i',
            'late_minutes' => 'required|integer|min:0|max:120',
        ]);

        $setting = AttendanceSetting::firstOrCreate(['id' => 1]);

        $setting->update([
            'start_time' => Carbon::createFromFormat('H:i', $request->start_time)->format('H:i:s'),
            'late_minutes' => $request->late_minutes,
        ]);

        return redirect()
            ->route('attendance.settings.edit')
            ->with('success', 'Pengaturan jam absensi berhasil disimpan');
    }
}
