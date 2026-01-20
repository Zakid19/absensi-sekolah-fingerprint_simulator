<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Register | {{ config('app.name') }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    {{-- AdminLTE CSS --}}
    <link rel="stylesheet" href="{{ asset('admin/plugins/fontawesome-free/css/all.min.css') }}">
    <link rel="stylesheet" href="{{ asset('admin/dist/css/adminlte.min.css') }}">
</head>

<body class="hold-transition register-page">

<div class="register-box">

    <div class="card card-outline card-primary">

        {{-- HEADER --}}
        <div class="card-header text-center">
            <img src="{{ asset('images/mandiri-bersemi.png') }}"
                 alt="Logo Sekolah"
                 style="width:80px; margin-bottom:10px;">

            <h4 class="mb-0 font-weight-bold">
                {{ config('app.school_name') }}
            </h4>

            <small class="text-muted">Sistem Absensi Sekolah</small>
        </div>

        {{-- BODY --}}
        <div class="card-body">
            <p class="login-box-msg">Silakan daftar untuk melanjutkan</p>

            <form action="{{ route('register') }}" method="POST">
                @csrf

                {{-- Nama --}}
                <div class="input-group mb-3">
                    <input type="text"
                           name="name"
                           class="form-control @error('name') is-invalid @enderror"
                           placeholder="Nama Lengkap"
                           value="{{ old('name') }}"
                           required autofocus>
                    <div class="input-group-append">
                        <div class="input-group-text">
                            <span class="fas fa-user"></span>
                        </div>
                    </div>
                    @error('name')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                {{-- Email --}}
                <div class="input-group mb-3">
                    <input type="email"
                           name="email"
                           class="form-control @error('email') is-invalid @enderror"
                           placeholder="Email"
                           value="{{ old('email') }}"
                           required>
                    <div class="input-group-append">
                        <div class="input-group-text">
                            <span class="fas fa-envelope"></span>
                        </div>
                    </div>
                    @error('email')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                {{-- Password --}}
                <div class="input-group mb-3">
                    <input type="password"
                           name="password"
                           class="form-control @error('password') is-invalid @enderror"
                           placeholder="Password"
                           required>
                    <div class="input-group-append">
                        <div class="input-group-text">
                            <span class="fas fa-lock"></span>
                        </div>
                    </div>
                    @error('password')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                {{-- Konfirmasi Password --}}
                <div class="input-group mb-3">
                    <input type="password"
                           name="password_confirmation"
                           class="form-control"
                           placeholder="Konfirmasi Password"
                           required>
                    <div class="input-group-append">
                        <div class="input-group-text">
                            <span class="fas fa-lock"></span>
                        </div>
                    </div>
                </div>

                {{-- ACTION --}}
                <div class="row">
                    <div class="col-8">
                        <a href="{{ route('login') }}" class="text-sm">
                            Sudah punya akun?
                        </a>
                    </div>
                    <div class="col-4">
                        <button type="submit" class="btn btn-primary btn-block">
                            Daftar
                        </button>
                    </div>
                </div>
            </form>
        </div>
        <!-- /.card-body -->

    </div>
    <!-- /.card -->

</div>
<!-- /.register-box -->

{{-- AdminLTE JS --}}
<script src="{{ asset('admin/plugins/jquery/jquery.min.js') }}"></script>
<script src="{{ asset('admin/plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('admin/dist/js/adminlte.min.js') }}"></script>

</body>
</html>
