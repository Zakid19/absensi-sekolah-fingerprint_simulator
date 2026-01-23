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
          <span class="badge badge-info ml-2">{{ $class->name }}</span>
        </h1>
        <small class="text-muted">
          Langkah 2: Pilih siswa dan daftarkan fingerprint
        </small>
      </div>
      <div class="col-sm-4">
        <ol class="breadcrumb float-sm-right">
          <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
          <li class="breadcrumb-item">
              <a href="{{ url()->previous() }}">Fingerprint</a>
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

  {{-- Back --}}
  <a href="{{ url()->previous() }}" class="btn btn-secondary btn-sm mb-3">
    <i class="fas fa-arrow-left"></i> Kembali Pilih Kelas
  </a>

  <div class="card card-primary card-outline">

    {{-- Card Header --}}
    <div class="card-header">
      <h3 class="card-title">
        <i class="fas fa-users mr-1"></i>
        Daftar Siswa
      </h3>
    </div>

    <div class="card-body">

      {{-- Alerts --}}
      @if(session('success'))
        <div class="alert alert-success alert-dismissible">
          <i class="fas fa-check-circle"></i> {{ session('success') }}
          <button class="close" data-dismiss="alert">&times;</button>
        </div>
      @endif

      @if(session('error'))
        <div class="alert alert-danger alert-dismissible">
          <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
          <button class="close" data-dismiss="alert">&times;</button>
        </div>
      @endif

      {{-- Table --}}
      <div class="table-responsive">
        <table id="studentTable" class="table table-bordered table-hover w-100">

          <thead class="thead-light">
            <tr>
              <th width="50">No</th>
              <th>Nama</th>
              <th>NIS</th>
              <th width="120">Fingerprint ID</th>
              <th width="180" class="text-center">Aksi</th>
            </tr>
          </thead>

          <tbody>
          @foreach($class->students as $i => $s)
            <tr>
              <td>{{ $i + 1 }}</td>
              <td><strong>{{ $s->name }}</strong></td>
              <td>{{ $s->nis }}</td>
              <td class="text-center">
                {{ $s->fingerprint_id ?? '-' }}
              </td>
              <td>
                @if($s->fingerprint_id)
                  <form method="POST"
                        action="{{ route('device.students.resetFingerprint', $s) }}"
                        onsubmit="return confirmReset('{{ $s->name }}')"
                        class="d-inline">
                    @csrf

                    <button class="btn btn-sm btn-danger">
                      <i class="fas fa-undo mr-1"></i>
                      Reset Fingerprint
                    </button>
                  </form>
                @else
                  <span class="text-muted">
                    —
                  </span>
                @endif
              </td>
            </tr>

          @endforeach
          </tbody>

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
<script src="/admin/plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>

<script>
$(function () {
  $('#studentTable').DataTable({
    responsive: true,
    autoWidth: false,
    pageLength: 10,
    language: {
      search: "Cari Siswa:",
      lengthMenu: "Tampilkan _MENU_ data",
      info: "Menampilkan _START_ - _END_ dari _TOTAL_ siswa",
      paginate: {
        previous: "Sebelumnya",
        next: "Berikutnya"
      }
    }
  });
});
</script>

@if ($errors->any())
<script>
$(function () {
  $('#fingerModal{{ old("student_id") }}').modal('show');
});
</script>
@endif
@endpush
