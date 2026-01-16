<?php

namespace App\Http\Controllers;
use App\Models\classRoom;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Str;

class ClassRoomController extends Controller
{

    public function index()
    {
        $classes = ClassRoom::withCount('students')->orderBy('name')->get();

        return view('class_room.index', compact('classes'));
    }

    public function show(ClassRoom $classRoom)
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

    public function manage()
    {
        $class['table'] = [
            'table_url' => '/class/data',
            'columns' => [
                ['label' => 'Nama Kelas', 'name' => 'name'],
                ['label' => 'Jumlah Siswa', 'name' => 'students_count'],
                ['label' => 'Action', 'name' => 'action', 'class' => 'text-right', 'width' => '120px'],
            ],
        ];
        return view('class_room.manage', $class);
    }

    public function getData()
    {
        try {
            $class = classRoom::withCount('students')
                // ->select(['id', 'name', 'created_at'])
                ->orderBy('created_at', 'desc');

            return DataTables::of($class)
                ->editColumn('created_at', fn($class) => date('d-m-Y', strtotime($class->created_at)))


                ->addColumn('action', function ($class) {
                    return '
                        <div class="d-flex justify-content-end align-items-center text-nowrap">

                            <a href="' . route('class.show', $class->id) . '"
                            class="btn btn-primary btn-sm mr-2" title="Lihat Siswa">
                                <i class="fas fa-users"></i>
                            </a>

                            <a href="' . route('class.edit', $class->id) . '"
                            class="btn btn-info btn-sm mr-2" title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>

                            <button type="button" title="Hapus"
                                    class="btn btn-danger btn-sm delete-form mr-2">
                                <i class="fa fa-trash"></i>
                            </button>

                            <form action="/class/delete/' . $class->id . '" method="POST" class="d-none">
                                ' . method_field('delete') . csrf_field() . '
                            </form>

                        </div>
                    ';
                })

                ->rawColumns(['action'])
                ->make(true);
        } catch (\Exception $error) {
            return response()->json(['error' => $error->getMessage()]);
        }
    }


    public function create() {

        return view('class_room.form');

    }

    public function store(Request $request)
    {
        // 🔹 Cek duplikat nama kelas
        $exists = ClassRoom::where('name', $request->name)->exists();

        if ($exists) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Nama kelas sudah terdaftar. Gunakan nama lain.');
        }

        $input = $request->all();
        $input['slug'] = Str::slug($request->name);

        try {
            ClassRoom::create($input);

            return redirect()
                ->route('class.manage')
                ->with('success', 'Data kelas berhasil disimpan.');

        } catch (\Throwable $e) {
            return redirect()
                ->back()
                ->with('error', $e->getMessage());
        }
    }


    public function edit($id)
    {
        try {
            $data['class'] = classRoom::findOrFail($id);
            return view('class_room.form', $data);
        } catch (\Exception $error) {
            return redirect()->back()->with('error', $error->getMessage());
        }
    }

    public function update($id, Request $request)
    {
        $class = ClassRoom::findOrFail($id);

        // 🔹 Cek duplikat nama kelas (kecuali dirinya sendiri)
        $exists = ClassRoom::where('name', $request->name)
            ->where('id', '!=', $class->id)
            ->exists();

        if ($exists) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Nama kelas sudah terdaftar. Gunakan nama lain.');
        }

        $slug = Str::slug($request->name);
        $inputUpdate = $request->all();
        $inputUpdate['slug'] = $slug;

        try {
            $class->update($inputUpdate);

            return redirect()
                ->route('class.manage')
                ->with('success', 'Berhasil mengupdate kelas');

        } catch (\Throwable $e) {
            return redirect()
                ->back()
                ->with('error', $e->getMessage());
        }
    }


    public function delete($id)
    {
        $class = classRoom::findOrFail($id);

            if ($class->students()->exists()) {
            return back()->with('error', 'Kelas masih memiliki siswa. Pindahkan atau hapus siswa terlebih dahulu.');
        }

        try {

            $classDelete = $class->delete();


            if ($classDelete) {
                return redirect()->route('class.manage')->with('success', 'Berhasil Menghapus data kelas');
            }
        } catch (\Exception $error) {
            return response()->json(['error' => $error->getMessage()]);
        }
    }
}
