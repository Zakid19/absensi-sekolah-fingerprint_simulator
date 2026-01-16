@extends('layouts.master')

@push('style')
    <link rel="stylesheet" href="/admin/plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
    <link rel="stylesheet" href="/admin/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="/admin/plugins/datatables-buttons/css/buttons.bootstrap4.min.css">
@endpush
@section('content')
    @php
        if (isset($student)) {
            $actionUrl = route('student.update', $student->id);
        } else {
            $actionUrl = route('student.store');
        }
    @endphp
{{ Session::get('message') }}
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    @if(isset($student))
                        <h1 class="m-0">Edit Siswa</h1>
                    @else
                        <h1 class="m-0">Tambah Siswa</h1>
                    @endif

                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="/dashboard">Home</a></li>
                        <li class="breadcrumb-item active">Siswa</li>
                    </ol>
                </div><!-- /.col -->
            </div><!-- /.row -->
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">

                    <div class="card card-secondary">
                        <div class="card-header">
                            <h3 class="card-title">{{ isset($student) ? 'Edit Siswa' : 'Tambah Siswa' }}</h3>
                        </div>
                        <form id="submitStudent" method="POST" action="{{ $actionUrl }}" enctype="multipart/form-data">
                            @if (@isset($student))
                                {{ method_field('PUT') }}
                                <input type="hidden" name="user_id" value="{{ $student->id }}" />
                            @endif
                            @csrf
                            <div class="card-body">
                                <div class="form-group">
                                    <label for="name">NIS</label>
                                    <input type="text" required class="form-control" name="nis" id="nis"
                                        placeholder="NIS" value="{{ isset($student) ? $student->nis : old('nis') }}">
                                    @error('nis')
                                        <div class="mt-2 text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <label for="name">Nama Siswa</label>
                                    <input type="text" required class="form-control" name="name" id="name"
                                        placeholder="Nama Siswa" value="{{ isset($student) ? $student->name : old('name') }}">
                                    @error('name')
                                        <div class="mt-2 text-danger">{{ $message }}</div>
                                    @enderror
                                </div>

                                @if(isset($student))
                                    {{-- MODE EDIT: kelas bisa diubah --}}
                                    <div class="form-group">
                                        <label for="class_room_id">Kelas</label>
                                        <select name="class_room_id" id="class_room_id" class="form-control" required>
                                            <option value="">-- Pilih Kelas --</option>
                                            @foreach($classes as $class)
                                                <option value="{{ $class->id }}"
                                                    {{ $student->class_room_id == $class->id ? 'selected' : '' }}>
                                                    {{ $class->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('class_room_id')
                                            <div class="mt-2 text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>

                                @elseif(isset($class_room_id))
                                    {{-- MODE CREATE DARI KELAS: hidden --}}
                                    <input type="hidden" name="class_room_id" value="{{ $class_room_id }}">

                                @else
                                    {{-- MODE CREATE MANUAL: dropdown --}}
                                    <div class="form-group">
                                        <label for="class_room_id">Kelas</label>
                                        <select name="class_room_id" id="class_room_id" class="form-control" required>
                                            <option value="">-- Pilih Kelas --</option>
                                            @foreach($classes as $class)
                                                <option value="{{ $class->id }}" {{ old('class_room_id') == $class->id ? 'selected' : '' }}>
                                                    {{ $class->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('class_room_id')
                                            <div class="mt-2 text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                @endif


                            </div>
                            <div class="card-footer">
                                <button type="submit" submitStudent="submit" class="btn btn-info btn-sm">Submit</button>
                                <a href="{{ url()->previous() ?? route('student.manage') }}" class="btn btn-secondary btn-sm">
                                    Batal
                                </a>
                            </div>
                        </form>
                    </div>

                </div>
            </div>
        </div>
    </section>
@endsection
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
<script src="/admin/plugins/datatables/jquery.dataTables.min.js"></script>
<script src="/admin/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
<script type="text/javascript">
    $(document).ready(function() {

        $('#description').summernote({
        placeholder: 'Deskripsi',
        tabsize: 2,
        height: 150
        });
    });
</script>


