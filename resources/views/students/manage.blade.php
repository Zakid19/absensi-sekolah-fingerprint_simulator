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
                    Manajemen Siswa
                    @if($selected_class)
                        <span class="badge badge-info ml-2">
                            {{ $selected_class->name }}
                        </span>
                    @endif
                </h1>
                <small class="text-muted">
                    Kelola data siswa berdasarkan kelas
                </small>
            </div>
            <div class="col-sm-4">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item">
                        <a href="{{ route('dashboard') }}">Home</a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="{{ route('class.index') }}">Kelas</a>
                    </li>
                    <li class="breadcrumb-item active">Siswa</li>
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
                                <i class="fas fa-users mr-1"></i>
                                Daftar Siswa
                            </h3>

                            <div>
                                <a href="{{ route('class.index') }}"
                                   class="btn btn-sm btn-secondary">
                                    <i class="fas fa-arrow-left"></i> Pilih Kelas
                                </a>

                                <a href="{{ route('student.create', ['class_room_id' => $selected_class]) }}"
                                   class="btn btn-sm btn-primary">
                                    <i class="fas fa-plus"></i> Tambah Siswa
                                </a>
                            </div>
                        </div>
                    </div>

                    {{-- Card Body --}}
                    <div class="card-body">

                        <div class="table-responsive">
                            <table id="dataStudent"
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
    $('#dataStudent').DataTable({
        language: {
            url: 'https://cdn.datatables.net/plug-ins/1.13.4/i18n/id.json',
        },
        processing: true,
        serverSide: false,
        ajax: '{{ $table['table_url'] }}',
        ordering: true,
        paging: true,
        autoWidth: false,

        columns: [
            @foreach ($table['columns'] as $column)
            {
                data: '{{ $column['name'] }}',
                name: '{{ $column['name'] }}'
            },
            @endforeach
        ],

        drawCallback: function () {
            $('.delete-form').off('click').on('click', function () {
                let form = $(this).closest('td').find('form');

                Swal.fire({
                    title: 'Konfirmasi',
                    text: 'Yakin ingin menghapus siswa ini?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Hapus',
                    cancelButtonText: 'Batal',
                    confirmButtonColor: '#d33'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        }
    });
});
</script>
@endpush
