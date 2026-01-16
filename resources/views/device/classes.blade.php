@extends('layouts.master')

@push('style')
<link rel="stylesheet" href="/admin/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
<link rel="stylesheet" href="/admin/plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
@endpush

@section('content')
<div class="container-fluid">
  <div class="card card-outline card-primary">
    <div class="card-header">
      <h3 class="card-title">
        <i class="fas fa-school mr-1"></i> Daftar Kelas
      </h3>
    </div>

    <div class="card-body">
      <table id="classTable" class="table table-bordered table-striped table-hover">
        <thead class="thead-light">
          <tr>
            <th width="5%">#</th>
            <th>Nama Kelas</th>
            <th width="15%">Jumlah Siswa</th>
            <th width="15%" class="text-center">Aksi</th>
          </tr>
        </thead>
        <tbody>
          @foreach($classes as $index => $class)
          <tr>
            <td>{{ $index + 1 }}</td>
            <td>{{ $class->name }}</td>
            <td>{{ $class->students_count }}</td>
            <td class="text-center">
              <a href="{{ route('device.class.show', $class->id) }}" class="btn btn-sm btn-primary">
                <i class="fas fa-eye"></i> Detail
              </a>
            </td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>
</div>
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
    lengthChange: true,
    pageLength: 10,
    language: {
      search: "Cari:",
      lengthMenu: "Tampilkan _MENU_ data",
      info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
      paginate: {
        previous: "Sebelumnya",
        next: "Berikutnya"
      }
    }
  });
});
</script>
@endpush
