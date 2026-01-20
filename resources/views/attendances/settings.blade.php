@extends('layouts.master')

@section('content')
@php
    use Carbon\Carbon;

    $startTimeValue = old(
        'start_time',
        $setting->start_time
            ? Carbon::parse($setting->start_time)->format('H:i')
            : '07:00'
    );

    $lateMinutesValue = old('late_minutes', $setting->late_minutes ?? 0);

    $lateLimitPreview = Carbon::createFromFormat('H:i', $startTimeValue)
        ->addMinutes((int) $lateMinutesValue)
        ->format('H:i');
@endphp

{{-- ================= HEADER ================= --}}
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2 align-items-center">
            <div class="col-sm-7">
                <h1 class="m-0 font-weight-bold">Pengaturan Absensi</h1>
                <small class="text-muted">
                    Atur jam masuk dan aturan keterlambatan siswa
                </small>
            </div>
            <div class="col-sm-5">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item">
                        <a href="{{ route('dashboard') }}">Home</a>
                    </li>
                    <li class="breadcrumb-item active">Absensi</li>
                </ol>
            </div>
        </div>
    </div>
</div>

{{-- ================= CONTENT ================= --}}
<section class="content">
<div class="container-fluid">

    {{-- ALERT --}}
    @if(session('success'))
        <div class="alert alert-success">
            <i class="fas fa-check-circle mr-1"></i>
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="row">

        {{-- FORM --}}
        <div class="col-md-7">
            <div class="card card-primary card-outline">

                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-clock mr-1"></i>
                        Aturan Jam Masuk
                    </h3>
                </div>

                <form method="POST" action="{{ route('attendance.settings.update') }}">
                    @csrf

                    <div class="card-body">

                        {{-- JAM MASUK --}}
                        <div class="form-group">
                            <label>Jam Masuk Sekolah</label>
                            <input type="time"
                                   name="start_time"
                                   class="form-control"
                                   value="{{ $startTimeValue }}"
                                   step="60"
                                   required>

                            <small class="text-muted">
                                Contoh: 07:00
                            </small>
                        </div>

                        {{-- TOLERANSI --}}
                        <div class="form-group">
                            <label>Toleransi Keterlambatan (menit)</label>
                            <input type="number"
                                   name="late_minutes"
                                   class="form-control"
                                   min="0"
                                   max="120"
                                   value="{{ $lateMinutesValue }}"
                                   required>

                            <small class="text-muted">
                                Contoh: 10 → siswa masih dianggap hadir sampai 07:10
                            </small>
                        </div>

                        {{-- PREVIEW --}}
                        <div class="alert alert-info">
                            <h6 class="mb-2">
                                <i class="fas fa-eye mr-1"></i>
                                Preview Aturan
                            </h6>
                            <ul class="mb-0">
                                <li>Jam Masuk: <b>{{ $startTimeValue }}</b></li>
                                <li>Toleransi: <b>{{ $lateMinutesValue }} menit</b></li>
                                <li>Batas Terlambat: <b>{{ $lateLimitPreview }}</b></li>
                            </ul>
                        </div>

                    </div>

                    <div class="card-footer text-right">
                        <button class="btn btn-primary">
                            <i class="fas fa-save mr-1"></i>
                            Simpan Pengaturan
                        </button>
                    </div>

                </form>
            </div>
        </div>

        {{-- INFO --}}
        <div class="col-md-5">
            <div class="card card-secondary card-outline">

                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-info-circle mr-1"></i>
                        Catatan Penting
                    </h3>
                </div>

                <div class="card-body">
                    <ul class="pl-3">
                        <li>Aturan ini berlaku untuk <b>semua siswa</b></li>
                        <li>Perhitungan absensi mengikuti <b>waktu device fingerprint</b></li>
                        <li>Status <b>Terlambat</b> ditentukan otomatis oleh sistem</li>
                        <li>Perubahan langsung aktif tanpa restart</li>
                    </ul>
                </div>

            </div>
        </div>

    </div>

</div>
</section>
@endsection
