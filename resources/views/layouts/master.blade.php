<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>
        Absensi {{ config('app.school_name') ?? config('app.name') }}
    </title>

    <meta name="description" content="Sistem Absensi Fingerprint">
    <meta name="author" content="Admin">

    <link rel="icon" type="image/png" href="/admin/dist/img/logo.png">

    {{-- Google Font --}}
    <link rel="stylesheet"
          href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">

    {{-- Font Awesome --}}
    <link rel="stylesheet" href="/admin/plugins/fontawesome-free/css/all.min.css">

    {{-- Ionicons --}}
    <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">

    {{-- AdminLTE Core --}}
    <link rel="stylesheet" href="/admin/dist/css/adminlte.min.css">

    {{-- Plugins --}}
    <link rel="stylesheet" href="/admin/plugins/icheck-bootstrap/icheck-bootstrap.min.css">
    <link rel="stylesheet" href="/admin/plugins/overlayScrollbars/css/OverlayScrollbars.min.css">
    <link rel="stylesheet" href="/admin/plugins/daterangepicker/daterangepicker.css">
    <link rel="stylesheet" href="/admin/plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css">
    <link rel="stylesheet" href="/admin/plugins/summernote/summernote-bs5.min.css">
    <link rel="stylesheet" href="/admin/plugins/jqvmap/jqvmap.min.css">

    {{-- Custom page styles --}}
    @stack('style')
</head>

<body class="hold-transition sidebar-mini layout-fixed">

<div class="wrapper">

    {{-- PRELOADER --}}
    <div class="preloader flex-column justify-content-center align-items-center">
        <img class="animation__shake"
             src="/admin/dist/img/AdminLTELogo.png"
             alt="Loading"
             height="60"
             width="60">
    </div>

    {{-- NAVBAR --}}
    @include('layouts.partial.navbar')

    {{-- SIDEBAR --}}
    @include('layouts.partial.sidebar')

    {{-- CONTENT --}}
    <div class="content-wrapper">
        @yield('content')
    </div>

    {{-- FOOTER --}}
    <footer class="main-footer">
        <strong>
            &copy; {{ date('Y') }} {{ config('app.school_name') ?? 'Sekolah' }}
        </strong>
        <span class="ml-1">All rights reserved.</span>

        <div class="float-right d-none d-sm-inline-block">
            <b>Version</b> 1.0.0
        </div>
    </footer>

    {{-- CONTROL SIDEBAR --}}
    <aside class="control-sidebar control-sidebar-dark"></aside>

</div>

{{-- ================= JS CORE ================= --}}

<script src="/admin/plugins/jquery/jquery.min.js"></script>
<script src="/admin/plugins/jquery-ui/jquery-ui.min.js"></script>

<script>
    $.widget.bridge('uibutton', $.ui.button)
</script>

<script src="/admin/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="/admin/plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js"></script>

{{-- ================= PLUGINS ================= --}}

<script src="/admin/plugins/chart.js/Chart.min.js"></script>
<script src="/admin/plugins/sparklines/sparkline.js"></script>
<script src="/admin/plugins/jquery-knob/jquery.knob.min.js"></script>
<script src="/admin/plugins/moment/moment.min.js"></script>
<script src="/admin/plugins/daterangepicker/daterangepicker.js"></script>
<script src="/admin/plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js"></script>
<script src="/admin/plugins/jqvmap/jquery.vmap.min.js"></script>
<script src="/admin/plugins/jqvmap/maps/jquery.vmap.usa.js"></script>
<script src="/admin/plugins/summernote/summernote-bs5.min.js"></script>

{{-- ================= ADMINLTE ================= --}}

<script src="/admin/dist/js/adminlte.js"></script>

{{-- OPTIONAL DASHBOARD (AMAN) --}}
<script src="/admin/dist/js/pages/dashboard.js"></script>

{{-- GLOBAL COMPONENTS --}}
@include('components.alert')
@include('components.custom')

{{-- PAGE SCRIPTS --}}
@stack('script')

{{-- ================= GLOBAL HELPERS ================= --}}
<script>
    // Global Summernote default (optional)
    if ($('#summernote').length) {
        $('#summernote').summernote({
            placeholder: 'Isi Konten',
            tabsize: 2,
            height: 120,
            toolbar: [
                ['style', ['style']],
                ['font', ['bold', 'underline', 'clear']],
                ['color', ['color']],
                ['para', ['ul', 'ol', 'paragraph']],
                ['table', ['table']],
                ['insert', ['link', 'picture', 'video']],
                ['view', ['fullscreen', 'codeview']]
            ]
        });
    }
</script>

</body>
</html>
