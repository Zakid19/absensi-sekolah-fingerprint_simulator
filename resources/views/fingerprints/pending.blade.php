@extends('layouts.master')

@section('title', 'Registrasi Fingerprint')

@php
  use App\Models\SystemSetting;
  $mode = SystemSetting::get('fingerprint_mode');

  // 🔒 fingerprint yang SUDAH dipakai siswa
  $usedFingerprints = $students
      ->whereNotNull('fingerprint_id')
      ->pluck('fingerprint_id')
      ->values();
@endphp

@section('content')
<div class="container-fluid">

  {{-- ================= MODE WARNING ================= --}}
  @if($mode !== 'register')
    <div class="alert alert-warning d-flex align-items-center">
      <i class="fas fa-exclamation-triangle mr-2"></i>
      <div>
        <strong>Mode Absensi Aktif</strong><br>
        Halaman ini hanya untuk <b>registrasi fingerprint</b>.
        Silakan buka menu <b>Absensi Realtime</b>.
      </div>
    </div>
  @endif

  {{-- ================= HEADER ================= --}}
  <div class="card card-outline card-primary mb-3">
    <div class="card-body d-flex justify-content-between align-items-center">

      <div>
        <h4 class="mb-1">
          <i class="fas fa-fingerprint mr-1"></i>
          Registrasi Fingerprint
        </h4>
        <small class="text-muted">
          Pendaftaran fingerprint siswa (Mode Register)
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

  {{-- ================= CONTENT ================= --}}
  <div class="row {{ $mode !== 'register' ? 'opacity-50 pointer-events-none' : '' }}">

    {{-- ================= SCAN PANEL ================= --}}
    <div class="col-md-5">
      <div class="card card-outline card-dark h-100">
        <div class="card-header">
          <strong><i class="fas fa-fingerprint mr-1"></i> Scan Fingerprint</strong>
        </div>

        <div class="card-body text-center">

          <div id="alertBox"></div>

          <div id="waitingBox">
            <i class="fas fa-fingerprint fa-4x text-secondary mb-3"></i>
            <h5 class="mb-1">Menunggu Scan</h5>
            <small class="text-muted">
              Hanya fingerprint yang BELUM terdaftar
            </small>
          </div>

          <div id="successBox" class="d-none">
            <i class="fas fa-check-circle fa-4x text-success mb-3"></i>
            <h5 class="mb-1">Fingerprint Baru Terdeteksi</h5>
            <p class="mb-0">
              ID Fingerprint:
              <strong id="fpText"></strong>
            </p>
          </div>

        </div>
      </div>
    </div>

    {{-- ================= MAPPING PANEL ================= --}}
    <div class="col-md-7">
      <div class="card card-outline card-info h-100">
        <div class="card-header">
          <strong><i class="fas fa-link mr-1"></i> Hubungkan ke Data Siswa</strong>
        </div>

        <div class="card-body">

          <form method="POST"
                action="{{ route('device.fingerprints.map') }}">
            @csrf

            <input type="hidden" name="fingerprint_id" id="fpInput">

            <div class="form-group">
              <label>Pilih Siswa</label>
              <select name="student_id"
                      id="studentSelect"
                      class="form-control"
                      disabled>
                <option value="">— Pilih siswa —</option>
                @foreach($students as $student)
                  @if(!$student->fingerprint_id)
                    <option value="{{ $student->id }}">
                      {{ $student->name }}
                    </option>
                  @endif
                @endforeach
              </select>
              <small class="text-muted">
                Hanya siswa yang belum memiliki fingerprint
              </small>
            </div>

            <button type="submit"
                    class="btn btn-primary"
                    id="mapBtn"
                    disabled>
              <i class="fas fa-link mr-1"></i>
              Hubungkan Fingerprint
            </button>
          </form>

          <div class="alert alert-secondary mt-4 mb-0">
            <i class="fas fa-info-circle mr-1"></i>
            Fingerprint yang sudah digunakan
            <strong>tidak akan terdeteksi lagi</strong>.
          </div>

        </div>
      </div>
    </div>

  </div>
</div>
@endsection

@push('script')
@if($mode === 'register')
<script>
/* ===============================
 * STATE
 * =============================== */

// fingerprint yang SUDAH dipakai (hard source of truth)
const usedFingerprints = new Set(@json($usedFingerprints));

let lastEventKey = null;
let isProcessing = false;

/* ===============================
 * ELEMENTS
 * =============================== */
const alertBox      = document.getElementById('alertBox');
const waitingBox    = document.getElementById('waitingBox');
const successBox    = document.getElementById('successBox');
const fpText        = document.getElementById('fpText');
const fpInput       = document.getElementById('fpInput');
const studentSelect = document.getElementById('studentSelect');
const mapBtn        = document.getElementById('mapBtn');

/* ===============================
 * HELPERS
 * =============================== */
function resetUI() {
  alertBox.innerHTML = '';
  waitingBox.classList.remove('d-none');
  successBox.classList.add('d-none');
  studentSelect.disabled = true;
  mapBtn.disabled = true;
}

/* ===============================
 * REALTIME POLLING
 * =============================== */
async function pollFingerprint() {
  if (isProcessing) return;

  try {
    const res = await fetch('/api/fingerprint/last', { cache: 'no-store' });
    if (!res.ok) return;

    const data = await res.json();
    if (!data?.fingerprint_id || !data?.detected_at) return;

    // 🔒 HARD FILTER: fingerprint sudah terdaftar → ABAIKAN
    if (usedFingerprints.has(data.fingerprint_id)) {
      return;
    }

    const eventKey = data.fingerprint_id + data.detected_at;
    if (eventKey === lastEventKey) return;
    lastEventKey = eventKey;

    isProcessing = true;
    resetUI();

    // hanya fingerprint BARU yang lolos
    waitingBox.classList.add('d-none');
    successBox.classList.remove('d-none');

    fpText.innerText = data.fingerprint_id;
    fpInput.value   = data.fingerprint_id;

    studentSelect.disabled = false;
    mapBtn.disabled = false;

    setTimeout(() => {
      isProcessing = false;
    }, 1200);

  } catch (e) {
    console.error('Realtime error', e);
    isProcessing = false;
  }
}

setInterval(pollFingerprint, 1500);
</script>
@endif
@endpush
