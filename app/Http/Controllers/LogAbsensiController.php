<?php

namespace App\Http\Controllers;

use App\Models\LogAbsensi;
use Illuminate\Http\Request;

class LogAbsensiController extends Controller
{
    public function index(Request $request)
    {
        $logs = LogAbsensi::with('student')
            ->latest('waktu_scan')
            ->paginate(50);

        return view('attendances.log', compact('logs'));
    }
}
