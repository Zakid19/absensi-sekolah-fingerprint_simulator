@extends('layouts.master')

@section('content')

{{-- ================= HEADER ================= --}}
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2 align-items-center">
            <div class="col-sm-7">
                <h1 class="m-0 font-weight-bold">Profil Akun</h1>
                <small class="text-muted">
                    Kelola informasi akun dan keamanan login
                </small>
            </div>
            <div class="col-sm-5">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item">
                        <a href="{{ route('dashboard') }}">Beranda</a>
                    </li>
                    <li class="breadcrumb-item active">Profil</li>
                </ol>
            </div>
        </div>
    </div>
</div>

{{-- ================= CONTENT ================= --}}
<section class="content">
<div class="container-fluid">

    <div class="row">

        {{-- INFORMASI PROFIL --}}
        <div class="col-md-6">
            <div class="card card-primary card-outline">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-user mr-1"></i>
                        Informasi Profil
                    </h3>
                </div>

                <div class="card-body">
                    <p class="text-muted small">
                        Perbarui nama dan alamat email yang digunakan untuk login.
                    </p>

                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>
        </div>

        {{-- GANTI PASSWORD --}}
        <div class="col-md-6">
            <div class="card card-warning card-outline">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-key mr-1"></i>
                        Ganti Password
                    </h3>
                </div>

                <div class="card-body">
                    <p class="text-muted small">
                        Gunakan password yang kuat untuk menjaga keamanan akun.
                    </p>

                    @include('profile.partials.update-password-form')
                </div>
            </div>
        </div>

    </div>

    {{-- HAPUS AKUN --}}
    <div class="row">
        <div class="col-md-12">
            <div class="card card-danger card-outline">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-exclamation-triangle mr-1"></i>
                        Hapus Akun
                    </h3>
                </div>

                <div class="card-body">
                    <div class="alert alert-danger">
                        <strong>Perhatian!</strong><br>
                        Tindakan ini akan menghapus akun secara permanen dan tidak dapat dibatalkan.
                    </div>

                    @include('profile.partials.delete-user-form')
                </div>
            </div>
        </div>
    </div>

</div>
</section>
@endsection
