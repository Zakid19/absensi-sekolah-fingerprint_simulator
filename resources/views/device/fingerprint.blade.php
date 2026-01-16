@extends('layouts.master')

@push('style')
<link rel="stylesheet" href="/admin/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
<link rel="stylesheet" href="/admin/plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
@endpush

@section('content')

<div class="container-fluid">

  <a href="/device/fingerprint" class="btn btn-secondary btn-sm mb-3">
    <i class="fas fa-arrow-left"></i> Kembali
  </a>

  <div class="card card-outline card-primary">
    <div class="card-header">
      <h3 class="card-title">
        <i class="fas fa-users mr-1"></i> {{ $class->name }}
      </h3>
    </div>

    <div class="card-body">

      @if(session('success'))
      <div class="alert alert-success alert-dismissible fade show">
        {{ session('success') }}
        <button class="close" data-dismiss="alert">&times;</button>
      </div>
      @endif

      @if(session('error'))
      <div class="alert alert-danger alert-dismissible fade show">
        {{ session('error') }}
        <button class="close" data-dismiss="alert">&times;</button>
      </div>
      @endif

      <table id="studentTable" class="table table-bordered table-striped table-hover">
        <thead class="thead-light">
          <tr>
            <th width="5%">#</th>
            <th>Nama</th>
            <th>NIS</th>
            <th width="15%">Fingerprint</th>
            <th width="15%">Status</th>
            <th width="20%" class="text-center">Aksi</th>
          </tr>
        </thead>
        <tbody>
          @foreach($class->students as $i => $s)
          <tr>
            <td>{{ $i + 1 }}</td>
            <td>{{ $s->name }}</td>
            <td>{{ $s->nis }}</td>
            <td>{{ $s->fingerprint_id ?? '-' }}</td>
            <td>
              @if($s->fingerprint_id)
                <span class="badge badge-success"><i class="fas fa-check"></i> Registered</span>
              @else
                <span class="badge badge-secondary">Belum</span>
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

          {{-- MODAL --}}
          <div class="modal fade" id="fingerModal{{ $s->id }}" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">

              <div class="modal-content text-center">

                {{-- Header --}}
                <div class="modal-header bg-dark text-white">
                  <h5 class="modal-title w-100">
                    <i class="fas fa-fingerprint"></i>
                    {{ $s->fingerprint_id ? 'Edit Fingerprint' : 'Register Fingerprint' }}
                  </h5>
                  <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                </div>

                {{-- FORM REGISTER --}}
                <form method="POST" action="{{ route('device.fingerprint.register') }}">
                  @csrf
                  <input type="hidden" name="student_id" value="{{ $s->id }}">

                  <div class="modal-body">

                    @if($errors->any() && old('student_id') == $s->id)
                      <div class="alert alert-danger">
                        {{ $errors->first('fingerprint_id') }}
                      </div>
                    @endif

                    <div class="mb-3">
                      <i class="fas fa-hand-point-up fa-4x text-secondary"></i>
                      <p class="mt-2 mb-1"><strong>Tempelkan jari</strong></p>
                      <small class="text-muted">Masukkan Fingerprint ID lalu simpan</small>
                    </div>

                    <input type="text"
                           name="fingerprint_id"
                           class="form-control form-control-lg text-center"
                           placeholder="fp001"
                           value="{{ old('fingerprint_id', $s->fingerprint_id) }}"
                           required>

                  </div>

                  <div class="modal-footer justify-content-center">
                    <button type="submit" class="btn btn-success btn-lg">
                      <i class="fas fa-save"></i> Simpan
                    </button>
                  </div>
                </form>

                {{-- FORM CLEAR --}}
                @if($s->fingerprint_id)
                  <div class="pb-3">
                    <form action="{{ route('device.fingerprint.clear', $s->id) }}"
                          method="POST"
                          onsubmit="return confirm('Yakin mau menghapus fingerprint ini?')">
                      @csrf
                      @method('DELETE')
                      <button type="submit" class="btn btn-danger btn-sm">
                        <i class="fas fa-trash"></i> Clear Fingerprint
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

@if ($errors->any())
<script>
  $(function () {
    $('#fingerModal{{ old("student_id") }}').modal('show');
  });
</script>
@endif
@endpush
