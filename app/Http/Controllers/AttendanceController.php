<?php

namespace App\Http\Controllers;
use App\Models\Attendance;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Str;

class AttendanceController extends Controller
{

    public function manage(Request $request)
    {
        $attendance['table'] = [
            'table_url' => '/attendance/data',
            'columns' => [
                ['label' => 'Siswa', 'name' => 'siswa_name'],
                ['label' => 'Kelas', 'name' => 'kelas_name'],
                ['label' => 'Tanggal', 'name' => 'date'],
                ['label' => 'Waktu Masuk', 'name' => 'time_in'],
                ['label' => 'Status', 'name' => 'status'],
                ['label' => 'Action', 'name' => 'action'],
            ],
        ];

        $attendance['default_date'] = $request->get('date', now()->toDateString());

        return view('attendances.manage', $attendance);
    }

    // public function getData()
    // {
    //     try {
    //         $attendances = Attendance::with('students')->select(['id', 'date', 'time_in', 'status', 'student_id'])
    //             ->orderBy('created_at', 'desc');

    //         return DataTables::of($attendances)
    //             ->addColumn('siswa_name', fn($attendance) => $attendance->students ? $attendance->students->name : '\Tidak ada service')
    //             // ->editColumn('created_at', fn($attendances) => date('d-m-Y', strtotime($attendances->created_at)))

    //             ->addColumn('action', function ($attendance) {
    //                 '<div style="display: flex; gap: 0.5rem; align-items: center;">' .
    //                     $string = '<button title="Hapus" class="btn btn-icon btn-sm btn-danger waves-effect waves-light delete-form"><i class="fa fa-trash"></i></button>';

    //                     $string .= '<form  action="/attendance/delete/' . $attendance->id . '" method="POST">' . method_field('delete') . csrf_field() . '</form>';

    //                 '</div>';

    //                 return $string;
    //             })
    //             ->rawColumns(['action'])
    //             ->make(true);
    //     } catch (\Exception $error) {
    //         return response()->json(['error' => $error->getMessage()]);
    //     }
    // }

    public function getData(Request $request)
    {
        try {

            // Ambil date dari request atau fallback ke hari ini
            $date = $request->input('date');

            if (!$date) {
                $date = Attendance::max('date') ?? now()->toDateString();
            }

            $attendances = Attendance::with(['students.classRoom'])
                ->select(['id', 'date', 'time_in', 'status', 'student_id'])
                ->whereDate('date', $date)
                ->orderBy('time_in', 'asc');

            if ($request->filled('class_room_id')) {
                $attendances->whereHas('students', function ($q) use ($request) {
                    $q->where('class_room_id', $request->class_room_id);
                });
            }

            if ($request->filled('student_id')) {
                $attendances->where('student_id', $request->student_id);
            }

            return DataTables::of($attendances)
                ->addColumn('siswa_name', fn($a) => optional($a->students)->name ?? '-')
                ->addColumn('kelas_name', fn($a) => optional(optional($a->students)->classRoom)->name ?? '-')
                ->addColumn('status', fn($a) => $a->status ? 'Terlambat' : 'Hadir')
                ->addColumn('action', function ($a) {
                    $name = optional($a->students)->name ?? '-';
                    return '
                    <div style="display:flex;gap:.5rem;">
                        <button class="btn btn-sm btn-primary btn-history" data-id="'.$a->student_id.'" data-name="'.$name.'">Detail</button>
                        <form action="/attendance/delete/'.$a->id.'" method="POST" style="display:inline;">
                            '.method_field('delete').csrf_field().'
                            <button class="btn btn-sm btn-danger"><i class="fa fa-trash"></i></button>
                        </form>
                    </div>';
                })
                ->rawColumns(['action'])
                ->make(true);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }


    public function delete($id)
    {
        $attendance = Attendance::findOrFail($id);
        try {

            $attendanceDelete = $attendance->delete();

            if ($attendanceDelete) {
                return redirect()->route('attendance.manage')->with('success', 'Berhasil Menghapus data absensi');
            }
        } catch (\Exception $error) {
            return response()->json(['error' => $error->getMessage()]);
        }
    }

    public function history($studentId)
    {
        $history = Attendance::where('student_id', $studentId)
            ->orderBy('date', 'desc')
            ->get(['date', 'time_in', 'status', 'is_late']);

        return response()->json($history);
    }


}
