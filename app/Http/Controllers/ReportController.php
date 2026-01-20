<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\ClassRoom;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\AttendanceExport;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use App\Models\AttendanceSetting;
use Carbon\Carbon;
use Yajra\DataTables\Facades\DataTables;

class ReportController extends Controller
{
    public function manage()
    {
        return view('reports.manage', [
            'classes' => ClassRoom::all()
        ]);
    }

    // public function getData(Request $r)
    // {
    //     $query = $this->buildQuery($r);

    //     return DataTables::of($query)
    //         ->addColumn('siswa_name', fn($a) => $a->students->name ?? '-')
    //         ->addColumn('class_name', fn($a) => $a->students->classRoom->name ?? '-')
    //         ->editColumn('status', fn($a) => $a->status ?? '-')
    //         ->make(true);
    // }

    public function getData(Request $r)
    {
        $setting = AttendanceSetting::first();

        // normalisasi jam masuk (AMAN mau H:i atau H:i:s)
        $startTime = Carbon::parse($setting?->start_time ?? '07:00')
            ->format('H:i');

        $lateMinutes = (int) ($setting?->late_minutes ?? 0);

        $query = $this->buildQuery($r);

        return DataTables::of($query)

            ->addColumn('siswa_name', fn ($a) =>
                optional($a->students)->name ?? '-'
            )

            ->addColumn('class_name', fn ($a) =>
                optional(optional($a->students)->classRoom)->name ?? '-'
            )

            ->addColumn('status', function ($a) use ($startTime, $lateMinutes) {

                if (!$a->date || !$a->time_in) {
                    return '-';
                }

                // jam masuk hari tersebut
                $startAt = Carbon::parse($a->date . ' ' . $startTime);

                // batas telat
                $lateLimit = $startAt->copy()->addMinutes($lateMinutes);

                // waktu scan siswa (AUTO detect format)
                $scanTime = Carbon::parse($a->date . ' ' . $a->time_in);

                return $scanTime->greaterThan($lateLimit)
                    ? 'TERLAMBAT'
                    : 'HADIR';
            })

            ->make(true);
    }

    public function exportExcel(Request $r)
    {
        $data = $this->buildQuery($r)->get();
        return Excel::download(new AttendanceExport($data), 'laporan-absensi.xlsx');
    }

    public function exportPdf(Request $r)
    {
        $data = $this->buildQuery($r)->get();
        $pdf = Pdf::loadView('reports.pdf', compact('data'));
        return $pdf->download('laporan-absensi.pdf');
    }

    private function buildQuery(Request $r)
    {
        $query = Attendance::with('students.classRoom');

        if ($r->date) {
            $query->whereDate('date', $r->date);
        }

        if ($r->month) {
            $query->whereYear('date', substr($r->month, 0, 4))
                  ->whereMonth('date', substr($r->month, 5, 2));
        }

        if ($r->class_room_id) {
            $query->whereHas('students', function ($q) use ($r) {
                $q->where('class_room_id', $r->class_room_id);
            });
        }

        return $query->orderBy('date', 'desc');
    }
}
