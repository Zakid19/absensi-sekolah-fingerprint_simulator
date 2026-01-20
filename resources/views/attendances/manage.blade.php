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
                <h1 class="m-0 font-weight-bold">Riwayat Absensi</h1>
                <small class="text-muted">
                    Monitoring kehadiran siswa berdasarkan tanggal dan kelas
                </small>
            </div>
            <div class="col-sm-5">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item active">Absensi</li>
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
                    <label class="text-muted small">Tanggal</label>
                    <input type="date" id="filter-date" class="form-control">
                </div>

                <div class="col-md-3 col-sm-6 mb-2">
                    <label class="text-muted small">Kelas</label>
                    <select id="filter-class" class="form-control">
                        <option value="">Semua Kelas</option>
                        @foreach(\App\Models\ClassRoom::all() as $class)
                            <option value="{{ $class->id }}">{{ $class->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3 col-sm-6 mb-2">
                    <label class="text-muted small">Nama Siswa</label>
                    <input type="text" id="filter-student" class="form-control"
                           placeholder="Siswa">
                </div>

                <div class="col-md-3 col-sm-6 mb-2 text-muted small">
                    Filter akan diterapkan otomatis
                </div>

            </div>
        </div>
    </div>

    {{-- ================= TABLE ================= --}}
    <div class="card card-primary card-outline">
        <div class="card-body">
            <div class="table-responsive">
                <table id="dataAttendance" class="table table-bordered table-hover w-100">
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

{{-- ================= MODAL DETAIL ================= --}}
<div class="modal fade" id="historyModal" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content shadow border-0">

            {{-- Header --}}
            <div class="modal-header bg-secondary text-white">
                <div class="d-flex align-items-center">
                    <div class="bg-white text-primary rounded-circle d-flex align-items-center justify-content-center mr-3"
                         style="width:48px;height:48px;font-weight:bold;">
                        <span id="studentInitial">A</span>
                    </div>
                    <div>
                        <div class="font-weight-bold" id="studentName">Siswa</div>
                        <small class="opacity-75">Detail riwayat absensi</small>
                    </div>
                </div>
                <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
            </div>

            {{-- Body --}}
            <div class="modal-body p-0">

                {{-- Loading --}}
                <div id="historyLoading" class="text-center py-5">
                    <div class="spinner-border text-primary"></div>
                    <div class="mt-2">Memuat riwayat...</div>
                </div>

                {{-- Content --}}
                <div id="historyContent" class="d-none">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="thead-light">
                                <tr>
                                    <th>Tanggal</th>
                                    <th>Jam Masuk</th>
                                    <th>Status</th>
                                    <th>Keterangan</th>
                                </tr>
                            </thead>
                            <tbody id="historyBody"></tbody>
                        </table>
                    </div>
                </div>

                {{-- Empty --}}
                <div id="historyEmpty" class="text-center py-5 d-none text-muted">
                    <i class="fa fa-calendar-times fa-2x mb-2"></i>
                    <div>Belum ada data absensi</div>
                </div>

            </div>

            <div class="modal-footer bg-light">
                <button class="btn btn-secondary btn-sm" data-dismiss="modal">
                    Tutup
                </button>
            </div>

        </div>
    </div>
</div>
@endsection

@push('script')
<script src="/admin/plugins/datatables/jquery.dataTables.min.js"></script>
<script src="/admin/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>

<script>
$(function () {

    // Default tanggal hari ini
    $('#filter-date').val(new Date().toISOString().split('T')[0]);

    // DataTable
    const table = $('#dataAttendance').DataTable({
        language: { url: 'https://cdn.datatables.net/plug-ins/1.13.4/i18n/id.json' },
        processing: true,
        serverSide: false,
        paging: true,
        ordering: true,
        searching: false,
        autoWidth: false,

        ajax: {
            url: '{{ $table['table_url'] }}',
            data: d => {
                d.date = $('#filter-date').val();
                d.class_room_id = $('#filter-class').val();
                // d.student_id = $('#filter-student').val();
                d.siswa_name = $('#filter-student').val();
            }
        },

        columns: [
            @foreach ($table['columns'] as $column)
                { data: '{{ $column['name'] }}', name: '{{ $column['name'] }}' },
            @endforeach
        ]
    });

    // Auto filter
    $('#filter-date, #filter-class, #filter-student')
        .on('change keyup', () => table.ajax.reload());

    // Detail modal
    $(document).on('click', '.btn-history', function () {

        const studentId   = $(this).data('id');
        const studentName = $(this).data('name') || '-';

        $('#studentName').text(studentName);
        $('#studentInitial').text(studentName.charAt(0).toUpperCase());

        $('#historyBody').html('');
        $('#historyLoading').removeClass('d-none');
        $('#historyContent, #historyEmpty').addClass('d-none');

        $('#historyModal').modal('show');

        $.get('/attendance/history/' + studentId, function (data) {

            $('#historyLoading').addClass('d-none');

            if (!data || data.length === 0) {
                $('#historyEmpty').removeClass('d-none');
                return;
            }

            let rows = '';

            data.forEach(row => {
                const badge = row.is_late
                    ? '<span class="badge badge-danger">Terlambat</span>'
                    : '<span class="badge badge-success">Hadir</span>';

                rows += `
                    <tr>
                        <td>${row.date}</td>
                        <td>${row.time_in ?? '-'}</td>
                        <td>${badge}</td>
                        <td>${row.is_late ? 'Datang melewati jam masuk' : '-'}</td>
                    </tr>
                `;
            });

            $('#historyBody').html(rows);
            $('#historyContent').removeClass('d-none');
        });
    });

});
</script>
@endpush
