@extends('layouts.master')

@push('style')
<link rel="stylesheet" href="/admin/plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
<link rel="stylesheet" href="/admin/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
<link rel="stylesheet" href="/admin/plugins/datatables-buttons/css/buttons.bootstrap4.min.css">
@endpush

@section('content')
@php
    if (isset($teacher)) {
        $actionUrl = route('teacher.update', $teacher->id);
    } else {
        $actionUrl = route('teacher.store');
    }
@endphp

<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Guru</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="/dashboard">Home</a></li>
                    <li class="breadcrumb-item active">Guru</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<section class="content">
  <div class="container-fluid">
    <div class="row">
      <div class="col-md-12">

      <div class="card card-secondary">
        <div class="card-header">
            <h3 class="card-title">{{ isset($teacher) ? 'Edit Guru' : 'Tambah Guru' }}</h3>
        </div>

        <form method="POST" action="{{ $actionUrl }}">
            @csrf
            @if(isset($teacher))
                @method('PUT')
                <input type="hidden" name="user_id" value="{{ $teacher->user_id }}">
            @endif

            <div class="card-body">

                <div class="form-group">
                    <label>Nama Guru</label>
                    <input type="text" name="name" class="form-control" required
                          value="{{ isset($teacher) ? $teacher->name : old('name') }}">
                    @error('name') <div class="text-danger mt-1">{{ $message }}</div> @enderror
                </div>

                <div class="form-group">
                    <label>Email Login</label>
                    <input type="email" name="email" class="form-control" required
                          value="{{ isset($teacher) ? $teacher->email : old('email') }}">
                    @error('email') <div class="text-danger mt-1">{{ $message }}</div> @enderror
                </div>

                <div class="form-group">
                    <label>No. HP</label>
                    <input type="text" name="phone" class="form-control"
                          value="{{ isset($teacher) ? $teacher->phone : old('phone') }}">
                </div>

                @if(isset($teacher))
                <div class="form-group">
                    <label>Password Baru (opsional)</label>
                    <input type="password" name="password" class="form-control" placeholder="Kosongkan jika tidak ingin mengubah">
                    @error('password') <div class="text-danger mt-1">{{ $message }}</div> @enderror
                </div>

                <div class="form-group">
                    <label>Konfirmasi Password</label>
                    <input type="password" name="password_confirmation" class="form-control">
                </div>
                @endif

                @if(!isset($teacher))
                <div class="alert alert-info">
                    Password default akan dibuat otomatis: <strong>teacher123</strong>
                </div>
                @endif



            </div>

            <div class="card-footer">
                <button type="submit" class="btn btn-info btn-sm">Simpan</button>
                <a href="{{ route('teacher.manage') }}" class="btn btn-secondary btn-sm">Batal</a>
            </div>

        </form>
      </div>

      </div>
    </div>
  </div>
</section>
@endsection
