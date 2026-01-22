@extends('layouts.master')

@section('title', 'Absensi Realtime')

@php
  use App\Models\SystemSetting;
  $mode = SystemSetting::get('fingerprint_mode');
@endphp

@section('content')
<div class="container-fluid">

  {{-- ================= MODE GUARD ================= --}}
  @if($mode !== 'attendance')
    <div class="alert alert-warning">
      <strong>MODE REGISTRASI AKTIF</strong><br>
      Halaman ini hanya digunakan untuk <b>absensi realtime</b>.<br>
      Silakan aktifkan <b>Mode Absensi</b>.
    </div>
  @endif

  {{-- ================= HEADER ================= --}}
  <div class="card mb-3">
    <div class="card-body d-flex justify-content-between align-items-center">
      <div>
        <h4 class="mb-0">Absensi Fingerprint</h4>
        <small class="text-muted">
          Monitoring kehadiran siswa secara realtime
        </small>
      </div>

      <span class="badge badge-success px-3 py-2">
        MODE: ATTENDANCE
      </span>
    </div>
  </div>

  {{-- ================= MAIN PANEL ================= --}}
  <div class="card shadow-sm">
    <div class="card-header bg-dark text-white">
      <strong>📡 Scan Masuk Kelas</strong>
    </div>

    <div class="card-body text-center">

      {{-- ALERT --}}
      <div id="alertBox"></div>

      {{-- WAITING --}}
      <div id="waitingBox">
        <i class="fas fa-fingerprint fa-4x text-secondary mb-3"></i>
        <h5>Menunggu Scan Fingerprint</h5>
        <small class="text-muted">
          Silakan siswa scan jari di device
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
@endsection

@push('script')
@if($mode === 'attendance')
<script>
/**
 * ===============================
 * REALTIME STATE
 * ===============================
 */
let lastUpdatedAt = null;

const alertBox    = document.getElementById('alertBox');
const waitingBox  = document.getElementById('waitingBox');
const resultBox   = document.getElementById('resultBox');

const studentName = document.getElementById('studentName');
const scanTime    = document.getElementById('scanTime');
const statusBadge = document.getElementById('statusBadge');
const statusIcon  = document.getElementById('statusIcon');

/**
 * ===============================
 * UI HELPERS
 * ===============================
 */
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
  scanTime.innerText    = data.updated_at ?? data.time_in ?? '-';

  if (data.status === 'hadir') {
    statusBadge.className = 'badge badge-success px-3 py-2';
    statusBadge.innerText = 'HADIR';
    statusIcon.className  = 'fas fa-check-circle fa-4x text-success mb-3';

    showAlert('success', '✅ <strong>Berhasil melakukan absensi</strong>');
  } else if (data.status === 'terlambat') {
    statusBadge.className = 'badge badge-danger px-3 py-2';
    statusBadge.innerText = 'TERLAMBAT';
    statusIcon.className  = 'fas fa-exclamation-circle fa-4x text-danger mb-3';

    showAlert('success', '⏰ <strong>Absen tercatat (terlambat)</strong>');
  } else {
    // fallback (jika backend kirim status lain)
    statusBadge.className = 'badge badge-warning px-3 py-2';
    statusBadge.innerText = data.status ?? 'INFO';
    statusIcon.className  = 'fas fa-info-circle fa-4x text-warning mb-3';

    showAlert('warning', '⚠️ <strong>Siswa ini sudah melakukan absensi</strong>');
  }

  // reset otomatis → siap scan berikutnya
  setTimeout(resetUI, 3500);
}

/**
 * ===============================
 * POLLING REALTIME (UPDATED_AT BASED)
 * ===============================
 */
async function pollAttendance() {
  try {
    const res = await fetch('/api/attendance/last', {
      cache: 'no-store'
    });

    if (!res.ok) return;

    const data = await res.json();
    if (!data || !data.updated_at) return;

    // 🔑 kunci realtime: updated_at
    if (data.updated_at === lastUpdatedAt) return;

    lastUpdatedAt = data.updated_at;
    console.log('[REALTIME]', data);

    renderAttendance(data);

  } catch (err) {
    console.error('Realtime polling error:', err);
  }
}

// polling stabil
setInterval(pollAttendance, 1200);
</script>
@endif
@endpush
