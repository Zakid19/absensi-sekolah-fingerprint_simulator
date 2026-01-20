@extends('layouts.master')

@section('content')

{{-- ================= HEADER ================= --}}
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2 align-items-center">
            <div class="col-sm-7">
                <h1 class="m-0 font-weight-bold">System Backup</h1>
                <small class="text-muted">
                    Kelola backup & restore database aplikasi
                </small>
            </div>
            <div class="col-sm-5">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item">
                        <a href="{{ route('dashboard') }}">Home</a>
                    </li>
                    <li class="breadcrumb-item active">Backup</li>
                </ol>
            </div>
        </div>
    </div>
</div>

{{-- ================= CONTENT ================= --}}
<section class="content">
<div class="container-fluid">

    {{-- INFO --}}
    <div class="row">
        <div class="col-md-4">
            <div class="info-box">
                <span class="info-box-icon bg-info">
                    <i class="fas fa-database"></i>
                </span>
                <div class="info-box-content">
                    <span class="info-box-text">Backup Terakhir</span>
                    <span class="info-box-number">
                        {{ $lastBackup ? basename($lastBackup) : 'Belum ada' }}
                    </span>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="info-box">
                <span class="info-box-icon bg-success">
                    <i class="fas fa-file-archive"></i>
                </span>
                <div class="info-box-content">
                    <span class="info-box-text">Total File Backup</span>
                    <span class="info-box-number">{{ $count }}</span>
                </div>
            </div>
        </div>
    </div>

    {{-- BACKUP ACTION --}}
    <div class="card card-primary card-outline">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-cloud-upload-alt mr-1"></i>
                Backup Database
            </h3>
        </div>

        <div class="card-body">
            <p class="text-muted">
                Backup akan menyimpan seluruh data aplikasi ke dalam file.
            </p>

            <form method="POST" action="{{ route('backup.run') }}">
                @csrf
                <button class="btn btn-primary">
                    <i class="fas fa-play mr-1"></i>
                    Backup Sekarang
                </button>
            </form>
        </div>
    </div>

    {{-- FILE LIST --}}
    <div class="card card-secondary card-outline">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-folder-open mr-1"></i>
                Daftar File Backup
            </h3>
        </div>

        <div class="card-body p-0">
            @if(count($files))
                <table class="table table-striped mb-0">
                    <thead>
                        <tr>
                            <th>Nama File</th>
                            <th width="120" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($files as $file)
                            <tr>
                                <td>{{ basename($file) }}</td>
                                <td class="text-center">
                                    <a href="{{ route('backup.download', basename($file)) }}"
                                       class="btn btn-sm btn-outline-secondary">
                                        <i class="fas fa-download"></i>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <div class="p-3 text-muted">
                    Belum ada file backup.
                </div>
            @endif
        </div>
    </div>

    {{-- RESTORE --}}
    <div class="card card-danger card-outline">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-exclamation-triangle mr-1"></i>
                Restore Database
            </h3>
        </div>

        <div class="card-body">
            <div class="alert alert-warning">
                <strong>Perhatian!</strong>
                Restore akan <b>menggantikan seluruh data</b> dengan file backup.
            </div>

            <form method="POST"
                  action="{{ route('backup.restore') }}"
                  enctype="multipart/form-data">
                @csrf

                <div class="form-group">
                    <label>Pilih File Backup</label>
                    <input type="file"
                           name="backup_file"
                           class="form-control"
                           required>
                </div>

                <button class="btn btn-danger">
                    <i class="fas fa-undo mr-1"></i>
                    Restore Database
                </button>
            </form>
        </div>
    </div>

</div>
</section>
@endsection
