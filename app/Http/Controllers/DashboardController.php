<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ClassRoom;
use App\Models\Attendance;
use App\Models\Student;
use Carbon\Carbon;


class DashboardController extends Controller
{

    public function index()
    {
        $today = Carbon::today('Asia/Jakarta');

        // Statistik utama
        $totalSiswa = Student::count();
        $totalKelas = ClassRoom::count();

        $totalHadir = Attendance::whereDate('date', $today)
            ->where('status', 'HADIR')
            ->count();

        $totalTerlambat = Attendance::whereDate('date', $today)
            ->where('is_late', true)
            ->count();

        $belumAbsen = Student::whereDoesntHave('attendances', function ($q) use ($today) {
            $q->whereDate('date', $today);
        })->count();

        return view('dashboard', compact(
            'totalSiswa',
            'totalKelas',
            'totalHadir',
            'totalTerlambat',
            'belumAbsen'
        ));
    }
}
