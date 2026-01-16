<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Teacher;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\DB;

class TeacherController extends Controller
{
    public function manage()
    {
        $teacher['table'] = [
            'table_url' => '/teacher/data',
            'columns' => [
                ['label' => 'Nama Guru', 'name' => 'name'],
                ['label' => 'Email', 'name' => 'user_email'],
                ['label' => 'Role', 'name' => 'role'],
                ['label' => 'Action', 'name' => 'action'],
            ],
        ];
        return view('teacher.manage', $teacher);
    }

    public function getData()
    {
        try {
            // $teacher = Teacher::with('users')->select(['id', 'name', 'email', 'role'])
            //     ->orderBy('created_at', 'desc');

            $teacher = Teacher::join('users', 'users.id', '=', 'teachers.user_id')
            ->select(
                'teachers.id',
                'teachers.name',
                'teachers.email',
                'teachers.phone',
                'users.email as user_email',
                'users.role',
                'teachers.created_at'
            )
            ->orderBy('teachers.created_at', 'desc');

            return DataTables::of($teacher)

                ->addColumn('action', function ($teacher) {
                    $string  = '<div class="d-flex gap-2 align-items-center">';

                        $string .=   '<a href="' . route('teacher.edit', $teacher->id) . '" class="btn btn-info btn-sm">
                                        <i class="fas fa-edit"></i>
                                    </a>';

                        $string .= '<button title="Hapus" class="btn btn-icon btn-sm btn-danger waves-effect waves-light delete-form"><i class="fa fa-trash"></i></button>';

                        $string .= '<form  action="/teacher/delete/' . $teacher->id . '" method="POST">' . method_field('delete') . csrf_field() . '</form>';

                    $string .= '</div>';

                    return $string;
                })
                ->rawColumns(['action'])
                ->make(true);
        } catch (\Exception $error) {
            return response()->json(['error' => $error->getMessage()]);
        }
    }

    public function create()
    {
        return view('teacher.form');
    }

    // public function store(Request $request)
    // {
    //     $data = $request->validate([
    //         'name'     => 'required|string|max:255',
    //         'email'    => 'required|email|unique:users,email',
    //         'password' => 'nullable|min:6',
    //     ]);

    //     $password = $data['password'] ?? Str::random(8);

    //     User::create([
    //         'name'     => $data['name'],
    //         'email'    => $data['email'],
    //         'password' => Hash::make($password),
    //         'role'     => 'teacher',
    //     ]);

    //     return redirect()->route('teachers.index')
    //         ->with('success', 'Guru berhasil ditambahkan.');
    // }

    public function store(Request $request)
    {
        $request->validate([
            'name'  => 'required',
            'email' => 'required|email|unique:users,email',
        ]);

        DB::transaction(function () use ($request) {

            $user = User::create([
                'name'     => $request->name,
                'email'    => $request->email,
                'password' => Hash::make('teacher123'), // default password
                'role'     => 'teacher',
            ]);

            Teacher::create([
                'name'    => $request->name,
                'email'   => $request->email,
                'phone'   => $request->phone,
                'user_id' => $user->id,
            ]);
        });

        return redirect()->route('teacher.manage')
            ->with('success', 'Guru berhasil ditambahkan & akun dibuat.');
    }

    public function edit($id)
    {
        $teacher = Teacher::with('user')->findOrFail($id);

        return view('teacher.form', compact('teacher'));
    }

    public function update(Request $request, $id)
    {
        $teacher = Teacher::with('user')->findOrFail($id);

        $request->validate([
            'name'  => 'required',
            'email' => 'required|email|unique:users,email,' . $teacher->user_id,
            'password' => 'nullable|min:6|confirmed',
        ]);

        \DB::transaction(function () use ($request, $teacher) {

            // update user login
            $teacher->user->update([
                'name'  => $request->name,
                'email' => $request->email,
            ]);

            // update data guru
            $teacher->update([
                'name'  => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
            ]);

             // 🔹 update password jika diisi
            if ($request->filled('password')) {
                $teacher->user->update([
                    'password' => \Hash::make($request->password),
                ]);
            }
        });

        return redirect()->route('teacher.manage')
            ->with('success', 'Data guru berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $teacher = Teacher::with('user')->findOrFail($id);

        \DB::transaction(function () use ($teacher) {
            // hapus user login dulu
            $teacher->user()->delete();

            // hapus data guru
            $teacher->delete();
        });

        return redirect()
            ->route('teacher.manage')
            ->with('success', 'Guru dan akun login berhasil dihapus.');
    }



}
