@extends('layouts.master')

@section('content')
@php
    $isEdit = isset($student);
    $actionUrl = $isEdit
        ? route('student.update', $student->id)
        : route('student.store');
@endphp

{{-- ================= HEADER ================= --}}
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2 align-items-center">
            <div class="col-sm-7">
                <h1 class="m-0 font-weight-bold">
                    {{ $isEdit ? 'Edit Siswa' : 'Tambah Siswa' }}
                </h1>
                <small class="text-muted">
                    {{ $isEdit
                        ? 'Perbarui data siswa'
                        : 'Daftarkan siswa ke dalam kelas' }}
                </small>
            </div>
            <div class="col-sm-5">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item">
                        <a href="{{ route('dashboard') }}">Home</a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="{{ url()->previous() }}" >
                             Siswa
                        </a>

                        {{-- <a href="{{ route('student.manage') }}">Siswa</a> --}}
                    </li>
                    <li class="breadcrumb-item active">
                        {{ $isEdit ? 'Edit' : 'Tambah' }}
                    </li>
                </ol>
            </div>
        </div>
    </div>
</div>

{{-- ================= CONTENT ================= --}}
<section class="content">
<div class="container-fluid">

<div class="card card-primary card-outline">

    {{-- Card Header --}}
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-user-graduate mr-1"></i>
            Informasi Siswa
        </h3>
    </div>

    <form method="POST" action="{{ $actionUrl }}">
        @csrf
        @if($isEdit)
            @method('PUT')
        @endif

        <div class="card-body">

            {{-- INFO --}}
            <div class="alert alert-info small">
                <i class="fas fa-info-circle mr-1"></i>
                Data siswa digunakan untuk absensi dan identifikasi fingerprint.
            </div>

            <div class="row">

                {{-- NIS --}}
                <div class="col-md-6">
                    <div class="form-group">
                        <label>NIS</label>
                        <input type="text"
                               name="nis"
                               class="form-control"
                               placeholder="Nomor Induk Siswa"
                               required
                               value="{{ old('nis', $student->nis ?? '') }}">
                        @error('nis')
                            <div class="text-danger mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                {{-- NAMA --}}
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Nama Siswa</label>
                        <input type="text"
                               name="name"
                               class="form-control"
                               placeholder="Nama lengkap siswa"
                               required
                               value="{{ old('name', $student->name ?? '') }}">
                        @error('name')
                            <div class="text-danger mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                {{-- KELAS --}}
                <div class="col-md-6">
                    @if($isEdit)
                        {{-- MODE EDIT --}}
                        <div class="form-group">
                            <label>Kelas</label>
                            <select name="class_room_id" class="form-control" required>
                                <option value="">-- Pilih Kelas --</option>
                                @foreach($classes as $class)
                                    <option value="{{ $class->id }}"
                                        {{ $student->class_room_id == $class->id ? 'selected' : '' }}>
                                        {{ $class->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('class_room_id')
                                <div class="text-danger mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                    @elseif(isset($class_room_id))
                        {{-- CREATE DARI HALAMAN KELAS --}}
                        <input type="hidden" name="class_room_id" value="{{ $class_room_id }}">
                        <div class="form-group">
                            <label>Kelas</label>
                            <input type="text"
                                   class="form-control"
                                   value="{{ optional($classes->firstWhere('id',$class_room_id))->name }}"
                                   disabled>
                        </div>

                    @else
                        {{-- CREATE MANUAL --}}
                        <div class="form-group">
                            <label>Kelas</label>
                            <select name="class_room_id" class="form-control" required>
                                <option value="">-- Pilih Kelas --</option>
                                @foreach($classes as $class)
                                    <option value="{{ $class->id }}"
                                        {{ old('class_room_id') == $class->id ? 'selected' : '' }}>
                                        {{ $class->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('class_room_id')
                                <div class="text-danger mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                    @endif
                </div>

            </div>
        </div>

        {{-- FOOTER --}}
        <div class="card-footer d-flex justify-content-between">
            <a href="{{ url()->previous() }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Batal
            </a>

            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i>
                {{ $isEdit ? 'Simpan Perubahan' : 'Simpan Siswa' }}
            </button>
        </div>

    </form>

</div>

</div>
</section>
@endsection
