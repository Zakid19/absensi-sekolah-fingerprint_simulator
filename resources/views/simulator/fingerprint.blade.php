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
          <li class="breadcrumb-item"><a href="{{ route('simulator.fingerprint.index') }}">Fingerprint</a></li>
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
  <a href="{{ route('simulator.fingerprint.index') }}" class="btn btn-secondary btn-sm mb-3">
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
              <th width="130">Status</th>
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
                  <span class="badge badge-success">
                    <i class="fas fa-check"></i> Registered
                  </span>
                @else
                  <span class="badge badge-secondary">
                    Belum
                  </span>
                @endif
              </td>
              <td class="text-center">
                <button class="btn btn-sm {{ $s->fingerprint_id ? 'btn-warning' : 'btn-primary' }}"
                        data-toggle="modal"
                        data-target="#fingerModal{{ $s->id }}">
                  <i class="fas fa-fingerprint"></i>
                  {{ $s->fingerprint_id ? 'Edit Finger' : 'Register' }}
                </button>
              </td>
            </tr>

            {{-- ================= MODAL FINGERPRINT ================= --}}
            <div class="modal fade" id="fingerModal{{ $s->id }}" tabindex="-1" aria-hidden="true">
              <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow">

                  {{-- HEADER --}}
                  <div class="modal-header bg-dark text-white">
                    <h5 class="modal-title">
                      <i class="fas fa-fingerprint mr-1"></i>
                      {{ $s->fingerprint_id ? 'Edit Fingerprint' : 'Register Fingerprint' }}
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal">
                      &times;
                    </button>
                  </div>

                  {{-- BODY --}}
                  <div class="modal-body text-center">

                    {{-- STUDENT INFO --}}
                    <div class="mb-3">
                      <div class="font-weight-bold">{{ $s->name }}</div>
                      <small class="text-muted">NIS: {{ $s->nis }}</small>
                    </div>

                    {{-- ERROR --}}
                    @if($errors->any() && old('student_id') == $s->id)
                      <div class="alert alert-danger">
                        {{ $errors->first('fingerprint_id') }}
                      </div>
                    @endif

                    {{-- ICON --}}
                    <div class="mb-3">
                      <i class="fas fa-hand-point-up fa-4x text-secondary"></i>
                      <p class="mt-2 mb-1">
                        <strong>Masukkan ID Fingerprint</strong>
                      </p>
                      <small class="text-muted">
                        ID harus sesuai dengan yang terdaftar di mesin fingerprint
                      </small>
                    </div>

                    {{-- FORM REGISTER / EDIT --}}
                    <form method="POST" action="{{ route('simulator.fingerprint.register') }}">
                      @csrf
                      <input type="hidden" name="student_id" value="{{ $s->id }}">

                      <input type="text"
                            name="fingerprint_id"
                            class="form-control form-control-lg text-center mb-3"
                            placeholder="Contoh: fp001"
                            value="{{ old('fingerprint_id', $s->fingerprint_id) }}"
                            required>

                      <button type="submit" class="btn btn-success btn-lg btn-block">
                        <i class="fas fa-save mr-1"></i>
                        {{ $s->fingerprint_id ? 'Simpan Perubahan' : 'Simpan Fingerprint' }}
                      </button>
                    </form>

                  </div>

                  {{-- FOOTER / CLEAR --}}
                  @if($s->fingerprint_id)
                  <div class="modal-footer justify-content-center bg-light">
                    <form action="{{ route('simulator.fingerprint.clear', $s->id) }}"
                          method="POST">
                      @csrf
                      @method('DELETE')

                      <button type="submit" class="btn btn-outline-danger btn-sm">
                        <i class="fas fa-trash mr-1"></i>
                        Clear Fingerprint
                      </button>
                    </form>
                  </div>
                  @endif

                </div>
              </div>
            </div>

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
