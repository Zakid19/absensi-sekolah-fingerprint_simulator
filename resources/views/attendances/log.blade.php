@extends('layouts.master')

@section('title', 'Log Absensi')

@section('content')
<div class="container-fluid">

  {{-- ================= HEADER ================= --}}
  <div class="card card-outline card-secondary mb-3">
    <div class="card-body d-flex justify-content-between align-items-center">

      <div>
        <h4 class="mb-1">
          <i class="fas fa-clipboard-list mr-1"></i>
          Log Scan Fingerprint
        </h4>
        <small class="text-muted">
          Riwayat detail setiap scan fingerprint (debugging & audit)
        </small>
      </div>

      <span class="badge badge-secondary px-3 py-2">
        READ ONLY
      </span>
    </div>
  </div>

  {{-- ================= INFO BOX ================= --}}
  <div class="alert alert-info d-flex align-items-start">
    <i class="fas fa-info-circle mr-2 mt-1"></i>
    <div>
      Halaman ini digunakan untuk <strong>melacak histori scan fingerprint</strong>,
      <strong>bukan</strong> sebagai laporan kehadiran siswa.
      <br>
      Data bersifat <strong>read-only</strong> dan hanya dapat diakses oleh admin.
    </div>
  </div>

  {{-- ================= TABLE ================= --}}
  <div class="card shadow-sm">
    <div class="card-header bg-light">
      <strong>
        <i class="fas fa-database mr-1"></i>
        Data Log Scan
      </strong>
    </div>

    <div class="card-body table-responsive p-0">
      <table class="table table-sm table-hover mb-0">
        <thead class="thead-light">
          <tr>
            <th width="160">Waktu Scan</th>
            <th>Nama Siswa</th>
            <th width="120">Jenis Scan</th>
            <th width="130">Status</th>
            <th>Keterangan Teknis</th>
          </tr>
        </thead>

        <tbody>
          @forelse ($logs as $log)
            <tr>
              <td>
                <small class="text-muted">
                  {{ $log->waktu_scan }}
                </small>
              </td>

              <td>
                <strong>
                  {{ $log->student->name ?? '-' }}
                </strong>
              </td>

              <td>
                <span class="badge badge-info">
                  {{ strtoupper($log->jenis) }}
                </span>
              </td>

              <td>
                @if ($log->status === 'success')
                  <span class="badge badge-success">
                    SUCCESS
                  </span>
                @elseif ($log->status === 'duplicate')
                  <span class="badge badge-warning">
                    DUPLICATE
                  </span>
                @else
                  <span class="badge badge-danger">
                    REJECTED
                  </span>
                @endif
              </td>

              <td>
                <small class="text-muted">
                  {{ $log->keterangan }}
                </small>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="5" class="text-center py-4 text-muted">
                <i class="fas fa-database fa-2x mb-2"></i><br>
                Belum ada data log scan fingerprint
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    {{-- ================= PAGINATION ================= --}}
    <div class="card-footer clearfix">
      {{ $logs->links() }}
    </div>
  </div>

</div>
@endsection
