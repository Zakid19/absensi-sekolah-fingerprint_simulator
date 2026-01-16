@extends('layouts.master')

@push('style')
<link rel="stylesheet" href="/admin/plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
<link rel="stylesheet" href="/admin/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
<link rel="stylesheet" href="/admin/plugins/datatables-buttons/css/buttons.bootstrap4.min.css">
@endpush

@section('content')
@php
    $actionUrl = isset($student)
        ? route('fingerprint.update', $student->id)
        : route('fingerprint.store');
@endphp

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

@if(session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
@endif

<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Scan Fingerprint</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="/dashboard">Home</a></li>
                    <li class="breadcrumb-item active">Fingerprint</li>
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
                        <h3 class="card-title">Set Fingerprint Siswa</h3>
                    </div>

                    <form method="POST" action="{{ $actionUrl }}" id="fingerprintForm">
                        @csrf
                        @isset($student)
                            @method('PUT')
                        @endisset

                        <div class="card-body">
                            <div class="form-group">
                                <label for="fingerprint_id">Fingerprint ID</label>
                                <input
                                    type="text"
                                    required
                                    class="form-control"
                                    name="fingerprint_id"
                                    id="fingerprint_id"
                                    placeholder="Scan jari di device..."
                                    value="{{ old('fingerprint_id', $student->fingerprint_id ?? '') }}"
                                >
                                @error('fingerprint_id')
                                    <div class="mt-2 text-danger">{{ $message }}</div>
                                @enderror
                            </div>

                            @isset($student->fingerprint_id)
                                <div class="alert alert-info mt-2">
                                    Fingerprint saat ini: <strong>{{ $student->fingerprint_id }}</strong>
                                </div>
                            @endisset

                            <div id="realtimeStatus" class="alert alert-secondary mt-2 d-none"></div>
                        </div>

                        <div class="card-footer d-flex gap-2">
                            <button type="submit" class="btn btn-info btn-sm">
                                Simpan
                            </button>

                            @isset($student->fingerprint_id)
                                <form action="{{ route('fingerprint.delete', $student->id) }}" method="POST" onsubmit="return confirm('Yakin mau clear fingerprint?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm">
                                        Hapus Fingerprint
                                    </button>
                                </form>
                            @endisset
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@push('script')
<script src="https://js.pusher.com/7.2/pusher.min.js"></script>
<script>
    const studentId = {{ $student->id ?? 'null' }};

    if(studentId){
        const pusher = new Pusher('{{ config("broadcasting.connections.pusher.key") }}', {
            cluster: '{{ config("broadcasting.connections.pusher.options.cluster") }}',
            forceTLS: true
        });

        const channel = pusher.subscribe('fingerprint');
        channel.bind('FingerprintSynced', function(data) {
            if (data.student_id == studentId) {
                const input = document.getElementById('fingerprint_id');
                const statusBox = document.getElementById('realtimeStatus');

                input.value = data.fingerprint_id;

                statusBox.classList.remove('d-none', 'alert-secondary');
                statusBox.classList.add('alert-success');
                statusBox.innerHTML = 'Fingerprint berhasil diterima dari device. Silakan simpan.';
            }
        });
    }
</script>
@endpush
