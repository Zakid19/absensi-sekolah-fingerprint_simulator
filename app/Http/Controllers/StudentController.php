<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\classRoom;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Str;

class StudentController extends Controller
{

    public function manage(ClassRoom $classRoom)
    {
        return view('students.manage', [
            'selected_class' => $classRoom,
            'table' => [
                'table_url' => route('student.data', ['class_room_id' => $classRoom->id]),
                'columns' => [
                    ['label' => 'NIS', 'name' => 'nis'],
                    ['label' => 'Nama Siswa', 'name' => 'name'],
                    ['label' => 'Kelas', 'name' => 'class_name'],
                    ['label' => 'Finger ID', 'name' => 'fingerprint_id'],
                    ['label' => 'Action', 'name' => 'action'],
                ],
            ],
        ]);
    }

    public function getData(Request $request)
    {
        $students = Student::with('classRoom:id,name')
            ->select('id','nis','name','class_room_id','fingerprint_id','created_at')
            ->where('class_room_id', $request->class_room_id)
            ->orderBy('name');

        return DataTables::of($students)
            ->addColumn('class_name', fn($s) => $s->classRoom->name ?? '-')
            ->addColumn('action', function ($students) {

                $string  = '<div class="d-flex gap-2">';

                // $string .= '<a href="'.route('fingerprint.set_fingerprint',$students->id).'" class="btn btn-warning btn-sm"><i class="fas fa-fingerprint"></i></a>';

                $string .= '<a href="'.route('student.edit',$students->id).'" class="btn btn-info btn-sm"><i class="fas fa-edit"></i></a>';

                $string .= '<button title="Hapus" class="btn btn-icon btn-sm btn-danger waves-effect waves-light delete-form"><i class="fa fa-trash"></i></button>';

                $string .= '<form  action="/student/delete/' . $students->id . '" method="POST">' . method_field('delete') . csrf_field() . '</form>';

                $string .= '</div>';

                return $string;
            })
            ->rawColumns(['action'])
            ->make(true);
    }


    public function create(Request $request) {

        return view('students.form', [
            'classes' => ClassRoom::all(),
            'class_room_id' => $request->class_room_id
        ]);

    }

    public function store(Request $request)
    {
        $exists = Student::where('nis', $request->nis)->exists();

        if ($exists) {
            return back()->withInput()->with('error', 'NIS sudah terdaftar.');
        }

        $input = $request->all();
        $input['slug'] = Str::slug($request->name);

        try {
            Student::create($input);

            return redirect()
                ->route('student.manage', $request->class_room_id)
                ->with('success', 'Data berhasil disimpan.');

        } catch (\Throwable $e) {
            return back()->with('error', $e->getMessage());
        }
    }


    public function edit($id)
    {
        $student = Student::findOrFail($id);

        return view('students.form', [
            'student' => $student,
            'classes' => ClassRoom::all(),
            'class_room_id' => $student->class_room_id
        ]);
    }


    public function update(Request $request, $id)
    {
        $student = Student::findOrFail($id);

        $exists = Student::where('nis', $request->nis)
            ->where('id', '!=', $student->id)
            ->exists();

        if ($exists) {
            return back()->withInput()->with('error', 'NIS sudah terdaftar.');
        }

        $input = $request->all();
        $input['slug'] = Str::slug($request->name);

        try {
            $student->update($input);

            return redirect()
                ->route('student.manage', $request->class_room_id ?? $student->class_room_id)
                ->with('success', 'Berhasil update siswa');

        } catch (\Throwable $e) {
            return back()->with('error', $e->getMessage());
        }
    }


    public function delete($id)
    {
        $student = Student::with('attendances')->findOrFail($id);
        $classRoomId = $student->class_room_id;

        // Jika tidak pakai cascade di DB, hapus child dulu
        if ($student->attendances()->exists()) {
            $student->attendances()->delete();
        }

        $student->delete();

        return redirect()
            ->route('student.manage', $classRoomId)
            ->with('success', 'Berhasil menghapus siswa.');
    }

}
