@extends('layouts.master')

@section('content')
@php
    $isEdit = isset($teacher);
    $actionUrl = $isEdit
        ? route('teacher.update', $teacher->id)
        : route('teacher.store');
@endphp

{{-- ================= HEADER ================= --}}
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2 align-items-center">
            <div class="col-sm-7">
                <h1 class="m-0 font-weight-bold">
                    {{ $isEdit ? 'Edit Guru' : 'Tambah Guru' }}
                </h1>
                <small class="text-muted">
                    {{ $isEdit
                        ? 'Perbarui data guru dan akun login'
                        : 'Menambahkan guru baru beserta akun login' }}
                </small>
            </div>
            <div class="col-sm-5">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item">
                        <a href="{{ route('dashboard') }}">Home</a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="{{ route('teacher.manage') }}">Guru</a>
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
<div class="row justify-content-center">

<div class="col-md-8">

<div class="card card-primary card-outline">

    {{-- Card Header --}}
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-chalkboard-teacher mr-1"></i>
            Informasi Guru
        </h3>
    </div>

    <form method="POST" action="{{ $actionUrl }}">
        @csrf
        @if($isEdit)
            @method('PUT')
            <input type="hidden" name="user_id" value="{{ $teacher->user_id }}">
        @endif

        <div class="card-body">

            {{-- INFO --}}
            <div class="alert alert-info small">
                <i class="fas fa-info-circle mr-1"></i>
                Guru akan memiliki akun login dengan role <b>Teacher</b>.
            </div>

            {{-- NAMA --}}
            <div class="form-group">
                <label>Nama Guru</label>
                <input type="text"
                       name="name"
                       class="form-control"
                       placeholder="Nama lengkap guru"
                       required
                       value="{{ old('name', $teacher->name ?? '') }}">
                @error('name')
                    <div class="text-danger mt-1">{{ $message }}</div>
                @enderror
            </div>

            {{-- EMAIL --}}
            <div class="form-group">
                <label>Email Login</label>
                <input type="email"
                       name="email"
                       class="form-control"
                       placeholder="Email untuk login"
                       required
                       value="{{ old('email', $teacher->email ?? '') }}">
                <small class="text-muted">
                    Email digunakan sebagai username login.
                </small>
                @error('email')
                    <div class="text-danger mt-1">{{ $message }}</div>
                @enderror
            </div>

            {{-- PHONE --}}
            <div class="form-group">
                <label>No. HP (Opsional)</label>
                <input type="text"
                       name="phone"
                       class="form-control"
                       placeholder="Contoh: 08xxxxxxxxxx"
                       value="{{ old('phone', $teacher->phone ?? '') }}">
            </div>

            {{-- PASSWORD --}}
            @if($isEdit)
                <hr>
                <div class="form-group">
                    <label>Password Baru</label>
                    <input type="password"
                           name="password"
                           class="form-control"
                           placeholder="Kosongkan jika tidak ingin mengubah">
                    <small class="text-muted">
                        Isi hanya jika ingin mengganti password guru.
                    </small>
                    @error('password')
                        <div class="text-danger mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label>Konfirmasi Password</label>
                    <input type="password"
                           name="password_confirmation"
                           class="form-control">
                </div>
            @else
                <div class="alert alert-warning small">
                    <i class="fas fa-key mr-1"></i>
                    Password awal akan dibuat otomatis:
                    <strong>teacher123</strong>
                </div>
            @endif

        </div>

        {{-- FOOTER --}}
        <div class="card-footer d-flex justify-content-between">
            <a href="{{ route('teacher.manage') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Batal
            </a>

            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> Simpan
            </button>
        </div>

    </form>

</div>

</div>
</div>
</div>
</section>
@endsection
