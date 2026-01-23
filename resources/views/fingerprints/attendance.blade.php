@extends('layouts.master')

@section('title', 'Absensi Realtime')

@php
  use App\Models\SystemSetting;
  $mode = SystemSetting::get('fingerprint_mode');
@endphp

@section('content')
<div class="container-fluid">

  {{-- ================= MODE WARNING ================= --}}
  @if($mode !== 'attendance')
    <div class="alert alert-warning d-flex align-items-center">
      <i class="fas fa-exclamation-triangle mr-2"></i>
      <div>
        <strong>Mode Registrasi Aktif</strong><br>
        Halaman ini hanya digunakan untuk <b>absensi realtime</b>.
        Silakan aktifkan <b>Mode Absensi</b>.
      </div>
    </div>
  @endif

  {{-- ================= HEADER ================= --}}
  <div class="card card-outline card-success mb-3">
    <div class="card-body d-flex justify-content-between align-items-center">

      <div>
        <h4 class="mb-1">
          <i class="fas fa-clock mr-1"></i>
          Absensi Fingerprint
        </h4>
        <small class="text-muted">
          Monitoring kehadiran siswa secara realtime
        </small>
      </div>

      <div class="text-right">
        <form method="POST" action="{{ route('device.fingerprint.mode') }}">
          @csrf
          <div class="btn-group">
            <button type="submit"
                    name="mode"
                    value="register"
                    class="btn btn-sm btn-warning"
                    {{ $mode === 'register' ? 'disabled' : '' }}>
              <i class="fas fa-user-plus mr-1"></i>
              Registrasi
            </button>

            <button type="submit"
                    name="mode"
                    value="attendance"
                    class="btn btn-sm btn-success"
                    {{ $mode === 'attendance' ? 'disabled' : '' }}>
              <i class="fas fa-clock mr-1"></i>
              Absensi
            </button>
          </div>
        </form>

        <small class="d-block mt-2 text-muted">
          Mode aktif:
          <strong class="text-uppercase">{{ $mode }}</strong>
        </small>
      </div>

    </div>
  </div>

  {{-- ================= MAIN PANEL ================= --}}
  <div class="row {{ $mode !== 'attendance' ? 'opacity-50 pointer-events-none' : '' }}">
    <div class="col-md-12">
      <div class="card card-outline card-dark shadow-sm">
        <div class="card-header">
          <strong>
            <i class="fas fa-satellite-dish mr-1"></i>
            Scan Masuk Kelas
          </strong>
        </div>

        <div class="card-body text-center">

          {{-- ALERT --}}
          <div id="alertBox"></div>

          {{-- WAITING --}}
          <div id="waitingBox">
            <i class="fas fa-fingerprint fa-4x text-secondary mb-3"></i>
            <h5 class="mb-1">Menunggu Scan Fingerprint</h5>
            <small class="text-muted">
              Silakan siswa scan jari pada device fingerprint
            </small>
          </div>

          {{-- RESULT --}}
          <div id="resultBox" class="d-none">
            <i id="statusIcon" class="fas fa-check-circle fa-4x mb-3"></i>

            <h5 id="studentName" class="mb-1"></h5>

            <p class="mb-1">
              Jam Scan:
              <strong id="scanTime"></strong>
            </p>

            <span id="statusBadge" class="badge px-3 py-2"></span>
          </div>

        </div>
      </div>
    </div>
  </div>

</div>
@endsection

@push('script')
@if($mode === 'attendance')
<script>
let lastAttendanceId = null;

const alertBox    = document.getElementById('alertBox');
const waitingBox  = document.getElementById('waitingBox');
const resultBox   = document.getElementById('resultBox');

const studentName = document.getElementById('studentName');
const scanTime    = document.getElementById('scanTime');
const statusBadge = document.getElementById('statusBadge');
const statusIcon  = document.getElementById('statusIcon');

function showAlert(type, message) {
  alertBox.innerHTML = `
    <div class="alert alert-${type}">
      ${message}
    </div>
  `;
}

function resetUI() {
  alertBox.innerHTML = '';
  resultBox.classList.add('d-none');
  waitingBox.classList.remove('d-none');
}

function renderAttendance(data) {
  waitingBox.classList.add('d-none');
  resultBox.classList.remove('d-none');

  studentName.innerText = data.name ?? '-';
  scanTime.innerText    = data.scan_time ?? data.time_in ?? '-';

  if (data.status === 'hadir') {
    statusBadge.className = 'badge badge-success px-3 py-2';
    statusBadge.innerText = 'HADIR';
    statusIcon.className  = 'fas fa-check-circle fa-4x text-success mb-3';
    showAlert('success', '✅ <strong>Absensi berhasil</strong>');
  }
  else if (data.status === 'terlambat') {
    statusBadge.className = 'badge badge-danger px-3 py-2';
    statusBadge.innerText = 'TERLAMBAT';
    statusIcon.className  = 'fas fa-exclamation-circle fa-4x text-danger mb-3';
    showAlert('success', '⏰ <strong>Absensi tercatat (terlambat)</strong>');
  }
  else if (data.status === 'already') {
    statusBadge.className = 'badge badge-warning px-3 py-2';
    statusBadge.innerText = 'SUDAH ABSEN';
    statusIcon.className  = 'fas fa-info-circle fa-4x text-warning mb-3';
    showAlert('warning', '⚠️ <strong>Siswa sudah melakukan absensi</strong>');
  }
  else {
    showAlert('danger', '❌ <strong>Fingerprint tidak dikenali</strong>');
  }

  setTimeout(resetUI, 3500);
}

async function pollAttendance() {
  try {
    const res = await fetch('/api/attendance/last', { cache: 'no-store' });
    if (!res.ok) return;

    const data = await res.json();
    if (!data || !data.id) return;

    if (lastAttendanceId === null) {
      lastAttendanceId = data.id;
      return;
    }

    if (data.id === lastAttendanceId) return;

    lastAttendanceId = data.id;
    renderAttendance(data);

  } catch (err) {
    console.error('Realtime polling error:', err);
  }
}

setInterval(pollAttendance, 1200);
</script>
@endif
@endpush
