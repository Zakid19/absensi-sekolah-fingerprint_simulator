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
            <div class="col-sm-8">
                <h1 class="m-0 font-weight-bold">
                    Registrasi Fingerprint
                </h1>
                <small class="text-muted">
                    Langkah 1: Pilih kelas siswa yang akan didaftarkan fingerprint
                </small>
            </div>
            <div class="col-sm-4">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item">
                        <a href="{{ route('dashboard') }}">Home</a>
                    </li>
                    <li class="breadcrumb-item">Simulator</li>
                    <li class="breadcrumb-item active">Fingerprint</li>
                </ol>
            </div>
        </div>
    </div>
</div>

{{-- ================= CONTENT ================= --}}
<section class="content">
    <div class="container-fluid">

        <div class="card card-primary card-outline">

            {{-- Card Header --}}
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-school mr-1"></i>
                    Daftar Kelas
                </h3>
            </div>

            {{-- Card Body --}}
            <div class="card-body">

                <div class="alert alert-info">
                    <i class="fas fa-info-circle mr-1"></i>
                    Pilih kelas untuk melihat daftar siswa dan mendaftarkan ID fingerprint.
                </div>

                <div class="table-responsive">
                    <table id="classTable"
                           class="table table-bordered table-hover w-100">

                        <thead class="thead-light">
                            <tr>
                                <th width="50">No</th>
                                <th>Nama Kelas</th>
                                <th width="150" class="text-center">Jumlah Siswa</th>
                                <th width="200" class="text-center">Aksi</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach($classes as $index => $class)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>
                                        <strong>{{ $class->name }}</strong>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge badge-info">
                                            {{ $class->students_count }} Siswa
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <a href="{{ route('simulator.class.show', $class->id) }}"
                                           class="btn btn-sm btn-primary">
                                            <i class="fas fa-fingerprint"></i>
                                            Pilih Kelas
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
</section>
@endsection

@push('script')
<script src="/admin/plugins/datatables/jquery.dataTables.min.js"></script>
<script src="/admin/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
<script src="/admin/plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>

<script>
$(function () {
    $('#classTable').DataTable({
        responsive: true,
        autoWidth: false,
        pageLength: 10,
        lengthChange: true,
        language: {
            search: "Cari Kelas:",
            lengthMenu: "Tampilkan _MENU_ data",
            info: "Menampilkan _START_ - _END_ dari _TOTAL_ kelas",
            paginate: {
                previous: "Sebelumnya",
                next: "Berikutnya"
            }
        }
    });
});
</script>
@endpush
