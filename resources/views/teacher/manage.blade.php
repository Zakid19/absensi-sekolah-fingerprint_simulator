@extends('layouts.master')

@push('style')
<link rel="stylesheet" href="/admin/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
<link rel="stylesheet" href="/admin/plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
@endpush

@section('content')

{{-- ================= HEADER ================= --}}
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2 align-items-center">
            <div class="col-sm-7">
                <h1 class="m-0 font-weight-bold">Manajemen Guru</h1>
                <small class="text-muted">
                    Kelola data guru dan akun pengguna dengan role <b>Teacher</b>
                </small>
            </div>
            <div class="col-sm-5">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item">
                        <a href="{{ route('dashboard') }}">Home</a>
                    </li>
                    <li class="breadcrumb-item active">Guru</li>
                </ol>
            </div>
        </div>
    </div>
</div>

{{-- ================= CONTENT ================= --}}
<section class="content">
    <div class="container-fluid">

        <div class="card card-primary card-outline">

            {{-- Header --}}
            <div class="card-header d-flex justify-content-between align-items-center">
                <h3 class="card-title">
                    <i class="fas fa-chalkboard-teacher mr-1"></i>
                    Daftar Guru
                </h3>

                <a href="{{ route('teacher.create') }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus"></i> Tambah Guru
                </a>
            </div>

            {{-- Body --}}
            <div class="card-body">

                <div class="alert alert-info small">
                    <i class="fas fa-info-circle mr-1"></i>
                    Guru yang ditambahkan akan otomatis memiliki akun login dengan role <b>Teacher</b>.
                </div>

                <div class="table-responsive">
                    <table id="dataTeacher"
                           class="table table-bordered table-hover w-100">

                        <thead class="thead-light">
                            <tr>
                                @foreach ($table['columns'] as $column)
                                    <th>{{ $column['label'] }}</th>
                                @endforeach
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

    const table = $('#dataTeacher').DataTable({
        language: {
            url: 'https://cdn.datatables.net/plug-ins/1.13.4/i18n/id.json'
        },
        processing: true,
        serverSide: false,
        paging: true,
        ordering: true,
        searching: true,
        autoWidth: false,

        ajax: '{{ $table['table_url'] }}',

        columns: [
            @foreach ($table['columns'] as $column)
                {
                    data: '{{ $column['name'] }}',
                    name: '{{ $column['name'] }}'
                },
            @endforeach
        ]
    });

    /* ================= DELETE CONFIRM ================= */
    $(document).on('click', '.delete-form', function (e) {
        e.preventDefault();

        const form = $(this).closest('form');

        Swal.fire({
            title: 'Hapus Data Guru?',
            text: 'Akun guru dan akses login akan ikut terhapus.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, hapus',
            cancelButtonText: 'Batal'
        }).then(result => {
            if (result.isConfirmed) {
                form.submit();
            }
        });
    });

});
</script>
@endpush
