@extends('layouts.master')

@section('content')

{{-- ================= CONTENT HEADER ================= --}}
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2 align-items-center">
            <div class="col-sm-6">
                <h1 class="m-0 font-weight-bold">Dashboard</h1>
                <small class="text-muted">Ringkasan absensi hari ini</small>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item">
                        <a href="{{ route('dashboard') }}">Home</a>
                    </li>
                    <li class="breadcrumb-item active">Dashboard</li>
                </ol>
            </div>
        </div>
    </div>
</div>

{{-- ================= MAIN CONTENT ================= --}}
<section class="content">
    <div class="container-fluid">

        {{-- ===== ABSENSI SUMMARY ===== --}}
        <div class="row">

            {{-- Total Hadir --}}
            <div class="col-lg-3 col-md-6 col-12">
                <div class="small-box bg-success">
                    <div class="inner">
                        <h3>{{ $totalHadir }}</h3>
                        <p>Total Hadir</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-user-check"></i>
                    </div>
                </div>
            </div>

            {{-- Total Terlambat --}}
            <div class="col-lg-3 col-md-6 col-12">
                <div class="small-box bg-warning">
                    <div class="inner">
                        <h3>{{ $totalTerlambat }}</h3>
                        <p>Total Terlambat</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-user-clock"></i>
                    </div>
                </div>
            </div>

            {{-- Belum Absen --}}
            <div class="col-lg-3 col-md-6 col-12">
                <div class="small-box bg-danger">
                    <div class="inner">
                        <h3>{{ $belumAbsen }}</h3>
                        <p>Belum Absen</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-user-times"></i>
                    </div>
                </div>
            </div>

            {{-- Total Siswa --}}
            <div class="col-lg-3 col-md-6 col-12">
                <div class="small-box bg-info">
                    <div class="inner">
                        <h3>{{ $totalSiswa }}</h3>
                        <p>Total Siswa</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-users"></i>
                    </div>
                </div>
            </div>

        </div>

        {{-- ===== DATA SEKOLAH ===== --}}
        <div class="row mt-3">

            {{-- Total Kelas --}}
            <div class="col-lg-3 col-md-6 col-12">
                <div class="small-box bg-secondary">
                    <div class="inner">
                        <h3>{{ $totalKelas }}</h3>
                        <p>Total Kelas</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-school"></i>
                    </div>
                </div>
            </div>

        </div>

    </div>
</section>
@endsection
