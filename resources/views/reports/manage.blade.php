@extends('layouts.master')

@push('style')
<link rel="stylesheet" href="/admin/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
@endpush

@section('content')

<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Laporan Absensi</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="/dashboard">Home</a></li>
                    <li class="breadcrumb-item active">Reports</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<section class="content">
<div class="container-fluid">

    {{-- Filter --}}
    <div class="card mb-3">
    <div class="card-body">

        <div class="row g-2 align-items-end">

            <div class="col-auto">
                <label class="small text-muted">Kelas</label>
                <select id="filter-class" class="form-control">
                    <option value="">Semua Kelas</option>
                    @foreach($classes as $class)
                        <option value="{{ $class->id }}">{{ $class->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-auto">
                <label class="small text-muted">Tanggal</label>
                <input type="date" id="filter-date" class="form-control">
            </div>

            <div class="col-auto">
                <label class="small text-muted">Bulan</label>
                <input type="month" id="filter-month" class="form-control">
            </div>

            <div class="col-auto d-flex gap-5 mt-3 mt-md-0">
                {{-- <button id="filter-btn" class="btn btn-primary">
                    <i class="fa fa-filter"></i> Filter
                </button> --}}

                <a href="#" id="export-excel" class="btn btn-success">
                    <i class="fa fa-file-excel"></i> Excel
                </a>

                <a href="#" id="export-pdf" class="btn btn-danger">
                    <i class="fa fa-file-pdf"></i> PDF
                </a>
            </div>

        </div>

    </div>
</div>


    {{-- Preview Table --}}
    <div class="card">
        <div class="card-body">
            <table id="reportTable" class="table table-bordered table-striped">
                <thead>
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
</section>
@endsection

@push('script')
<script src="/admin/plugins/datatables/jquery.dataTables.min.js"></script>
<script src="/admin/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>

<script>
$(document).ready(function () {

    let table = $('#reportTable').DataTable({
        processing: true,
        serverSide: false,
        ajax: {
            url: '{{ route("reports.data") }}',
            data: function (d) {
                d.class_room_id = $('#filter-class').val();
                d.date = $('#filter-date').val();
                d.month = $('#filter-month').val();
            }
        },
        columns: [
            { data: 'date', name: 'date' },
            { data: 'siswa_name', name: 'siswa_name' },
            { data: 'class_name', name: 'class_name' },
            { data: 'time_in', name: 'time_in' },
            { data: 'status', name: 'status' },
        ]
    });

    // Auto reload when filter changes
    $('#filter-class, #filter-date, #filter-month').on('change input', function () {
        table.ajax.reload();
    });

    function buildParams() {
        return $.param({
            class_room_id: $('#filter-class').val(),
            date: $('#filter-date').val(),
            month: $('#filter-month').val()
        });
    }

    $('#export-excel').on('click', function (e) {
        e.preventDefault();
        window.location.href = '/reports/export/excel?' + buildParams();
    });

    $('#export-pdf').on('click', function (e) {
        e.preventDefault();
        window.location.href = '/reports/export/pdf?' + buildParams();
    });

});
</script>

@endpush
