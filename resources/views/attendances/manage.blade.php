@extends('layouts.master')

@push('style')
<link rel="stylesheet" href="/admin/plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
<link rel="stylesheet" href="/admin/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
<link rel="stylesheet" href="/admin/plugins/datatables-buttons/css/buttons.bootstrap4.min.css">
@endpush

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Riwayat Absen</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="/dashboard">Home</a></li>
                    <li class="breadcrumb-item active">Absen</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">

        {{-- Filter --}}
        <div class="card mb-3">
            <div class="card-body d-flex gap-2 flex-wrap">
                <input type="date" id="filter-date" class="form-control" style="max-width: 200px">

                <select id="filter-class" class="form-control" style="max-width: 200px">
                    <option value="">Semua Kelas</option>
                    @foreach(\App\Models\ClassRoom::all() as $class)
                        <option value="{{ $class->id }}">{{ $class->name }}</option>
                    @endforeach
                </select>

                <input type="number" id="filter-student" placeholder="ID Siswa" class="form-control" style="max-width: 200px">

                <button id="filter-btn" class="btn btn-primary">Filter</button>
            </div>
        </div>

        <div class="row">
            <div class="col-12 col-sm-12">
                <div class="card card-primary card-outline card-outline-tabs">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="dataAttendance" class="table table-striped table-hover">
                                <thead>
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
        </div>

        {{-- Modal --}}
        <div class="modal fade" id="historyModal" tabindex="-1" role="dialog">
            <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
                <div class="modal-content shadow-lg border-0 rounded-lg">

                <div class="modal-header bg-secondary text-white">
                    <div class="d-flex align-items-center">
                        <div class="bg-white text-primary rounded-circle d-flex align-items-center justify-content-center mr-3"
                            style="width:48px;height:48px;font-weight:bold;">
                            <span id="studentInitial">A</span>
                        </div>
                        <div>
                            Riwayat Kehadiran — <span id="studentName">Nama Siswa</span>
                             <small class="opacity-75">Detail absensi</small>
                        </div>
                    </div>
                    <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                </div>

                <div class="modal-body p-0">

                    <div id="historyLoading" class="text-center py-5">
                        <div class="spinner-border text-primary"></div>
                        <div class="mt-2">Memuat riwayat...</div>
                    </div>

                    <div id="historyContent" class="d-none">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                        <thead class="thead-light">
                            <tr>
                            <th>Tanggal</th>
                            <th>Jam</th>
                            <th>Status</th>
                            <th>Keterangan</th>
                            </tr>
                        </thead>
                        <tbody id="historyBody"></tbody>
                        </table>
                    </div>
                    </div>

                    <div id="historyEmpty" class="text-center py-5 d-none text-muted">
                        <i class="fa fa-calendar-times fa-2x mb-2"></i>
                        <div>Belum ada riwayat absensi</div>
                    </div>

                </div>

                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Tutup</button>
                </div>

                </div>
            </div>
        </div>

    </div>
</section>
@endsection

@push('script')
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
<script src="/admin/plugins/datatables/jquery.dataTables.min.js"></script>
<script src="/admin/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
<script src="/admin/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>

<script>
$(document).ready(function () {

    const today = new Date().toISOString().split('T')[0];

    if (!$('#filter-date').val()) {
        $('#filter-date').val(today);
    }

    let table = $('#dataAttendance').DataTable({
        language: { url: 'https://cdn.datatables.net/plug-ins/1.13.4/i18n/id.json' },
        processing: true,
        serverSide: false,
        deferLoading: 0, // ⬅ penting: stop auto-load
        ajax: {
            url: '{{ $table['table_url'] }}',
            data: function (d) {
                d.date = $('#filter-date').val();
                d.class_room_id = $('#filter-class').val();
                d.student_id = $('#filter-student').val();
            }
        },
        ordering: true,
        paging: true,
        autoWidth: true,

        columns: [
            @foreach ($table['columns'] as $column)
                { data: '{{ $column['name'] }}', name: '{{ $column['name'] }}' },
            @endforeach
        ],

        drawCallback: function () {
            $(".delete-form").on("click", function () {
                var form = $(this).parent().find("form");
                Swal.fire({
                    title: 'Are you sure?',
                    text: "Apakah kamu yakin ingin menghapus item ini?",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes!'
                }).then((result) => {
                    if (result.value) {
                        form.submit();
                    }
                })
            });
        }
    });

    // reload pertama setelah date siap
    table.ajax.reload();

    $('#filter-date, #filter-class, #filter-student').on('change', function () {
        table.ajax.reload();
    });

    $('#filter-btn').on('click', function () {
        table.ajax.reload();
    });

});


$(document).on('click', '.btn-history', function () {

    let studentId = $(this).data('id');
    let studentName = $(this).data('name');

    $('#studentName').text(studentName);
    $('#studentInitial').text(studentName.charAt(0).toUpperCase());

    $('#historyLoading').removeClass('d-none');
    $('#historyContent').addClass('d-none');
    $('#historyEmpty').addClass('d-none');
    $('#historyModal').modal('show');

    $.get('/attendance/history/' + studentId, function (data) {

        $('#historyLoading').addClass('d-none');

        if (!data || data.length === 0) {
            $('#historyEmpty').removeClass('d-none');
            return;
        }

        let rows = '';

        data.forEach(row => {

            let badge = row.status === 'TERLAMBAT'
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
</script>
@endpush
