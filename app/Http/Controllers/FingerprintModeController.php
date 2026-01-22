<?php

namespace App\Http\Controllers;
use App\Models\SystemSetting;
use Illuminate\Http\Request;

class FingerprintModeController extends Controller
{
    public function set(Request $request)
    {
        $request->validate([
            'mode' => 'required|in:register,attendance'
        ]);

        SystemSetting::set('fingerprint_mode', $request->mode);

        if ($request->mode === 'attendance') {
            return redirect()
                ->route('fingerprints.attendance')
                ->with('success', 'Mode absensi diaktifkan');
        }

        return redirect()
            ->route('fingerprints.pending')
            ->with('success', 'Mode registrasi diaktifkan');
    }

}
