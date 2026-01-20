@extends('layouts.master')

@push('style')
<link rel="stylesheet" href="/admin/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
@endpush

@section('content')

{{-- ================= HEADER ================= --}}
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2 align-items-center">
            <div class="col-sm-7">
                <h1 class="m-0 font-weight-bold">Laporan Absensi</h1>
                <small class="text-muted">
                    Rekap kehadiran siswa berdasarkan kelas dan periode
                </small>
            </div>
            <div class="col-sm-5">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item">
                        <a href="{{ route('dashboard') }}">Home</a>
                    </li>
                    <li class="breadcrumb-item active">Laporan</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<section class="content">
<div class="container-fluid">

    {{-- ================= FILTER ================= --}}
    <div class="card mb-3">
        <div class="card-body">
            <div class="row align-items-end">

                <div class="col-md-3 col-sm-6 mb-2">
                    <label class="small text-muted">Kelas</label>
                    <select id="filter-class" class="form-control">
                        <option value="">Semua Kelas</option>
                        @foreach($classes as $class)
                            <option value="{{ $class->id }}">{{ $class->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3 col-sm-6 mb-2">
                    <label class="small text-muted">Tanggal (Harian)</label>
                    <input type="date" id="filter-date" class="form-control">
                </div>

                <div class="col-md-3 col-sm-6 mb-2">
                    <label class="small text-muted">Bulan (Bulanan)</label>
                    <input type="month" id="filter-month" class="form-control">
                </div>

                <div class="col-md-3 col-sm-12 mb-2 d-flex align-items-end">
                    <div class="ml-md-auto">
                        <a href="#" id="export-excel" class="btn btn-success btn-sm mr-2">
                            <i class="fas fa-file-excel"></i> Export Excel
                        </a>

                        <a href="#" id="export-pdf" class="btn btn-danger btn-sm">
                            <i class="fas fa-file-pdf"></i> Export PDF
                        </a>
                    </div>
                </div>

            </div>

            <div class="mt-2 small text-muted">
                <i class="fas fa-info-circle"></i>
                Gunakan <b>tanggal</b> untuk laporan harian, atau <b>bulan</b> untuk laporan bulanan.
            </div>

        </div>
    </div>

    {{-- ================= TABLE ================= --}}
    <div class="card card-primary card-outline">
        <div class="card-body">
            <div class="table-responsive">
                <table id="reportTable"
                       class="table table-bordered table-hover w-100">
                    <thead class="thead-light">
                        <tr>
                            <th>Tanggal</th>
                            <th>Nama Siswa</th>
                            <th>Kelas</th>
                            <th>Jam Masuk</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>

</div>
</section>
@endsection

@push('script')
<script src="/admin/plugins/datatables/jquery.dataTables.min.js"></script>
<script src="/admin/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>

<script>
$(function () {

    /* ================= DATATABLE ================= */
    const table = $('#reportTable').DataTable({
        processing: true,
        serverSide: false,
        paging: true,
        ordering: true,
        searching: false,
        autoWidth: false,
        language: {
            url: 'https://cdn.datatables.net/plug-ins/1.13.4/i18n/id.json'
        },

        ajax: {
            url: '{{ route("reports.data") }}',
            data: function (d) {
                d.class_room_id = $('#filter-class').val();
                d.date  = $('#filter-date').val();
                d.month = $('#filter-month').val();
            }
        },

        columns: [
            { data: 'date',       name: 'date' },
            { data: 'siswa_name', name: 'siswa_name' },
            { data: 'class_name', name: 'class_name' },
            { data: 'time_in',    name: 'time_in' },
            { data: 'status',     name: 'status' },
        ]
    });

    /* ================= FILTER LOGIC ================= */

    // Jika pilih tanggal → reset bulan
    $('#filter-date').on('change', function () {
        if (this.value) {
            $('#filter-month').val('');
        }
        table.ajax.reload();
    });

    // Jika pilih bulan → reset tanggal
    $('#filter-month').on('change', function () {
        if (this.value) {
            $('#filter-date').val('');
        }
        table.ajax.reload();
    });

    $('#filter-class').on('change', function () {
        table.ajax.reload();
    });

    /* ================= EXPORT ================= */
    function buildParams() {
        return $.param({
            class_room_id : $('#filter-class').val(),
            date  : $('#filter-date').val(),
            month : $('#filter-month').val()
        });
    }

    $('#export-excel').on('click', function (e) {
        e.preventDefault();
        window.location.href =
            '{{ url("/reports/export/excel") }}?' + buildParams();
    });

    $('#export-pdf').on('click', function (e) {
        e.preventDefault();
        window.location.href =
            '{{ url("/reports/export/pdf") }}?' + buildParams();
    });

});
</script>
@endpush
