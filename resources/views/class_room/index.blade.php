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
            <div class="col-sm-6">
                <h1 class="m-0 font-weight-bold">Pilih Kelas</h1>
                <small class="text-muted">
                    Pilih kelas untuk melihat daftar siswa
                </small>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item">
                        <a href="{{ route('dashboard') }}">Home</a>
                    </li>
                    <li class="breadcrumb-item active">Kelas</li>
                </ol>
            </div>
        </div>
    </div>
</div>

{{-- ================= CONTENT ================= --}}
<section class="content">
    <div class="container-fluid">

        <div class="row">
            <div class="col-12">

                <div class="card card-primary card-outline">

                    {{-- Card Header --}}
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <h3 class="card-title">
                                <i class="fas fa-school mr-1"></i>
                                Daftar Kelas
                            </h3>

                            <div>
                                <a href="{{ route('class.create') }}" class="btn btn-sm btn-primary">
                                    <i class="fas fa-plus"></i> Tambah Kelas
                                </a>
                                <a href="{{ route('class.manage') }}" class="btn btn-sm btn-secondary">
                                    <i class="fas fa-cog"></i> Manage
                                </a>
                            </div>
                        </div>
                    </div>

                    {{-- Card Body --}}
                    <div class="card-body">

                        <div class="table-responsive">
                            <table id="tableClass"
                                   class="table table-bordered table-hover">

                                <thead class="thead-light">
                                    <tr>
                                        <th width="50">No</th>
                                        <th>Nama Kelas</th>
                                        <th width="150" class="text-center">Jumlah Siswa</th>
                                        <th width="160" class="text-center">Aksi</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    @foreach ($classes as $i => $class)
                                        <tr>
                                            <td>{{ $i + 1 }}</td>
                                            <td>
                                                <strong>{{ $class->name }}</strong>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge badge-info">
                                                    {{ $class->students_count }} Siswa
                                                </span>
                                            </td>
                                            <td class="text-center">
                                                <a href="{{ route('class.show', $class->id) }}"
                                                   class="btn btn-sm btn-success">
                                                    <i class="fas fa-users"></i>
                                                    Lihat Siswa
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>

                            </table>
                        </div>

                    </div>
                    {{-- /.card-body --}}
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
        $('#tableClass').DataTable({
            paging: true,
            searching: true,
            ordering: true,
            lengthChange: false,
            info: true,
            autoWidth: false
        });
    });
</script>
@endpush
