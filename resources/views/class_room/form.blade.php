@extends('layouts.master')

@section('content')
@php
    $isEdit = isset($class);
    $actionUrl = $isEdit
        ? route('class.update', $class->id)
        : route('class.store');
@endphp

{{-- ================= HEADER ================= --}}
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2 align-items-center">
            <div class="col-sm-7">
                <h1 class="m-0 font-weight-bold">
                    {{ $isEdit ? 'Edit Kelas' : 'Tambah Kelas' }}
                </h1>
                <small class="text-muted">
                    {{ $isEdit
                        ? 'Perbarui informasi kelas'
                        : 'Buat kelas baru untuk siswa' }}
                </small>
            </div>
            <div class="col-sm-5">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item">
                        <a href="{{ route('dashboard') }}">Home</a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="{{ route('class.manage') }}">Kelas</a>
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

<div class="col-md-6">

<div class="card card-primary card-outline">

    {{-- Card Header --}}
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-school mr-1"></i>
            Informasi Kelas
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
                Kelas digunakan untuk mengelompokkan siswa dan keperluan absensi.
            </div>

            {{-- NAMA KELAS --}}
            <div class="form-group">
                <label>Nama Kelas</label>
                <input type="text"
                       name="name"
                       class="form-control"
                       placeholder="Contoh: X IPA 1 / XI RPL A"
                       required
                       value="{{ old('name', $class->name ?? '') }}">
                @error('name')
                    <div class="text-danger mt-1">{{ $message }}</div>
                @enderror
            </div>

        </div>

        {{-- FOOTER --}}
        <div class="card-footer d-flex justify-content-between">
            <a href="{{ route('class.manage') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Batal
            </a>

            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i>
                {{ $isEdit ? 'Simpan Perubahan' : 'Simpan Kelas' }}
            </button>
        </div>

    </form>

</div>

</div>
</div>
</div>
</section>
@endsection
