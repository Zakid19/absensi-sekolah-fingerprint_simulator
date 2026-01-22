@extends('layouts.master')

@section('title', 'Registrasi Fingerprint')

@php
  use App\Models\SystemSetting;
  $mode = SystemSetting::get('fingerprint_mode');
@endphp

@section('content')
<div class="container-fluid">

  {{-- ================= MODE WARNING ================= --}}
  @if($mode !== 'register')
    <div class="alert alert-warning">
      <strong>MODE ABSENSI AKTIF</strong><br>
      Halaman ini hanya digunakan untuk <b>registrasi fingerprint</b>.<br>
      Silakan buka menu <b>Absensi Realtime</b>.
    </div>
  @endif

  {{-- ================= HEADER ================= --}}
  <div class="card mb-3">
    <div class="card-body d-flex justify-content-between align-items-center">

      <div>
        <h4 class="mb-0">Registrasi Fingerprint</h4>
        <small class="text-muted">
          Pendaftaran fingerprint siswa (mode register)
        </small>
      </div>

      <div class="text-right">
        <form method="POST" action="{{ route('fingerprint.mode') }}">
          @csrf
          <div class="btn-group">
            <button type="submit"
                    name="mode"
                    value="register"
                    class="btn btn-sm btn-warning"
                    {{ $mode === 'register' ? 'disabled' : '' }}>
              MODE REGISTRASI
            </button>

            <button type="submit"
                    name="mode"
                    value="attendance"
                    class="btn btn-sm btn-success"
                    {{ $mode === 'attendance' ? 'disabled' : '' }}>
              MODE ABSENSI
            </button>
          </div>
        </form>

        <small class="d-block mt-2 text-muted">
          Mode saat ini:
          <strong>{{ strtoupper($mode) }}</strong>
        </small>
      </div>

    </div>
  </div>

  {{-- ================= FLASH ================= --}}
  @if(session('success'))
    <div class="alert alert-success">
      {{ session('success') }}
    </div>
  @endif

  {{-- ================= CONTENT ================= --}}
  <div class="row {{ $mode !== 'register' ? 'opacity-50 pointer-events-none' : '' }}">

    {{-- ================= SCAN PANEL ================= --}}
    <div class="col-md-5">
      <div class="card shadow-sm">
        <div class="card-header bg-dark text-white">
          <strong>🖐 Scan Fingerprint</strong>
        </div>

        <div class="card-body text-center">

          <div id="alertBox"></div>

          <div id="waitingBox">
            <i class="fas fa-fingerprint fa-4x text-secondary mb-3"></i>
            <h5>Menunggu Scan</h5>
            <small class="text-muted">
              Silakan siswa scan jari di device
            </small>
          </div>

          <div id="successBox" class="d-none">
            <i class="fas fa-check-circle fa-4x text-success mb-3"></i>
            <h5>Fingerprint Terdeteksi</h5>
            <p class="mb-0">
              ID: <strong id="fpText"></strong>
            </p>
          </div>

        </div>
      </div>
    </div>

    {{-- ================= MAPPING PANEL ================= --}}
    <div class="col-md-7">
      <div class="card shadow-sm">
        <div class="card-header bg-light">
          <strong>🔗 Hubungkan ke Siswa</strong>
        </div>

        <div class="card-body">

          <form method="POST"
                action="{{ route('fingerprints.map') }}"
                id="mappingForm">
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
            </div>

            <button type="submit"
                    class="btn btn-primary"
                    id="mapBtn"
                    disabled>
              <i class="fas fa-link"></i>
              Hubungkan Fingerprint
            </button>
          </form>

          <div class="alert alert-secondary mt-3 mb-0">
            ⚠️ Pastikan siswa yang dipilih adalah siswa
            yang <b>barusan melakukan scan</b>
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
let lastId = null;

const alertBox      = document.getElementById('alertBox');
const waitingBox    = document.getElementById('waitingBox');
const successBox    = document.getElementById('successBox');
const fpText        = document.getElementById('fpText');
const fpInput       = document.getElementById('fpInput');
const studentSelect = document.getElementById('studentSelect');
const mapBtn        = document.getElementById('mapBtn');

function resetUI() {
  alertBox.innerHTML = '';
  waitingBox.classList.remove('d-none');
  successBox.classList.add('d-none');
  studentSelect.disabled = true;
  mapBtn.disabled = true;
}

function showAlert(type, message) {
  alertBox.innerHTML = `
    <div class="alert alert-${type}">
      ${message}
    </div>
  `;
}

async function pollFingerprint() {
  try {
    const res = await fetch('/api/fingerprint/last', { cache: 'no-store' });
    if (!res.ok) return;

    const data = await res.json();
    if (!data.id) return;

    if (lastId === null) {
      lastId = data.id;
      return;
    }

    if (data.id === lastId) return;
    lastId = data.id;

    resetUI();

    if (data.status === 'new') {
      waitingBox.classList.add('d-none');
      successBox.classList.remove('d-none');

      fpText.innerText = data.fingerprint_id;
      fpInput.value   = data.fingerprint_id;

      studentSelect.disabled = false;
      mapBtn.disabled = false;

    } else if (data.status === 'duplicate') {
      showAlert('danger', '❌ Fingerprint sudah pernah discan sebelumnya');
    } else if (data.status === 'mapped') {
      showAlert('warning', '⚠️ Fingerprint sudah digunakan siswa lain');
    }

  } catch (e) {
    console.error('Realtime error', e);
  }
}

setInterval(pollFingerprint, 2000);
</script>
@endif
@endpush
