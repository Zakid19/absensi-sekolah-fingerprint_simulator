@extends('layouts.master')

@section('content')
<!-- Content Header (Page header) -->
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Backup Database</h1>
            </div><!-- /.col -->
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="/dashboard">Home</a></li>
                    <li class="breadcrumb-item active">Backup Database</li>
                </ol>
            </div><!-- /.col -->
        </div><!-- /.row -->
    </div><!-- /.container-fluid -->
</div>
<!-- /.content-header -->
<div class="container">

    <p>Backup terakhir: {{ $lastBackup ? basename($lastBackup) : 'Belum ada' }}</p>
    <p>Total file: {{ $count }}</p>

    <form method="POST" action="{{ route('backup.run') }}">
        @csrf
        <button class="btn btn-primary">Backup Sekarang</button>
    </form>

    <hr>

    <h5>File Backup</h5>
    <ul>
        @forelse($files as $file)
            <li>
                {{ basename($file) }}
                <a href="{{ route('backup.download', basename($file)) }}" class="btn btn-sm btn-outline-secondary">
                    Download
                </a>
            </li>
        @empty
            <li>Belum ada file backup.</li>
        @endforelse
    </ul>

    <hr>

    <h5>Restore Database</h5>
    <form method="POST" action="{{ route('backup.restore') }}" enctype="multipart/form-data">
        @csrf
        <input type="file" name="backup_file" required>
        <button class="btn btn-danger mt-2">Restore</button>
    </form>
</div>
<!-- /.content -->
@endsection
